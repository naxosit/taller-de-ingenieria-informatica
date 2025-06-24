<?php
// CONNECTION/conexion.php
$host = 'localhost';
$port = '5432';
$dbname = 'BD_CINE';
$db_user = 'postgres';  
$db_pass = 'torresdiaz1811';

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