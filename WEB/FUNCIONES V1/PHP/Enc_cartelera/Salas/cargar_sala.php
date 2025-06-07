<?php
// cargar_sala.php
include_once("../../../CONNECTION/conexion.php");

function cargarSala($id_sala) {
    global $conn;
    try {
        $query = 'SELECT 
                s.idSala as id, 
                s.Nombre as nombre, 
                s.Tipo_pantalla as tipo_pantalla,
                s.Cine_idCine as cine_id,
                c.Nombre_cine as nombre_cine
            FROM Sala s
            JOIN Cine c ON s.Cine_idCine = c.idCine
            WHERE s.idSala = :id';

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id_sala, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al cargar sala: " . $e->getMessage());
        return false;
    }
}

function cargarCines() {
    global $conn;
    try {
        // Verificar si la tabla existe
        $tableExists = $conn->query("SELECT EXISTS (
            SELECT FROM information_schema.tables 
            WHERE table_name = 'cine'
        )")->fetchColumn();
        
        if (!$tableExists) {
            throw new Exception("La tabla Cine no existe");
        }

        // Obtener cines
        $query = 'SELECT idCine as id, Nombre_cine as nombre FROM Cine ORDER BY Nombre_cine';
        $stmt = $conn->query($query);
        
        if ($stmt === false) {
            throw new Exception("Error en la consulta SQL");
        }
        
        $cines = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($cines)) {
            error_log("Advertencia: La tabla Cine está vacía");
        }
        
        return $cines;
    } catch (PDOException $e) {
        error_log("Error PDO al cargar cines: " . $e->getMessage());
        return [];
    } catch (Exception $e) {
        error_log("Error al cargar cines: " . $e->getMessage());
        return [];
    }
}
?>