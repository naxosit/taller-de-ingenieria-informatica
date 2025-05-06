<?php
require_once 'conexion.php';



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_pelicula'])) {
    $id_pelicula = $_POST['id_pelicula'];
    
    try {
        // Primero verificamos si la película existe
        $query = "SELECT nombre FROM pelicula WHERE id_pelicula = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id_pelicula, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            header('Location: ../eliminarpelicula.php?mensaje=La+película+no+existe&error=1');
            exit;
        }
        
        $pelicula = $stmt->fetch(PDO::FETCH_ASSOC);
        $nombre_pelicula = $pelicula['nombre'];
        
        // Procedemos a eliminar
        $query = "DELETE FROM pelicula WHERE id_pelicula = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id_pelicula, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            header('Location: /web/taller-de-ingenieria-informatica/WEB/PHP/Enc_cartelera/vista_encargado.php?mensaje=Película+"'.urlencode($nombre_pelicula).'" eliminada+correctamente');


        } else {
            header('Location: ../eliminarpelicula.php?mensaje=Error+al+eliminar+la+película&error=1');
        }
        
    } catch (PDOException $e) {
        error_log('Error al eliminar película: ' . $e->getMessage());
        header('Location: ../eliminarpelicula.php?mensaje=Error+de+base+de+datos&error=1');
    }
} else {
    header('Location: ../eliminarpelicula.php');
}
?>