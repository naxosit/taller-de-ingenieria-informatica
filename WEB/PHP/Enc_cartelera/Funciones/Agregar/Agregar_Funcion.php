<?php 
include_once("Cargar_Datos.php");

// Recuperar valores del formulario si existen
$id_pelicula = $_POST['id_pelicula'] ?? '';
$id_sala = $_POST['id_sala'] ?? '';
$fecha = $_POST['fecha'] ?? '';
$hora = $_POST['hora'] ?? '';

// Variables para mensajes de error
$error_fecha = '';
$error_general = '';

// Procesar el formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que todos los campos estén presentes
    if (empty($id_pelicula) || empty($id_sala) || empty($fecha) || empty($hora)) {
        $error_general = "Todos los campos son obligatorios";
    } else {
        // Validar formato de fecha
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            $error_fecha = "Formato de fecha inválido";
        } 
        // Validar formato de hora
        elseif (!preg_match('/^\d{2}:\d{2}$/', $hora)) {
            $error_fecha = "Formato de hora inválido";
        } else {
            // Extraer componentes de hora
            list($horas, $minutos) = explode(':', $hora);
            
            // Validar componentes numéricos
            $horas = (int)$horas;
            $minutos = (int)$minutos;
            
            // Validar rango de hora y minutos
            if ($horas < 0 || $horas > 23) {
                $error_fecha = "La hora debe estar entre 00 y 23";
            } elseif ($minutos < 0 || $minutos > 59) {
                $error_fecha = "Los minutos deben estar entre 00 y 59";
            } else {
                // Validar si la fecha es futura
                $fecha_actual = new DateTime();
                $fecha_completa = $fecha . ' ' . $hora;
                $fecha_seleccionada = DateTime::createFromFormat('Y-m-d H:i', $fecha_completa);
                
                if ($fecha_seleccionada <= $fecha_actual) {
                    $error_fecha = "Debe seleccionar una fecha y hora futura";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Película - Web Cine</title>
    <link rel="stylesheet" href="../../../../CSS/styles.css">
    <link rel="stylesheet" href="../../../../CSS/formulario.css">
    <style>
        .error-message {
            color: #e74c3c;
            font-size: 0.9rem;
            margin-top: 5px;
            display: block;
        }
        .date-time-container {
            display: flex;
            gap: 10px;
        }
        .date-time-container input {
            flex: 1;
        }
        .hint {
            font-size: 0.85rem;
            color: #95a5a6;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">Web Cine - Gestión de Funciones</div>
        <nav>
            <a href="../../Funciones.php">Lista de Funciones</a>
            <a href="#">Calendario</a>
            <a href="#">Reportes</a>
        </nav>
    </header>

    <div class="capa"></div>

    <div class="container">
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="mensaje <?= isset($_GET['error']) ? 'error' : 'success' ?>">
                <?= htmlspecialchars(urldecode($_GET['mensaje'])) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error_general): ?>
            <div class="mensaje error">
                <?= htmlspecialchars($error_general) ?>
            </div>
        <?php endif; ?>

        <div class="formulario-agregar">
            <form id="form-pelicula" action="Guardar_funcion.php" method="POST">
                <table class="form-table">
                    <tr>
                        <td><label for="pelicula">Película:</label></td>
                        <td>
                            <select name="id_pelicula" required>
                                <option value="">Seleccione una película</option>
                                <?php foreach ($peliculas as $p): ?>
                                    <option value="<?= $p['idpelicula'] ?>"
                                        <?= ($id_pelicula == $p['idpelicula']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <td><label for="sala">Sala:</label></td>
                        <td>
                            <select id="id_sala" name="id_sala" required class="form-input">
                                <option value="">Seleccione una sala</option>
                                <?php foreach ($salas as $s): ?>
                                    <option value="<?= $s['idsala'] ?>"
                                        <?= ($id_sala == $s['idsala']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s['nombre_sala'] . " - " . $s['nombre_cine']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td><label for="fecha">Fecha y Hora:</label></td>
                        <td>
                            <div class="date-time-container">
                                <input type="date" id="fecha" name="fecha" 
                                    value="<?= htmlspecialchars($fecha) ?>" 
                                    min="<?= date('Y-m-d') ?>" required>
                                <input type="text" id="hora" name="hora" 
                                    value="<?= htmlspecialchars($hora) ?>" 
                                    placeholder="HH:MM"
                                    required>
                            </div>
                            <?php if ($error_fecha): ?>
                                <div class="error-message">
                                    <?= htmlspecialchars($error_fecha) ?>
                                </div>
                            <?php endif; ?>
                            <div class="hint">Formato: HH:MM (24 horas, minutos 00-59)</div>
                        </td>
                    </tr>
                    
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <button type="submit" class="btn-submit">Guardar Función</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>

      <script src="../../../../JS/hora.js"></script>
</body>
</html>