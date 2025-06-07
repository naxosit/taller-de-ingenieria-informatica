<?php include_once("../../CONNECTION/conexion.php");?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Salas - Web Cine</title>
    <link rel="stylesheet" href="../../CSS/styles.css">
    <link rel="stylesheet" href="../../CSS/botones.css">
</head>
<body>
    <header>
        <div class="logo">Web Cine - Gestión de Salas</div>
        <nav>
            <a href="Cartelera.php">Cartelera</a>
            <a href="../Peliculas/vista_pelicula.php">Películas</a>
            <a href="../Cines/vista_cine.php">Cines</a>
        </nav>
    </header>

    <div class="container">
        <h1 class="page-title">Listado de Salas</h1>
        
        <?php if (isset($_GET['error']) && $_GET['error'] === 'asociacion_funcion'): ?>
                <p style="color: red; font-weight: bold; margin-bottom: 15px;">
                    No se puede eliminar la sala porque tiene funciones asociadas.
                </p>
        <?php endif; ?>

        <?php if (isset($_GET['actualizado'])): ?>
            <div class="alert-success">Sala actualizada correctamente.</div>
        <?php endif; ?>
        
        <div class = "contenedor-boton-agregar">
            <a href="Salas/Agregar_sala.php" class="boton-agregar">Agregar Sala</a> 
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Tipo de Pantalla</th>
                        <th>Cine</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $query = 'SELECT 
                                s.idSala as id, 
                                s.Nombre as nombre, 
                                s.Tipo_pantalla as tipo_pantalla,
                                c.Nombre_cine as nombre_cine
                            FROM Sala s
                            JOIN Cine c ON s.Cine_idCine = c.idCine
                            ORDER BY c.Nombre_cine, s.Nombre';

                        $stmt = $conn->query($query);
                        
                        if ($stmt->rowCount() > 0) {
                            foreach ($stmt as $fila) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($fila['id'] ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars($fila['nombre'] ?? '') . "</td>";
                                echo "<td>" . htmlspecialchars($fila['tipo_pantalla'] ?? 'No especificado') . "</td>";
                                echo "<td>" . htmlspecialchars($fila['nombre_cine'] ?? '') . "</td>";
                                echo "<td><div class='acciones'>";
                                echo "<a href='Salas/Formulario.php?id=" . urlencode($fila['id']) . "' class='button-actualizar'>Actualizar</a>";
                                echo "<a href='Salas/Eliminar.php?id=" . urlencode($fila['id']) . "' class='button-eliminar' onclick=\"return confirm('¿Estás seguro de que deseas eliminar esta sala?');\">Eliminar</a>";
                                echo "</div></td>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No se encontraron salas</td></tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='5'>Error al obtener salas: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>