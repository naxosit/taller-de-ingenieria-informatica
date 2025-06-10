<?php
include_once("../../../CONNECTION/conexion.php");
echo '<pre>';
var_dump($_POST);
echo '</pre>';
// die();  // Descomenta si quieres parar aquí y ver el volcado


if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    //Tomamos los datos de los inputs
    $id = trim($_POST['id'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $duracion = trim($_POST['duracion'] ?? '');
    $sinopsis = trim($_POST['sinopsis'] ?? '');
    $director = trim($_POST['director'] ?? '');
    $genero = trim($_POST['genero'] ?? '');
    $imagen = trim($_POST['imagen'] ?? '');
    $id_cine = trim($_POST['id_cine'] ?? '');

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
        $sqlPeli = "UPDATE Pelicula SET nombre = ?, duracion = ?, sinopsis = ?, director = ?, genero = ?, imagen = ? WHERE idPelicula = ? ";
        $stmtPeli = $conn -> prepare($sqlPeli);
        $stmtPeli -> execute([$nombre, $duracion, $sinopsis, $director, $genero, $imagen, $id]);

        //Redirijimos de vuela al listado de peliculas con indicar de éxito.
        header("Location: ../vista_encargado.php?actualizado=1");
        exit;

    } catch (PDOException $e) {
        die("Error en la actualización: " . htmlspecialchars($e -> getMessage()));
    }
} else {
    header("Location: Actualizar_pelicula.php");
    exit;
}