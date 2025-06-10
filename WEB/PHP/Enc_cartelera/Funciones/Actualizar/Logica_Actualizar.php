<?php
include_once("../../../../CONNECTION/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibimos el idFuncion y la nueva fecha/hora
    $idFuncion = $_POST['idFuncion'] ?? '';
    $fechahora_nueva = $_POST['fechahora_nueva'] ?? '';

    // Validamos los datos
    if (empty($idFuncion) || empty($fechahora_nueva)) {
        die("Faltan datos obligatorios.");
    }

    // Convertir nueva fecha/hora al formato correcto para SQL TIMESTAMP
    $fechahora_nueva = date('Y-m-d H:i:s', strtotime($fechahora_nueva));

    try {
        $query = "UPDATE Funcion 
                  SET FechaHora = :nueva_fecha
                  WHERE idFuncion = :idFuncion";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nueva_fecha', $fechahora_nueva);
        $stmt->bindParam(':idFuncion', $idFuncion, PDO::PARAM_INT);

        $stmt->execute();

        header("Location: ../../Funciones.php?actualizado=1");
        exit;
    } catch (PDOException $e) {
        die("Error al actualizar función: " . htmlspecialchars($e->getMessage()));
    }
} else {
    header("Location: ../../Funciones.php");
    exit;
}
