<?php
function cargarProducto($id_producto) {
    include_once("../../../CONNECTION/conexion.php");
    
    try {
        $stmt = $conn->prepare("SELECT * FROM confiteria WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return null;
    }
}
?>