<?php
include_once("../../../../CONNECTION/conexion.php");
include_once("cargar_sala.php");

$cines = cargarCines();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Sala - Web Cine</title>
    <link rel="stylesheet" href="../../../../CSS/styles.css">
    <link rel="stylesheet" href="../../../../CSS/formulario.css">
</head>
<body>
    <header class="header">
        <div class="logo">Web Cine - Gestión de Salas</div>
        <nav>
            <a href="../../Salas.php">Volver</a>
        </nav>
    </header>

    <div class="capa"></div>

    <div class="container">
        <?php if (isset($_GET['error'])): ?>
            <div class="mensaje error">
                <?= htmlspecialchars(urldecode($_GET['error'])) ?>
            </div>
        <?php endif; ?>

        <div class="formulario-agregar">
            <form id="form-sala" action="guardar_sala.php" method="POST">
                <table class="form-table">
                    <tr>
                        <td><label for="nombre">Nombre de la Sala:</label></td>
                        <td>
                            <input type="text" id="nombre" name="nombre" 
                                   required class="form-input" maxlength="100">
                        </td>
                    </tr>
                    
                    <tr>
                        <td><label for="tipo_pantalla">Tipo de Pantalla:</label></td>
                        <td>
                            <select id="tipo_pantalla" name="tipo_pantalla" required class="form-input">
                                <option value="">Seleccione...</option>
                                <option value="2D">2D</option>
                                <option value="3D">3D</option>
                                <option value="IMAX">IMAX</option>
                                <option value="4DX">4DX</option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <td><label for="cine_id">Cine:</label></td>
                        <td>
                            <select id="cine_id" name="cine_id" required class="form-input">
                                <option value="">Seleccione un cine...</option>
                                <?php if (!empty($cines)): ?>
                                    <?php foreach ($cines as $cine): ?>
                                        <option value="<?= htmlspecialchars($cine['id']) ?>">
                                            <?= htmlspecialchars($cine['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No hay cines disponibles</option>
                                <?php endif; ?>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <button type="submit" class="btn-submit">Agregar Sala</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</body>
</html>