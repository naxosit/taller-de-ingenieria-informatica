<?php
include_once("../../../CONNECTION/conexion.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM Pelicula WHERE idPelicula = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0){
            header("Location: ../vista_encargado.php?eliminado=1");
        } else {
            header("Location: ../vista_encargado.php");
        }
        exit;   
    } catch (PDOException $e) {
        //Si el error es por clave foranea
        if ($e->getCode()=='23503'){
            header("Location: ../vista_encargado.php?error=asociacion_funcion");
        } else {
            //Para otro tipo de errores
            header("Location: ../vista_encargado.php");
        }
        exit;
    }
} else {
    header("Location: ../vista_encargado.php");
    exit;
}
?>
