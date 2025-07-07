<?php
include_once("../../../CONNECTION/conexion.php");

if (isset($_GET['id_producto'])) {
    $id_producto = (int)$_GET['id_producto'];

    try {
        $stmt = $conn->prepare("DELETE FROM confiteria WHERE id_producto = ?");
        $stmt->execute([$id_producto]);

        if ($stmt->rowCount() > 0) {
            header("Location: ../confiteria/confiteriadmin.php?eliminado=1");
        } else {
            header("Location: ../confiteria/confiteriadmin.php?error=1&mensaje=" . urlencode("No se encontró el producto a eliminar."));
        }
        exit;   
    } catch (PDOException $e) {
        header("Location: ../confiteria/confiteriadmin.php?error=1&mensaje=" . urlencode("Error al eliminar: " . $e->getMessage()));
        exit;
    }
} else {
    header("Location: ../confiteria/confiteriadmin.php?error=1&mensaje=" . urlencode("Parámetro id_producto faltante."));
    exit;
}