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
                        <th>Género</th>
                        <th>Sinopsis</th>
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
                                // Mostrar solo los primeros 50 caracteres de la sinopsis
                                $sinopsis = isset($fila['sinopsis']) ? 
                                    (strlen($fila['sinopsis']) > 50 ? substr($fila['sinopsis'], 0, 50) . '...' : $fila['sinopsis']) : 
                                    '';
                                echo "<td>" . htmlspecialchars($sinopsis) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No se encontraron películas</td></tr>";
                        }
                        
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='6'>Error al obtener películas: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>