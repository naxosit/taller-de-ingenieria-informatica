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
    <div class="mensaje-exito">¡Cine actualizo correctamente!</div>
<?php endif; ?>
<body>
    <header>
        <div class="logo">Web Cine - Gestión de Cines</div>
        <nav>
            <a href="vista_admin.php">Volver</a>
        </nav>
    </header>

    <div class="container">
        <h1 class="page-title">Listado de Cines</h1>

        <div class = "contenedor-boton-agregar">
            <a href="Cines/Agregar/Agregar_Cine.php" class="boton-agregar">Agregar Cine</a> 
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Ciudad</th>
                        <th>Cine</th>
                        <th>Correo</th>
                        <th>Telefono</th>
                        <th>Ubicación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $query = "SELECT c.idCine,
                                            c.Nombre_cine AS Cine, 
                                            c.correo_cine AS Correo, 
                                            c.telefono AS Telefono, 
                                            c.ubicacion AS Ubicacion,
                                            ci.NombreCiudad AS Ciudad
                                            FROM Cine c
                                            JOIN Ciudad ci ON c.idCiudad = ci.idCiudad
                                            ORDER BY ci.NombreCiudad, c.Nombre_cine, c.correo_cine, c.telefono, c.ubicacion";
                        
                        
                    $stmt = $conn->query($query);

                    if ($stmt->rowCount() > 0) {
                        foreach ($stmt as $fila) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($fila['ciudad'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($fila['cine'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($fila['correo'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($fila['telefono'] ?? '') . "</td>";
                            echo "<td>" . htmlspecialchars($fila['ubicacion'] ?? '') . "</td>";
                            
                            $idCine = $fila['idcine'];

                            echo "<td>";
                            echo "<a class='button-actualizar' href='Cines/Actualizar/Actualizar_Cine.php?idCine=$idCine'>Editar</a> ";
                            echo "<a class='button-eliminar' href='Cines/Eliminar/Eliminar_Cine.php?id=$idCine' onclick='return confirm(\"¿Estás seguro de eliminar este Cine?\")'>Eliminar</a>";
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
