<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Película - Web Cine</title>
    <link rel="stylesheet" href="../../../CSS/styles.css">
    <link rel="stylesheet" href="../../../CSS/eliminar.css">
</head>
<body>
    <header>
        <div class="logo">Web Cine - Gestión de Películas</div>
        <nav>
            <a href="../agregar/Agregar_pelicula.php">Agregar Película</a>
            <a href="#"></a>
            <a href="../vista_encargado.php">Ver Peliculas</a>
        </nav>
    </header>

    <div class="container">
        <h1 class="page-title">Eliminar Película</h1>
        
        <!-- Mostrar mensajes de éxito/error -->
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="mensaje <?= (isset($_GET['error']) && $_GET['error'] == '1') ? 'error' : 'success' ?>">
                <?= htmlspecialchars(urldecode($_GET['mensaje'])) ?>
            </div>
        <?php endif; ?>

        <div class="delete-container">
            <form id="form-eliminar-pelicula" action="logica/logicaeliminar.php" method="POST">
                <div class="form-group">
                    <label for="id_pelicula">Seleccione la película a eliminar:</label>
                    <select id="id_pelicula" name="id_pelicula" required class="form-input">
                        <option value="" disabled selected>Seleccione</option>
                        <?php include __DIR__ . '/cargar/cargarpeliculas.php'; ?>

                    </select>
                </div>
                
                <div class="delete-warning">
                    Advertencia: Esta acción es irreversible. Se eliminarán todos los datos asociados a la película.
                </div>
                
                <div class="form-group" style="text-align: center;">
                    <button type="submit" class="btn-submit btn-delete">Eliminar Película</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>