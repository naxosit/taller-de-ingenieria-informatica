<?php
include_once("../../../CONNECTION/conexion.php");
include_once("cargar_sala.php");

// Habilitar visualización de errores (solo para desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['id'])) {
    $id_sala = $_GET['id'];
    $sala = cargarSala($id_sala);
    $cines = cargarCines();
    
    
    
    if (!$sala) {
        header("Location: ../vista_salas.php?error=sala_no_encontrada");
        exit;
    }
} else {
    header("Location: ../vista_salas.php?error=id_no_proporcionado");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Sala - Web Cine</title>
    <link rel="stylesheet" href="../../../CSS/styles.css">
    <link rel="stylesheet" href="../../../CSS/formulario.css">
</head>
<body>
    <header class="header">
        <div class="logo">Web Cine - Gestión de Salas</div>
        <nav>
            <a href="../vista_salas.php">Lista de Salas</a>
            <a href="../Peliculas/vista_pelicula.php">Películas</a>
            <a href="../Cines/vista_cine.php">Cines</a>
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
            <form id="form-sala" action="logica_actualizar.php" method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($sala['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                
                <table class="form-table">
                    <tr>
                        <td><label for="nombre">Nombre de la Sala:</label></td>
                        <td>
                            <input type="text" id="nombre" name="nombre" 
                                   value="<?= htmlspecialchars($sala['nombre'] ?? '') ?>" 
                                   required class="form-input" maxlength="100">
                        </td>
                    </tr>
                    
                    <tr>
                        <td><label for="tipo_pantalla">Tipo de Pantalla:</label></td>
                        <td>
                            <select id="tipo_pantalla" name="tipo_pantalla" class="form-input">
                                <option value="">Seleccione...</option>
                                <option value="2D" <?= ($sala['tipo_pantalla'] ?? '') == '2D' ? 'selected' : '' ?>>2D</option>
                                <option value="3D" <?= ($sala['tipo_pantalla'] ?? '') == '3D' ? 'selected' : '' ?>>3D</option>
                                <option value="IMAX" <?= ($sala['tipo_pantalla'] ?? '') == 'IMAX' ? 'selected' : '' ?>>IMAX</option>
                                <option value="4DX" <?= ($sala['tipo_pantalla'] ?? '') == '4DX' ? 'selected' : '' ?>>4DX</option>
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
                                        <option value="<?= htmlspecialchars($cine['id']) ?>"
                                            <?= ($cine['id'] == ($sala['cine_id'] ?? '')) ? 'selected' : '' ?>>
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
                            <button type="submit" class="btn-submit">Actualizar Sala</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</body>
</html>