<?php
// cargar_funcion.php
include_once("../../../../CONNECTION/conexion.php");

function cargarFuncion($id_pelicula, $id_sala, $fecha_hora) {
    global $conn;
    try {
        $query = 'SELECT 
            f.Id_Pelicula, 
            f.Id_Sala, 
            f.FechaHora, 
            p.Nombre AS nombre_pelicula,
            s.Nombre AS nombre_sala,
            c.Nombre_cine AS nombre_cine
        FROM Funcion f
        INNER JOIN Pelicula p ON f.Id_Pelicula = p.idPelicula
        INNER JOIN Sala s ON f.Id_Sala = s.idSala
        INNER JOIN Cine c ON s.Cine_idCine = c.idCine
        WHERE f.Id_Pelicula = :id_pelicula 
          AND f.Id_Sala = :id_sala 
          AND f.FechaHora = :fecha_hora';

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_pelicula', $id_pelicula, PDO::PARAM_INT);
        $stmt->bindParam(':id_sala', $id_sala, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_hora', $fecha_hora); // tipo TIMESTAMP

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}
?>
