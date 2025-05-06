<?php
require_once __DIR__ . '/../../../../CONNECTION/conexion.php'; // asegúrate de que este archivo exista y tenga la conexión como $conn

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $duracion = $_POST['duracion'] ?? '';
    $sinopsis = $_POST['sinopsis'] ?? '';
    $director = $_POST['director'] ?? '';
    $genero = $_POST['genero'] ?? '';
    $idCine = $_POST['id_cine'] ?? '';

    if (empty($idCine)) {
        header("Location: ../agregar_pelicula.php?mensaje=" . urlencode("Debe seleccionar un cine") . "&error=1");
        exit;
    }

    try {
        // Iniciar transacción
        $conn->beginTransaction();

        // Insertar película
        $sqlPelicula = "INSERT INTO Pelicula (Nombre, Duracion, Sinopsis, Director, Genero)
                        VALUES (:nombre, :duracion, :sinopsis, :director, :genero)
                        RETURNING idPelicula";
        $stmtPelicula = $conn->prepare($sqlPelicula);
        $stmtPelicula->execute([
            ':nombre' => $nombre,
            ':duracion' => $duracion,
            ':sinopsis' => $sinopsis,
            ':director' => $director,
            ':genero' => $genero
        ]);

        $idPelicula = $stmtPelicula->fetchColumn(); // Obtener el ID insertado

        // Insertar en Proyeccion
        $sqlProyeccion = "INSERT INTO Proyeccion (Id_Pelicula, Id_Cine) VALUES (:idPelicula, :idCine)";
        $stmtProyeccion = $conn->prepare($sqlProyeccion);
        $stmtProyeccion->execute([
            ':idPelicula' => $idPelicula,
            ':idCine' => $idCine
        ]);

        // Confirmar transacción
        $conn->commit();

        header("Location: ../agregar_pelicula.php?mensaje=" . urlencode("Película guardada con éxito"));
        exit;
    } catch (PDOException $e) {
        $conn->rollBack();
        header("Location: ../agregar_pelicula.php?mensaje=" . urlencode("Error al guardar: " . $e->getMessage()) . "&error=1");
        exit;
    }
} else {
    header("Location: ../agregar_pelicula.php?mensaje=" . urlencode("Acceso inválido") . "&error=1");
    exit;
}
?>
