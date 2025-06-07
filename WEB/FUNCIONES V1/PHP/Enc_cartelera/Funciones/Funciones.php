<?php include_once("../../../CONNECTION/conexion.php");?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Películas - Web Cine</title>
    <link rel="stylesheet" href="../../../CSS/styles.css">
    <link rel="stylesheet" href="../../../CSS/botones.css">
</head>
<body>
    <header>
        <div class="logo">Web Cine - Gestión de Películas</div>
        <nav>
            <a href="../Cartelera.php">Cartelera</a>
            <a href="#"></a>
            <a href="#"></a>
        </nav>
    </header>

    <div class="container">
        <h1 class="page-title">Listado de Películas</h1>

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
                                    f.Id_Pelicula,
                                    f.Id_Sala,
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
                                echo "<td>" . htmlspecialchars($fila['cine'] ?? '') . " min</td>";
                                $fechaFormateada = date("d-m-Y H:i", strtotime($fila['fechahora']));
                                echo "<td>" . $fechaFormateada . "</td>";
                                // En Acciones, puedes colocar botones o links para editar, eliminar, etc.
                                $idPelicula = $fila['id_pelicula'];
                                $idSala = $fila['id_sala'];
                                $fechaUrl = urlencode($fila['fechahora']);
                                echo "<td>";
                                echo "<a class='button-actualizar' href='editarFuncion.php?pelicula=$idPelicula&sala=$idSala&fecha=$fechaUrl'>Editar</a> ";
                                echo "<a class='button-eliminar' href='eliminarFuncion.php?pelicula=$idPelicula&sala=$idSala&fecha=$fechaUrl' onclick='return confirm(\"¿Estás seguro de eliminar esta función?\")'>Eliminar</a>";
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
