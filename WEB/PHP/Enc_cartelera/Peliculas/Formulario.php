<?php
include_once("../../../CONNECTION/conexion.php");
include_once("cargar_pelicula.php");

if (isset($_GET['id'])) {
    $id_pelicula = $_GET['id'];

    // Usar tu función para obtener los datos de la película
    $pelicula = cargarPelicula($id_pelicula);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Película - Web Cine</title>
    <link rel="stylesheet" href="../../../CSS/styles.css">
    <link rel="stylesheet" href="../../../CSS/formulario.css">
</head>
<body>
    <header class="header">
        <div class="logo">Web Cine - Gestión de Películas</div>
        <nav>
            <a href="../vista_encargado.php">Lista de Peliculas</a>
            <a href="#"></a>
            <a href="#"></a>
        </nav>
    </header>

    <div class="capa"></div>

    <div class="container">
        <div class="formulario-agregar">
            <form id="form-pelicula" action="logica_actualizar.php" method="POST">
            <?php 
            // Evitamos pasar null a htmlspecialchars:
            $idPelicula = $pelicula['id'] ?? ''; 
            ?>
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($idPelicula, ENT_QUOTES, 'UTF-8'); ?>">    
                <table class="form-table">
                    <tr>
                        <td><label for="nombre">Nombre:</label></td>
                        <td><input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($pelicula['nombre']) ?>" required class="form-input"></td>
                    </tr>
                    
                    <tr>
                        <td><label for="duracion">Duración (min):</label></td>
                        <td><input type="number" id="duracion" name="duracion" value="<?= htmlspecialchars($pelicula['duracion']) ?>" min="1" required class="form-input"></td>
                    </tr>
                    
                    <tr>
                        <td><label for="sinopsis">Sinopsis:</label></td>
                        <td><textarea id="sinopsis" name="sinopsis" rows="3" required class="form-input"><?= htmlspecialchars($pelicula['sinopsis']) ?></textarea></td>
                    </tr>
                    
                    <tr>
                        <td><label for="director">Director:</label></td>
                        <td><input type="text" id="director" name="director" value="<?= htmlspecialchars($pelicula['director']) ?>" required class="form-input"></td>
                    </tr>
                    
                    <tr>
                        <td><label for="genero">Género:</label></td>
                        <td><input type="text" id="genero" name="genero" value="<?= htmlspecialchars($pelicula['genero']) ?>" required class="form-input"></td>
                    </tr>

                    <tr>
                        <td><label for="imagen">Url Portada:</label></td>
                        <td><input type="text" id="imagen" name="imagen" value="<?= htmlspecialchars($pelicula['imagen'])?>" required class="form-input"></td>
                    </tr>

                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <button type="submit" class="btn-submit">Actualizar Película</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</body>
</html>
