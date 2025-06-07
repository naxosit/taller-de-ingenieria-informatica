<?php
require_once __DIR__ . '/../../../CONNECTION/conexion.php';

// Verificar que se haya proporcionado un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../vista_salas.php?error=ID_de_sala_no_proporcionado");
    exit;
}

$idSala = $_GET['id'];

try {
    // Verificar si hay funciones asociadas primero
    $stmt = $conn->prepare("SELECT COUNT(*) FROM funcion WHERE id_sala = :id");
    $stmt->bindParam(':id', $idSala, PDO::PARAM_INT);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        header("Location: ../vista_salas.php?error=asociacion_funcion");
        exit;
    }

    // Si no hay funciones asociadas, proceder con la eliminación
    $stmt = $conn->prepare("DELETE FROM Sala WHERE idSala = :id");
    $stmt->bindParam(':id', $idSala, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        header("Location: ../vista_salas.php?success=Sala_eliminada_correctamente");
    } else {
        header("Location: ../vista_salas.php?error=Error_al_eliminar_la_sala");
    }
    exit;
} catch (PDOException $e) {
    // Capturar específicamente el error de violación de clave foránea (PostgreSQL)
    if ($e->getCode() == '23503') {
        header("Location: ../vista_salas.php?error=asociacion_funcion");
    } else {
        header("Location: ../vista_salas.php?error=" . urlencode($e->getMessage()));
    }
    exit;
}
?>