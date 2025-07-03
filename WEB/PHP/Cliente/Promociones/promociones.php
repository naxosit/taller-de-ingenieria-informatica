<?php
// --- CONFIGURACIÓN DE LA BASE DE DATOS POSTGRESQL ---
// Reemplaza estos valores con los de tu servidor de base de datos.
include_once("../../../CONNECTION/conexion.php");
session_start();      // tu contraseña

// --- CONEXIÓN A LA BASE DE DATOS ---
// Se crea la cadena de conexión para PostgreSQL
$conn_string = "host={$host} port={$port} dbname={$dbname} user={$db_user} password={$db_pass}";

// Se establece la conexión usando pg_connect
$conn = pg_connect($conn_string);

// Verificar si la conexión fue exitosa
if (!$conn) {
    // En un entorno de producción, sería mejor registrar este error que mostrarlo.
    die("Error de conexión. Por favor, inténtelo más tarde.");
}

// Establecer el charset a UTF-8 para manejar tildes y caracteres especiales
pg_set_client_encoding($conn, "UTF8");


// --- LECTURA Y AGRUPACIÓN DE DATOS ---
// Se crea un array para agrupar las promociones por cada día de la semana.
$promocionesPorDia = [
    "Lunes" => [],
    "Martes" => [],
    "Miércoles" => [],
    "Jueves" => [],
    "Viernes" => [],
    "Sábado" => [],
    "Domingo" => []
];

// Se obtienen todas las promociones de la base de datos.
// Nota: En PostgreSQL es buena práctica usar comillas dobles para tablas y columnas si contienen mayúsculas.
$sql = 'SELECT "nombrepromocion", "descripcionpromocion", "diapromocion" FROM "promocionesdiarias"';
$result = pg_query($conn, $sql);

$hayPromociones = false;
if ($result && pg_num_rows($result) > 0) {
    $hayPromociones = true;
    // Se itera sobre los resultados y se agrupan por día.
    while($row = pg_fetch_assoc($result)) {
        if (array_key_exists($row['diapromocion'], $promocionesPorDia)) {
            $promocionesPorDia[$row['diapromocion']][] = $row;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promociones de la Semana - CineMax</title>
    <!-- Se utiliza Tailwind CSS para un diseño moderno y responsivo -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
    </style>
</head>
<body class="bg-gray-900 text-white">

    <div class="container mx-auto p-4 md:p-8">
        <header class="text-center mb-10">
            <h1 class="text-4xl md:text-5xl font-bold text-yellow-400 mb-2">Promociones de la Semana</h1>
            <p class="text-lg text-gray-300">¡Promociones canjeables en TODOS nuestros cines!</p>
        </header>

        <!-- Contenedor de las tarjetas de promociones -->
        <main class="card-container">
            <?php
            // Se itera sobre el array de días para mostrar las tarjetas
            foreach ($promocionesPorDia as $dia => $promociones) :
                // Solo se muestra la tarjeta si el día tiene al menos una promoción
                if (!empty($promociones)) :
            ?>
                <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                    <div class="p-5">
                        <h2 class="text-2xl font-bold text-yellow-400 border-b-2 border-yellow-500 pb-2 mb-4">
                            <?php echo $dia; ?>
                        </h2>
                        <div class="space-y-4">
                            <?php 
                            // Se itera sobre las promociones del día
                            foreach ($promociones as $promo) : 
                            ?>
                                <div class="bg-gray-700 p-4 rounded-lg">
                                    <h3 class="font-semibold text-lg text-white"><?php echo htmlspecialchars($promo['nombrepromocion']); ?></h3>
                                    <p class="text-gray-300 text-sm"><?php echo htmlspecialchars($promo['descripcionpromocion']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php 
                endif;
            endforeach; 
            ?>
        </main>

        <?php 
        // Si no se encontró ninguna promoción en la base de datos, se muestra un mensaje.
        if (!$hayPromociones) : 
        ?>
            <div class="text-center bg-gray-800 p-8 rounded-lg shadow-lg mt-8 max-w-md mx-auto">
                <h2 class="text-2xl font-semibold text-yellow-400">¡Vuelve pronto!</h2>
                <p class="text-gray-300 mt-2">Actualmente no tenemos promociones disponibles. Por favor, revisa esta página más tarde.</p>
            </div>
        <?php endif; ?>

    </div>

    <footer class="text-center p-4 mt-8 text-gray-500 text-sm">
        <p>&copy; <?php echo date("Y"); ?> Cines. Todos los derechos reservados.</p>
    </footer>

</body>
</html>

<?php
// --- CIERRE DE LA CONEXIÓN ---
// Es una buena práctica cerrar la conexión cuando el script ha terminado.
pg_close($conn);
?>
