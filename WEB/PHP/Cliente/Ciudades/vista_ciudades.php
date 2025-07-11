<?php
// Asegúrate de que la ruta a tu archivo de conexión es correcta.
// Si tu archivo de conexión maneja la configuración y la inicialización de $conn como un objeto PDO, está bien.
include_once("../../../CONNECTION/conexion.php");

// 2. Consulta a la base de datos para obtener las regiones
// Usamos los nombres de tabla y columnas que me proporcionaste (region, idregion, nombreregion)
$sql = "SELECT idregion, nombreregion FROM region ORDER BY nombreregion ASC";

try {
    // Prepara la consulta para mayor seguridad y eficiencia (especialmente con parámetros)
    $stmt = $conn->prepare($sql);
    // Ejecuta la consulta
    $stmt->execute();
    // Obtiene todos los resultados como un array asociativo
    $regiones = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error al ejecutar la consulta: " . $e->getMessage());
}
    
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Regiones de nuestros cines</title>
    <link rel="stylesheet" href="../../../CSS/styles.css">
    <link rel="stylesheet" href="../../../CSS/botones.css">
    <style>
        /* Estilos generales, algunos ya los tenías */
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
            max-width: 600px;
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
            margin-bottom: 10px; /* Espacio entre los botones/enlaces */
        }
        .no-regions {
            text-align: center;
            color: #95a5a6;
            font-style: italic;
            padding: 20px;
        }

        /* Estilos específicos para los "botones" de región */
        .region-button {
            display: block; /* Hace que el enlace ocupe todo el ancho del li */
            background-color: #6b51e1; /* Color de fondo similar a tu logo */
            color: white; /* Color del texto */
            padding: 12px 15px;
            border-radius: 6px;
            text-align: center;
            text-decoration: none; /* Quitar el subrayado por defecto de los enlaces */
            font-weight: bold;
            font-size: 1.1em;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .region-button:hover {
            background-color: #5a42cc; /* Un tono más oscuro al pasar el mouse */
            transform: translateY(-2px); /* Un pequeño efecto de levantamiento */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .region-button:active {
            background-color: #4b35b0; /* Un tono aún más oscuro al hacer clic */
            transform: translateY(0); /* Vuelve a su posición original */
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* === NUEVO ESTILO PARA EL BOTÓN DE VOLVER === */
        .back-button {
            display: block;
            width: fit-content;
            margin: 30px auto 10px; /* Margen superior para separarlo, centrado horizontalmente */
            background-color: #7f8c8d; /* Un color neutro/secundario */
            color: white;
            padding: 10px 25px;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #95a5a6;
        }
        
        /* Estilos para tu navegación y logo existentes */
        .page-title { /* Puedes quitar esto si <h1> es el título principal */
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
        <h1>Regiones en donde nos ubicamos</h1>

        <?php
        if (count($regiones) > 0) {
            echo "<ul>";
            foreach ($regiones as $row) {
                // Generamos un enlace (<a>) con una clase para darle estilo de botón
                // La URL será 'ciudades_por_region.php' y pasaremos el idregion como parámetro GET
                echo "<li><a href='ciudades_por_region.php?idregion=" . htmlspecialchars($row["idregion"]) . "' class='region-button'>";
                echo htmlspecialchars($row["nombreregion"]);
                echo "</a></li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='no-regions'>No se encontraron regiones en la base de datos.</p>";
        }
        ?>
        
        <a href="../Index.php" class="back-button">Volver al Inicio</a>

    </div>

    <?php
    $conn = null; // Cierra la conexión PDO
    ?>
</body>
</html>
