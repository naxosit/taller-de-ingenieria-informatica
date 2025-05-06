<?php
require_once 'conexion.php';

try {
    $query = "SELECT idpelicula, nombre FROM pelicula ORDER BY nombre ASC";
    $stmt = $conn->query($query);
    
    if ($stmt->rowCount() > 0) {
        while ($pelicula = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='".htmlspecialchars($pelicula['idpelicula'], ENT_QUOTES)."'>"
                .htmlspecialchars($pelicula['nombre'])
                ."</option>";
        }
    } else {
        echo "<option value='' disabled>No hay películas registradas</option>";
    }
    
} catch (PDOException $e) {
    error_log('Error al cargar películas: ' . $e->getMessage());
    echo "<option value='' disabled>Error al cargar películas</option>";
}
?>