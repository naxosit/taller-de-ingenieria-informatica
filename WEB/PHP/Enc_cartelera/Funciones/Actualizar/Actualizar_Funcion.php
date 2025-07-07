<?php
include_once("../../../../CONNECTION/conexion.php");
include_once("Cargar_Funcion.php");

// Inicializar variables
$error_general = '';
$error_fecha_hora = '';
$fecha_nueva = '';
$hora_nueva = '';

// Si se envió el formulario (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idFuncion = $_POST['idFuncion'] ?? '';
    $fecha_nueva = $_POST['fecha_nueva'] ?? '';
    $hora_nueva = $_POST['hora_nueva'] ?? '';

    // Validar que no estén vacíos
    if (empty($idFuncion) || empty($fecha_nueva) || empty($hora_nueva)) {
        $error_general = "Todos los campos son obligatorios";
    } else {
        // Validar formato de hora
        if (!preg_match('/^\d{2}:\d{2}$/', $hora_nueva)) {
            $error_fecha_hora = "Formato de hora inválido";
        } else {
            list($horas, $minutos) = explode(':', $hora_nueva);
            $horas = (int)$horas;
            $minutos = (int)$minutos;
            
            if ($horas < 0 || $horas > 23) {
                $error_fecha_hora = "Hora inválida (00-23)";
            } elseif ($minutos < 0 || $minutos > 59) {
                $error_fecha_hora = "Minutos inválidos (00-59)";
            } else {
                // Validar fecha futura
                $fechaHoraCompleta = $fecha_nueva . ' ' . $hora_nueva . ':00';
                $fechaHoraIngresada = new DateTime($fechaHoraCompleta);
                $fechaHoraActual = new DateTime();

                if ($fechaHoraIngresada <= $fechaHoraActual) {
                    $error_fecha_hora = "La fecha y hora deben ser futuras";
                }
            }
        }
    }

    // Si no hay errores, procesar la actualización
    if (empty($error_general) && empty($error_fecha_hora)) {
        try {
            $query = "UPDATE Funcion 
                      SET FechaHora = :nueva_fecha
                      WHERE idFuncion = :idFuncion";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':nueva_fecha', $fechaHoraCompleta);
            $stmt->bindParam(':idFuncion', $idFuncion, PDO::PARAM_INT);
            $stmt->execute();

            header("Location: ../../Funciones.php?actualizado=1");
            exit;
        } catch (PDOException $e) {
            $error_general = "Error al actualizar función: " . $e->getMessage();
        }
    }
}

// Si es GET o si hay errores, cargar la función
if (isset($_GET['idFuncion'])) {
    $idFuncion = $_GET['idFuncion'];
    $funcion = cargarFuncion($idFuncion);
    
    // Separar fecha y hora
    $fecha_actual = date('Y-m-d', strtotime($funcion['fechahora']));
    $hora_actual = date('H:i', strtotime($funcion['fechahora']));
} else {
    // Si no hay idFuncion, redirigir
    header("Location: ../../Funciones.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Función - Web Cine</title>
    <link rel="stylesheet" href="../../../../CSS/styles.css">
    <link rel="stylesheet" href="../../../../CSS/formulario.css">
</head>
<body>
    <header class="header">
        <div class="logo">Web Cine - Gestión de Funciones</div>
        <nav>
            <a href="../../Funciones.php">Lista de Funciones</a>
        </nav>
    </header>

    <div class="capa"></div>

    <div class="container">
        <div class="formulario-agregar">
            <form id="form-funcion" action="Logica_Actualizar.php" method="POST">
                <!-- Campos ocultos para claves primarias -->
                <input type="hidden" name="idFuncion" value="<?= htmlspecialchars($funcion['idfuncion']) ?>">

                <table class="form-table">
                    <tr>
                        <td><label>Película:</label></td>
                        <td><input type="text" value="<?= htmlspecialchars($funcion['nombre_pelicula']) ?>" disabled class="form-input"></td>
                    </tr>

                    <tr>
                        <td><label>Sala y Cine:</label></td>
                        <td><input type="text" value="<?= htmlspecialchars($funcion['nombre_sala'] . " - " . $funcion['nombre_cine']) ?>" disabled class="form-input"></td>
                    </tr>

                    <tr>
                        <td><label for="fechahora">Fecha y Hora Original:</label></td>
                        <td>
                            <input type="datetime-local" name="fechahora" id="fechahora" 
                                value="<?= date('Y-m-d\TH:i', strtotime($funcion['fechahora'])) ?>" readonly
                                required class="form-input">
                        </td>
                    </tr>

                    <tr>
                        <td><label for="fechahora_nueva">Nueva Fecha y Hora:</label></td>
                        <td>
                            <div class="date-time-container">
                                <input type="date" id="fecha_nueva" name="fecha_nueva" 
                                       value="<?= htmlspecialchars($fecha_nueva ?: $fecha_actual) ?>" 
                                       min="<?= date('Y-m-d') ?>" required>
                                <input type="text" id="hora_nueva" name="hora_nueva" 
                                       value="<?= htmlspecialchars($hora_nueva ?: $hora_actual) ?>" 
                                       placeholder="HH:MM"
                                       required>
                            </div>
                            <?php if ($error_fecha_hora): ?>
                                <div class="error-message">
                                    <?= htmlspecialchars($error_fecha_hora) ?>
                                </div>
                            <?php endif; ?>
                            <div class="hint">Formato: HH:MM (24 horas, minutos 00-59)</div>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <button type="submit" class="btn-submit">Actualizar Función</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
          <script src="../../../../JS/Ac_hora.js"></script>
</body>
</html>
