<?php include_once("../../CONNECTION/conexion.php");?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Películas - Web Cine</title>
    <link rel="stylesheet" href="../../CSS/styles.css">
    <link rel="stylesheet" href="../../CSS/botones.css">
</head>
<body>
    <header>
        <div class="logo">Web Cine - Gestión de Películas</div>
        <nav>
            <a href="Cartelera.php">Cartelera</a>
            <a href="vista_salas">Salas</a>
            <a href="Funciones.php">Funciones</a>
        </nav>
    </header>

    <div class="container">
        <h1 class="page-title">Listado de Películas</h1>
        
        <?php if (isset($_GET['error']) && $_GET['error'] === 'asociacion_funcion'): ?>
                <p style="color: red; font-weight: bold; margin-bottom: 15px;">
                    No se puede eliminar la película porque tiene funciones asociadas.
                </p>
        <?php endif; ?>

        <?php if (isset($_GET['actualizado'])): ?>
            <div class="alert-success">Película actualizada correctamente.</div>
        <?php endif; ?>
        
        <div class = "contenedor-boton-agregar">
            <a href="Peliculas/Agregar_pelicula.php" class="boton-agregar">Agregar Pelicula</a> 
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Duración</th>
                        <th>Director</th>
                        <th>Género</th>
                        <th>Sinopsis</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $query = 'SELECT 
                                idPelicula as id, 
                                Nombre as nombre, 
                                Duracion as duracion, 
                                Director as director, 
                                Genero as genero,
                                Sinopsis as sinopsis
                            FROM Pelicula
                            ORDER BY idPelicula';

                        $stmt = $conn->query($query);
                        
                        if ($stmt->rowCount() > 0) {
                            foreach ($stmt as $fila) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($fila['id'] ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars($fila['nombre'] ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars($fila['duracion'] ?? '') . " min</td>";
                                echo "<td>" . htmlspecialchars($fila['director'] ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars($fila['genero'] ?? '') . "</td>";
                                $sinopsis = isset($fila['sinopsis']) ? 
                                    (strlen($fila['sinopsis']) > 50 ? substr($fila['sinopsis'], 0, 50) . '...' : $fila['sinopsis']) : '';
                                echo "<td>" . htmlspecialchars($sinopsis) . "</td>";
                                echo "<td><div class='acciones'>";
                                echo "<a href='Peliculas/Actualizar.php?id=" . urlencode($fila['id']) . "' class='button-actualizar'>Actualizar</a>";
                                echo "<a href='Peliculas/Eliminar.php?id=" . urlencode($fila['id']) . "' class='button-eliminar' onclick=\"return confirm('¿Estás seguro de que deseas eliminar esta película?');\">Eliminar</a>";
                                echo "</div></td>";


                            }
                        } else {
                            echo "<tr><td colspan='7'>No se encontraron películas</td></tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='7'>Error al obtener películas: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
