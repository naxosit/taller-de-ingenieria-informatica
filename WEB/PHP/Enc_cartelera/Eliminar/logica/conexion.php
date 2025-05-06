<?php
// conexion.php
$host = 'localhost';
$dbname = 'CineMeyer';
$user = 'postgres';
$password = 'farrush';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn = $pdo; // <- Agregá esta línea para compatibilidad
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}
?>
