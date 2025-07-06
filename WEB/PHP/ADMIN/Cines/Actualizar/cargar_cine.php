<?php
include_once("../../../../CONNECTION/conexion.php");

function cargarCine($idCine) {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT idCine, Nombre_cine, correo_cine, telefono, ubicacion, idCiudad FROM Cine WHERE idCine = :idCine");
        $stmt->bindParam(':idCine', $idCine, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}
?>