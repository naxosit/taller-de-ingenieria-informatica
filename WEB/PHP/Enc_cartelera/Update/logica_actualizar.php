<?php
include_once("../../../CONNECTION/conexion.php");
echo '<pre>';
var_dump($_POST);
echo '</pre>';
// die();  // Descomenta si quieres parar aquí y ver el volcado


if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    //Tomamos los datos de los inputs
    $id = $_POST['id'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $duracion = $_POST['duracion'] ?? '';
    $sinopsis = $_POST['sinopsis'] ?? '';
    $director = $_POST['director'] ?? '';
    $genero = $_POST['genero'] ?? '';
    $id_cine = $_POST['id_cine'] ?? '';

    //Validamos los datos.

    
    if (
        empty($id)       ||
        empty($nombre)   ||
        empty($duracion) ||
        empty($sinopsis) ||
        empty($director) ||
        empty($genero)   
    ) {
        die("Faltan datos obligatorios.");
    }

    try {
        //Actualizamos los datos de la tabla "Pelicula"
        $sqlPeli = "UPDATE Pelicula SET nombre = ?, duracion = ?, sinopsis = ?, director = ?, genero = ? WHERE idPelicula = ? ";
        $stmtPeli = $conn -> prepare($sqlPeli);
        $stmtPeli -> execute([$nombre, $duracion, $sinopsis, $director, $genero, $id]);

        //Redirijimos de vuela al listado de peliculas con indicar de éxito.
        header("Location: Actualizar_pelicula.php?actualizado=1");
        exit;

    } catch (PDOException $e) {
        die("Error en la actualización: " . htmlspecialchars($e -> getMessage()));
    }
} else {
    header("Location: Actualizar_pelicula.php");
    exit;
}