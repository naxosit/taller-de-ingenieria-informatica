<?php
require_once __DIR__ . '/../../../CONNECTION/conexion.php'; // Asegúrate de que $conn es una instancia válida de PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $duracion = trim($_POST['duracion'] ?? '');
    $sinopsis = trim($_POST['sinopsis'] ?? '');
    $director = trim($_POST['director'] ?? '');
    $genero = trim($_POST['genero'] ?? '');
    $imagen = trim($_POST['imagen'] ?? '');

    if (empty($nombre) || empty($duracion)) {
        header("Location: Agregar_pelicula.php?mensaje=" . urlencode("Nombre y duración son obligatorios") . "&error=1");
        exit;
    }

    try {
        $sqlPelicula = "INSERT INTO Pelicula (Nombre, Duracion, Sinopsis, Director, Genero, Imagen)
                        VALUES (:nombre, :duracion, :sinopsis, :director, :genero, :imagen)";
        $stmt = $conn->prepare($sqlPelicula);
        $stmt->execute([
            ':nombre' => $nombre,
            ':duracion' => $duracion,
            ':sinopsis' => $sinopsis,
            ':director' => $director,
            ':genero' => $genero,
            ':imagen' => $imagen
        ]);

        header("Location: ../vista_encargado.php?mensaje=" . urlencode("Película guardada con éxito"));
        exit;
    } catch (PDOException $e) {
        header("Location: ../vista_encargado.php?mensaje=" . urlencode("Error al guardar: " . $e->getMessage()) . "&error=1");
        exit;
    }
} else {
    header("Location: ../vista_encargado.php?mensaje=" . urlencode("Acceso inválido") . "&error=1");
    exit;
}
?>
