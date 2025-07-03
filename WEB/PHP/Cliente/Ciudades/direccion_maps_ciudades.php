<?php
// Incluye el archivo de conexión a la base de datos.
include_once("../../../CONNECTION/conexion.php");

// 1. Obtener el idCiudad de la URL (parámetro GET)
$id_ciudad = isset($_GET['idciudad']) ? intval($_GET['idciudad']) : 0;

// Si no se proporcionó un ID de ciudad válido, redirige o muestra un error.
if ($id_ciudad === 0) {
    die("Error: No se ha especificado una ciudad válida para buscar direcciones de cines.");
}

// 2. Consulta a la base de datos para obtener el nombre de la ciudad y su idRegion.
$nombre_ciudad = "Ciudad Desconocida";
$id_region = 0; // Inicializar la variable para evitar errores.

try {
    // MODIFICACIÓN: La consulta SQL ahora usa nombres en minúsculas para mayor compatibilidad con PostgreSQL.
    $sql_ciudad_info = "SELECT nombreciudad, idregion FROM ciudad WHERE idciudad = :id_ciudad";
    $stmt_ciudad_info = $conn->prepare($sql_ciudad_info);
    $stmt_ciudad_info->bindParam(':id_ciudad', $id_ciudad, PDO::PARAM_INT);
    $stmt_ciudad_info->execute();
    $ciudad_info = $stmt_ciudad_info->fetch();
    
    if ($ciudad_info) {
        $nombre_ciudad = htmlspecialchars($ciudad_info['nombreciudad']);
        $id_region = intval($ciudad_info['idregion']); 
    }
} catch (PDOException $e) {
    error_log("Error al obtener la información de la ciudad: " . $e->getMessage());
}

// 3. Consulta a la base de datos para obtener las URLs de Maps de los cines en la ciudad.
// MODIFICACIÓN: La consulta SQL ahora usa nombres en minúsculas.
$sql_direcciones_maps = "
    SELECT
        dm.url,
        c.nombre_cine
    FROM
        direccionmaps AS dm
    JOIN
        cine AS c ON dm.idcine = c.idcine
    WHERE
        dm.idciudad = :id_ciudad
";

$direcciones = [];
try {
    $stmt_direcciones_maps = $conn->prepare($sql_direcciones_maps);
    $stmt_direcciones_maps->bindParam(':id_ciudad', $id_ciudad, PDO::PARAM_INT);
    $stmt_direcciones_maps->execute();
    // PDO para PostgreSQL devuelve los nombres de columna en minúsculas por defecto.
    $direcciones = $stmt_direcciones_maps->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al ejecutar la consulta de direcciones de Maps: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cines en <?php echo $nombre_ciudad; ?> - Direcciones</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <link rel="stylesheet" href="../../../CSS/styles.css">
    <link rel="stylesheet" href="../../../CSS/botones.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            background-color: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 30px auto;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            background-color: #ecf0f1;
            margin-bottom: 10px;
            padding: 12px 15px;
            border-radius: 6px;
            display: flex;
            flex-direction: column; 
            justify-content: space-between;
            align-items: flex-start;
            transition: background-color 0.3s ease;
        }
        li:hover {
            background-color: #dbe2e6;
        }
        li strong {
            font-weight: bold;
            color: #34495e;
            margin-bottom: 5px;
        }
        li a {
            color: #3498db;
            text-decoration: none;
            word-break: break-all;
        }
        li a:hover {
            text-decoration: underline;
        }
        .no-results {
            text-align: center;
            color: #95a5a6;
            font-style: italic;
            padding: 20px;
        }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .back-button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Cines en <?php echo $nombre_ciudad; ?></h1>

        <?php
        if (count($direcciones) > 0) {
            echo "<ul>";
            foreach ($direcciones as $row) {
                echo "<li>";
                // Los índices del array asociativo ya están en minúsculas, lo cual es correcto.
                echo "<strong>" . htmlspecialchars($row["nombre_cine"]) . "</strong><br>";
                echo "<a href='" . htmlspecialchars($row["url"]) . "' target='_blank' title='Abrir en Google Maps'><i class='fa-solid fa-map-location-dot'></i></a>";  
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='no-results'>No se encontraron direcciones de cines para esta ciudad.</p>";
        }
        ?>

        <div style="text-align: center;">
            <?php if ($id_region !== 0): ?>
                <a href="ciudades_por_region.php?idregion=<?php echo htmlspecialchars($id_region); ?>" class="back-button">Volver a Ciudades de la Región</a>
            <?php else: ?>
                <a href="vista_ciudades.php" class="back-button">Volver a Ciudades</a>
            <?php endif; ?>
        </div>
    </div>

    <?php
    $conn = null;
    ?>
</body>
</html>