<?php
// cargar_sala.php
include_once("../../../../CONNECTION/conexion.php");

function cargarCiudad() {
    global $conn;
    try {
        // Verificar si la tabla existe
        $tableExists = $conn->query("SELECT EXISTS (
            SELECT FROM information_schema.tables 
            WHERE table_name = 'ciudad'
        )")->fetchColumn();
        
        if (!$tableExists) {
            throw new Exception("La tabla Ciudad no existe");
        }

        // Obtener cines
        $query = 'SELECT idCiudad as id, NombreCiudad as nombre FROM Ciudad ORDER BY NombreCiudad';
        $stmt = $conn->query($query);
        
        if ($stmt === false) {
            throw new Exception("Error en la consulta SQL");
        }
        
        $ciudades = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($ciudades)) {
            error_log("Advertencia: La tabla Ciudad está vacía");
        }
        
        return $ciudades;
    } catch (PDOException $e) {
        error_log("Error PDO al cargar ciudades: " . $e->getMessage());
        return [];
    } catch (Exception $e) {
        error_log("Error al cargar ciudades: " . $e->getMessage());
        return [];
    }
}
?>