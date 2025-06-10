<?php
// cargar_funcion.php
include_once("../../../../CONNECTION/conexion.php");

function cargarFuncion($idFuncion) {
    global $conn;
    try {
        $query = 'SELECT 
            f.idFuncion,
            f.FechaHora AS fechahora, 
            p.Nombre AS nombre_pelicula,
            s.Nombre AS nombre_sala,
            c.Nombre_cine AS nombre_cine
        FROM Funcion f
        INNER JOIN Pelicula p ON f.Id_Pelicula = p.idPelicula
        INNER JOIN Sala s ON f.Id_Sala = s.idSala
        INNER JOIN Cine c ON s.Cine_idCine = c.idCine
        WHERE f.idFuncion = :idFuncion';

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':idFuncion', $idFuncion, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}
?>
