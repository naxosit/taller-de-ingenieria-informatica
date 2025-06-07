<?php
require_once __DIR__ . '/../../../CONNECTION/conexion.php'; // Asegúrate de que $conn es una instancia válida de PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $duracion = $_POST['duracion'] ?? '';
    $sinopsis = $_POST['sinopsis'] ?? '';
    $director = $_POST['director'] ?? '';
    $genero = $_POST['genero'] ?? '';
    $imagen = $_POST['imagen'] ?? '';

    if (empty($nombre) || empty($duracion)) {
        header("Location: ../agregar_pelicula.php?mensaje=" . urlencode("Nombre y duración son obligatorios") . "&error=1");
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
        header("Location: ../agregar_pelicula.php?mensaje=" . urlencode("Error al guardar: " . $e->getMessage()) . "&error=1");
        exit;
    }
} else {
    header("Location: ../agregar_pelicula.php?mensaje=" . urlencode("Acceso inválido") . "&error=1");
    exit;
}
?>
