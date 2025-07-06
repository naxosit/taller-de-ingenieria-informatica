<?php
include_once("../../../../CONNECTION/conexion.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM Cine WHERE idCine = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0){
            header("Location: ../../Cines.php?eliminado=1");
        } else {
            header("Location: ../../Cines.php");
        }
        exit;   
    } catch (PDOException $e) {
        //Si el error es por clave foranea
        if ($e->getCode()=='23503'){
            header("Location: ../../Cines.php?error=asociacion_funcion");
        } else {
            //Para otro tipo de errores
            header("Location: ../../Cines.php");
        }
        exit;
    }
} else {
    header("Location: ../../Cines.php");
    exit;
}
?>
