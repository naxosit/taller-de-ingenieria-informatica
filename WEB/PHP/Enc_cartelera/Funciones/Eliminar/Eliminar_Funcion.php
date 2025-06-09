<?php
include_once("../../../../CONNECTION/conexion.php");

if (isset($_GET['idPelicula'], $_GET['idSala'], $_GET['fechaHora'])) {
    $idPelicula = (int)$_GET['idPelicula'];
    $idSala = (int)$_GET['idSala'];
    $fechaHora = $_GET['fechaHora'];

    try {
        $stmt = $conn->prepare("DELETE FROM Funcion WHERE Id_Pelicula = ? AND Id_Sala = ? AND fechaHora = ?");
        $stmt->execute([$idPelicula, $idSala, $fechaHora]);

        if ($stmt->rowCount() > 0){
            header("Location: Eliminar_Funcion.php?eliminado=1");
        } else {
            header("Location: Eliminar_Funcion.php");
        }
        exit;   
    } catch (PDOException $e) {
        //Redireccion en caso de error
        header("Location: Eliminar_Funcion.php?error=1");
        exit;
    }
} else {
    header("Location: ../../Funciones.php");
    exit;
}
?>
