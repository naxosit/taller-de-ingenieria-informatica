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

<?php if (isset($_GET['actualizado']) && $_GET['actualizado'] == 1): ?>
    <div class="mensaje-exito">¡Función actualizada correctamente!</div>
<?php endif; ?>
<body>
    <header>
        <div class="logo">Web Cine - Gestión de Funciones</div>
        <nav>
            <a href="Cartelera.php">Cartelera</a>
            <a href="vista_encargado.php">Peliculas</a>
            <a href="vista_salas.php">Salas</a>
        </nav>
    </header>

    <div class="container">
        <h1 class="page-title">Listado de Funciones</h1>

        <div class = "contenedor-boton-agregar">
            <a href="Funciones/Agregar/Agregar_Funcion.php" class="boton-agregar">Agregar Funciones</a> 
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Pelicula</th>
                        <th>Sala</th>
                        <th>Cine</th>
                        <th>Fecha y Hora</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $query = "SELECT 
                                f.idFuncion,
                                f.FechaHora,
                                p.Nombre AS Pelicula,
                                s.Nombre AS Sala,
                                c.Nombre_cine AS Cine
                            FROM Funcion f
                            JOIN Pelicula p ON f.Id_Pelicula = p.idPelicula
                            JOIN Sala s ON f.Id_Sala = s.idSala
                            JOIN Cine c ON s.Cine_idCine = c.idCine
                            ORDER BY c.Nombre_cine, p.Nombre, f.FechaHora";

                    $stmt = $conn->query($query);

                    if ($stmt->rowCount() > 0) {
                        foreach ($stmt as $fila) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($fila['pelicula'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($fila['sala'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($fila['cine'] ?? '') . "</td>";
                            $fechaFormateada = date("d-m-Y H:i", strtotime($fila['fechahora']));
                            echo "<td>" . $fechaFormateada . "</td>";
                            
                            $idFuncion = $fila['idfuncion'];

                            echo "<td>";
                            echo "<a class='button-actualizar' href='Funciones/Actualizar/Actualizar_Funcion.php?idFuncion=$idFuncion'>Editar</a> ";
                            echo "<a class='button-eliminar' href='Funciones/Eliminar/Eliminar_Funcion.php?idFuncion=$idFuncion' onclick='return confirm(\"¿Estás seguro de eliminar esta función?\")'>Eliminar</a>";
                            echo "</td>";

                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No se encontraron funciones registradas</td></tr>";
                    }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='5'>Error al obtener funciones: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
