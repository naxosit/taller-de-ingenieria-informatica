<?php
include_once("../../../../CONNECTION/conexion.php");

if (isset($_GET['idFuncion'])) {
    $idFuncion = (int)$_GET['idFuncion'];

    try {
        $stmt = $conn->prepare("DELETE FROM Funcion WHERE idFuncion = ?");
        $stmt->execute([$idFuncion]);

        if ($stmt->rowCount() > 0){
            header("Location: ../../Funciones.php?eliminado=1");
        } else {
            header("Location: ../../Funciones.php?error=1&mensaje=" . urlencode("No se encontró la función a eliminar."));
        }
        exit;   
    } catch (PDOException $e) {
        header("Location: ../../Funciones.php?error=1&mensaje=" . urlencode("Error al eliminar: " . $e->getMessage()));
        exit;
    }
} else {
    header("Location: ../../Funciones.php?error=1&mensaje=" . urlencode("Parámetro idFuncion faltante."));
    exit;
}
?>
