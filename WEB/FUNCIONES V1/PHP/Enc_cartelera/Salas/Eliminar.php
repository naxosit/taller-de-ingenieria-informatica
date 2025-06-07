<?php
include_once("../../../CONNECTION/conexion.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Primero verificamos si hay funciones asociadas
        $stmtCheck = $conn->prepare("SELECT 1 FROM Funcion WHERE Sala_idSala = ? LIMIT 1");
        $stmtCheck->execute([$id]);
        
        if ($stmtCheck->rowCount() > 0) {
            // Hay funciones asociadas, no podemos eliminar
            header("Location: ../vista_sala.php?error=sala_ocupada");
            exit;
        }

        // Si no hay funciones asociadas, procedemos a eliminar
        $stmt = $conn->prepare("DELETE FROM Sala WHERE idSala = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            header("Location: ../vista_salas.php?eliminado=1");
        } else {
            header("Location: ../vista_salas.php?error=sala_no_encontrada");
        }
        exit;   
    } catch (PDOException $e) {
        // Manejo de errores de base de datos
        if ($e->getCode() == '23503') {
            // Error de clave foránea (aunque ya lo verificamos antes)
            header("Location: ../vista_salas.php?error=asociacion_funcion");
        } else {
            // Otros errores de base de datos
            header("Location: ../vista_salas.php?error=error_bd");
        }
        exit;
    }
} else {
    // Si no se proporcionó ID
    header("Location: ../vista_sala.php?error=id_no_proporcionado");
    exit;
}