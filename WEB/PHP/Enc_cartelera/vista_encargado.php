<?php include_once("../../CONNECTION/conexion.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Películas - Web Cine</title>
    <link rel="stylesheet" href="../../CSS/styles.css">
</head>
<body>
    <header>
        <div class="logo">Web Cine - Gestión de Películas</div>
        <nav>
            <a href="#">Agregar Película</a>
            <a href="#">Actualizar Película</a>
            <a href="#">Eliminar Película</a>
        </nav>
    </header>

    <div class="container">
        <h1 class="page-title">Listado de Películas</h1>
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Duración</th>
                        <th>Director</th>
                        <th>Clasificación</th>
                        <th>Género</th>
                        <th>Estreno</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $query = 'SELECT p.idpelicula, p.nombre, p.duracion_min, p.director, p.clasificacion,
                            g.nombre_genero AS genero, p.fecha_estreno
                            FROM pelicula p
                            JOIN genero g ON p.genero_idgenero = g.idgenero';

                        $stmt = $conn->query($query);
                        foreach ($stmt as $fila) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($fila['idpelicula']) . "</td>";
                            echo "<td>" . htmlspecialchars($fila['nombre']) . "</td>";
                            echo "<td>" . htmlspecialchars($fila['duracion_min']) . " min</td>";
                            echo "<td>" . htmlspecialchars($fila['director']) . "</td>";
                            echo "<td>" . htmlspecialchars($fila['clasificacion']) . "</td>";
                            echo "<td>" . htmlspecialchars($fila['genero']) . "</td>";
                            echo "<td>" . htmlspecialchars($fila['fecha_estreno']) . "</td>";
                            echo "</tr>";
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
