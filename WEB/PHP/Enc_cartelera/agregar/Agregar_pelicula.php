<?php ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Película - Web Cine</title>
    <link rel="stylesheet" href="../../../CSS/styles.css">
    <link rel="stylesheet" href="../../../CSS/formulario.css">
</head>
<body>
    <header class="header">
    <div class="logo">Web Cine - Gestión de Películas</div>
        <nav>
            <a href="../vista_encargado.php">Ver Peliculas</a>
            <a href="#"></a>
            <a href="../Eliminar/eliminarpelicula.php">Eliminar Película</a>
        </nav>
    </header>

    <div class="capa"></div>

    <div class="container">
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="mensaje <?= isset($_GET['error']) ? 'error' : 'success' ?>">
                <?= htmlspecialchars(urldecode($_GET['mensaje'])) ?>
            </div>
        <?php endif; ?>

        <div class="formulario-agregar">
            <form id="form-pelicula" action="logica/guardar_pelicula.php" method="POST">
                <table class="form-table">
                    <tr>
                        <td><label for="nombre">Nombre:</label></td>
                        <td><input type="text" id="nombre" name="nombre" required class="form-input"></td>
                    </tr>
                    
                    <tr>
                        <td><label for="duracion">Duración (min):</label></td>
                        <td><input type="number" id="duracion" name="duracion" min="1" required class="form-input"></td>
                    </tr>
                    
                    <tr>
                        <td><label for="sinopsis">Sinopsis:</label></td>
                        <td><textarea id="sinopsis" name="sinopsis" rows="3" required class="form-input"></textarea></td>
                    </tr>
                    
                    <tr>
                        <td><label for="director">Director:</label></td>
                        <td><input type="text" id="director" name="director" required class="form-input"></td>
                    </tr>
                    
                    <tr>
                        <td><label for="genero">Género:</label></td>
                        <td><input type="text" id="genero" name="genero" required class="form-input"></td>
                    </tr>

                    
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <button type="submit" class="btn-submit">Guardar Película</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</body>
</html>