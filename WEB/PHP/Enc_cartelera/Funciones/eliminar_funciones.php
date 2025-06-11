<?php
include_once("../../../CONNECTION/conexion.php");

try {
    $sql = "DELETE FROM Funcion WHERE FechaHora <= NOW()";
    $conn->exec($sql);

    echo "Funciones eliminadas correctamente.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
