<?php
require_once __DIR__ . '/../../../../CONNECTION/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPelicula = $_POST['id_pelicula'] ?? '';

    if (empty($idPelicula)) {
        header("Location: ../eliminar_pelicula.php?mensaje=" . urlencode("ID de película no proporcionado") . "&error=1");
        exit;
    }

    try {
        $conn->beginTransaction();

        // Eliminar boletos asociados
        $stmt = $conn->prepare("DELETE FROM Boleto WHERE IdPelicula = :id");
        $stmt->execute([':id' => $idPelicula]);

        // Eliminar funciones asociadas
        $stmt = $conn->prepare("DELETE FROM Funcion WHERE Id_Pelicula = :id");
        $stmt->execute([':id' => $idPelicula]);

        // Eliminar proyecciones asociadas
        $stmt = $conn->prepare("DELETE FROM Proyeccion WHERE Id_Pelicula = :id");
        $stmt->execute([':id' => $idPelicula]);

        // Finalmente, eliminar la película
        $stmt = $conn->prepare("DELETE FROM Pelicula WHERE idPelicula = :id");
        $stmt->execute([':id' => $idPelicula]);

        $conn->commit();

        header("Location: ../../vista_encargado.php?mensaje=" . urlencode("Película eliminada con éxito"));
        exit;
    } catch (PDOException $e) {
        $conn->rollBack();
        header("Location: ../eliminar_pelicula.php?mensaje=" . urlencode("Error al eliminar: " . $e->getMessage()) . "&error=1");
        exit;
    }
} else {
    header("Location: ../eliminar_pelicula.php?mensaje=" . urlencode("Acceso inválido") . "&error=1");
    exit;
}
?>
