<?php
include_once("/../../../CONNECTION/conexion.php");

// Verificar si se ha pasado un ID en la URL
if (isset($_GET['id'])) {
    $id_pelicula = $_GET['id'];

    // Cargar los datos de la película
    $pelicula = cargarPelicula($id_pelicula);
} else {
    // Si no se pasa el ID, redirigir o mostrar un error
    echo "ID de película no válido.";
    exit;
}

// Función para cargar los datos de la película por ID
function cargarPelicula($id_pelicula) {
    global $conn; // Aseguramos que $conn esté disponible dentro de la función

    try {
        $query = 'SELECT 
            p.idPelicula as id, 
            p.Nombre as nombre, 
            p.Duracion as duracion, 
            p.Director as director, 
            p.Genero as genero,
            p.Sinopsis as sinopsis,
            pr.Id_Cine as id_cine
          FROM Pelicula p
          JOIN Proyeccion pr ON p.idPelicula = pr.Id_Pelicula
          WHERE p.idPelicula = :id';

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id_pelicula, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Devuelve los datos de la película
    } catch (PDOException $e) {
        // Si ocurre un error, se maneja aquí
        echo "Error: " . $e->getMessage();
        return false;
    }
}
?>
