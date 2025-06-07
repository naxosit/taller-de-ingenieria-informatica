<?php
include_once("../../../../CONNECTION/conexion.php");

try {
    $peliculas = $conn->query("SELECT idPelicula, Nombre FROM Pelicula")->fetchAll(PDO::FETCH_ASSOC);

    $salas = $conn->query("
        SELECT Sala.idSala, Sala.Nombre AS nombre_sala, Cine.Nombre_cine 
        FROM Sala 
        INNER JOIN Cine ON Sala.Cine_idCine = Cine.idCine
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al cargar datos: " . $e->getMessage());
}
?>
