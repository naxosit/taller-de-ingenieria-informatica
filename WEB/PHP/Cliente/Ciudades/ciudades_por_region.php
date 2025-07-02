<?php
// Incluye el archivo de conexión a la base de datos.
// Asegúrate de que esta ruta sea correcta y que 'conexion.php' inicialice la variable $conn como un objeto PDO.
include_once("../../../CONNECTION/conexion.php");

// 1. Obtener el idRegion de la URL (parámetro GET)
// Se usa intval() para asegurar que el valor sea un entero, lo cual es crucial para la seguridad.
$id_region = isset($_GET['idregion']) ? intval($_GET['idregion']) : 0;

// Si no se proporcionó un ID de región válido (es decir, es 0 o no se encontró en la URL),
// se detiene la ejecución del script y se muestra un mensaje de error.
// Alternativamente, podrías redirigir al usuario a la página principal de regiones si prefieres.
if ($id_region === 0) {
    die("Error: No se ha especificado una región válida para mostrar ciudades.");
    // Ejemplo de redirección:
    // header("Location: mostrar_regiones.php");
    // exit();
}

// 2. Consulta para obtener el nombre de la región.
// Esto es opcional, pero permite mostrar un título más amigable en la página (ej. "Ciudades de Región Metropolitana").
$nombre_region = "Región Desconocida"; // Valor por defecto si no se encuentra la región
try {
    // Prepara la consulta para obtener el nombre de la región de la tabla 'Region'.
    $sql_region_nombre = "SELECT NombreRegion FROM Region WHERE idRegion = :id_region";
    $stmt_region_nombre = $conn->prepare($sql_region_nombre);
    // Bindea el parámetro :id_region con el valor obtenido de la URL. PDO::PARAM_INT especifica que es un entero.
    $stmt_region_nombre->bindParam(':id_region', $id_region, PDO::PARAM_INT);
    $stmt_region_nombre->execute();
    // Obtiene la primera fila del resultado.
    $region_info = $stmt_region_nombre->fetch();
    // Si se encontró la región, actualiza el nombre_region.
    if ($region_info) {
        $nombre_region = htmlspecialchars($region_info['nombreregion']);
    }
} catch (PDOException $e) {
    // Si hay un error en la consulta del nombre de la región, se registra en el log de errores.
    // No se detiene el script para que la página pueda intentar cargar las ciudades.
    error_log("Error al obtener el nombre de la región: " . $e->getMessage());
}


// 3. Consulta a la base de datos para obtener las ciudades de la región específica.
// Se utilizan los nombres de tabla y columnas que proporcionaste: 'Ciudad', 'idCiudad', 'NombreCiudad', 'idRegion'.
$sql_ciudades = "SELECT idCiudad, NombreCiudad FROM Ciudad WHERE idRegion = :id_region ORDER BY NombreCiudad ASC";

try {
    // Prepara la consulta para obtener las ciudades.
    $stmt_ciudades = $conn->prepare($sql_ciudades);
    // Bindea el parámetro :id_region con el ID de la región.
    $stmt_ciudades->bindParam(':id_region', $id_region, PDO::PARAM_INT);
    $stmt_ciudades->execute();
    // Obtiene todas las ciudades como un array de arrays asociativos.
    $ciudades = $stmt_ciudades->fetchAll();
} catch (PDOException $e) {
    // Si hay un error en la consulta de ciudades, se detiene el script y se muestra el error.
    die("Error al ejecutar la consulta de ciudades: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ciudades de <?php echo $nombre_region; ?></title>
    <!-- Incluye tus hojas de estilo CSS. Asegúrate de que las rutas sean correctas. -->
    <link rel="stylesheet" href="../../../CSS/styles.css">
    <link rel="stylesheet" href="../../../CSS/botones.css">
    <style>
        /* Estilos generales para el cuerpo de la página */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        /* Estilos para el contenedor principal del contenido */
        .container {
            background-color: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 30px auto;
        }
        /* Estilos para el título principal de la página */
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        /* Estilos para la lista desordenada de ciudades */
        ul {
            list-style-type: none;
            padding: 0;
        }
        /* Estilos para cada elemento de la lista (cada ciudad) */
        li {
            background-color: #ecf0f1;
            margin-bottom: 10px;
            padding: 12px 15px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s ease;
        }
        /* Efecto al pasar el mouse sobre un elemento de la lista */
        li:hover {
            background-color: #dbe2e6;
        }
        /* Estilos para el nombre de la ciudad (primer span dentro del li) */
        li span:first-child {
            font-weight: bold;
            color: #34495e;
        }
        /* Estilos para el ID de la ciudad (segundo span dentro del li) */
        li span:last-child {
            color: #7f8c8d;
            font-size: 0.9em;
            background-color: #bdc3c7;
            padding: 4px 8px;
            border-radius: 3px;
        }
        /* Estilos para el mensaje cuando no se encuentran ciudades */
        .no-cities {
            text-align: center;
            color: #95a5a6;
            font-style: italic;
            padding: 20px;
        }
        /* Estilos para el botón "Volver a Regiones" */
        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #3498db; /* Un color azul distintivo */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        /* Efecto al pasar el mouse sobre el botón "Volver" */
        .back-button:hover {
            background-color: #2980b9; /* Tono más oscuro al pasar el mouse */
        }

        /* Estilos para tu navegación y logo existentes (si los tienes en otras páginas y quieres consistencia) */
        .page-title {
            text-align: center;
            margin-bottom: 25px;
            color: #6b51e1;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            padding: 15px 0;
            color: #6b51e1;
            text-align: center;
        }
        nav {
            text-align: center;
            margin-bottom: 20px;
        }
        nav a {
            margin: 0 15px;
            text-decoration: none;
            color: #6b51e1;
            font-weight: bold;
        }
        nav a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Título de la página, mostrando el nombre de la región -->
        <h1>Ciudades de <?php echo $nombre_region; ?></h1>

        <?php
        // Verifica si se encontraron ciudades para la región.
        if (count($ciudades) > 0) {
            echo "<ul>";
            // Itera sobre cada ciudad y la muestra en un elemento de lista.
            foreach ($ciudades as $row) {
                // htmlspecialchars es crucial para prevenir ataques de Cross-Site Scripting (XSS).
                echo "<li><span>" . htmlspecialchars($row["nombreciudad"]) . "</span>";
                echo "<a href='direccion_maps_ciudades.php?idciudad=" . htmlspecialchars($row["idciudad"]) . "' class='back-button' style='margin-left: 10px; padding: 5px 10px; font-size: 0.8em;'>Ver Cines</a>";
                // Aquí podrías añadir un enlace para cada ciudad si necesitas ir a un nivel más profundo
                // Por ejemplo, a una página de detalles de la ciudad o para seleccionar una ubicación específica.
            }
            echo "</ul>";
        } else {
            // Mensaje si no se encontraron ciudades.
            echo "<p class='no-cities'>No se encontraron ciudades para esta región.</p>";
        }
        ?>
        <!-- Botón para volver a la página de regiones -->
        <div style="text-align: center;">
            <a href="vista_ciudades.php" class="back-button">Volver a Regiones</a>
        </div>
    </div>

    <?php
    // Cierra la conexión PDO. Asignar null a la variable de conexión es suficiente.
    $conn = null;
    ?>
</body>
</html>
