<?php
// CONNECTION/conexion.php
$host = 'localhost';
$port = '5432';
$dbname = 'BD_CINE';
$db_user = 'postgres';  // Cambiado de $user a $db_user para consistencia
$db_pass = 'torresdiaz1811'; // Cambiado de $password a $db_pass

// Configuración PDO
$dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $db = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Para compatibilidad con código existente que use $conn
$conn = $db;
?>