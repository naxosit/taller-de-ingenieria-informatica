<?php
include_once("../../../../CONNECTION/conexion.php");
include_once("Cargar_Funcion.php");

if (isset($_GET['idFuncion'])) {
    $idFuncion = $_GET['idFuncion'];

    // Cargar función por ID
    $funcion = cargarFuncion($idFuncion);
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

                    <tr><label for="fechahora_nueva">Nueva Fecha y Hora:</label>
                        <input type="datetime-local" id="fechahora_nueva" name="fechahora_nueva" 
                        value="<?= date('Y-m-d\TH:i', strtotime($funcion['fechahora'])) ?>" required>
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
</body>
</html>
