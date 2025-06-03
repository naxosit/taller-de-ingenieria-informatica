<?php
// cargar_pelicula.php
include_once("../../../CONNECTION/conexion.php");

function cargarPelicula($id_pelicula) {
    global $conn;
    try {
        $query = 'SELECT 
            p.idPelicula as id, 
            p.Nombre as nombre, 
            p.Duracion as duracion, 
            p.Director as director, 
            p.Genero as genero,
            p.Sinopsis as sinopsis,
            p.Imagen as imagen
          FROM Pelicula p
          WHERE p.idPelicula = :id';

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id_pelicula, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}
?>
