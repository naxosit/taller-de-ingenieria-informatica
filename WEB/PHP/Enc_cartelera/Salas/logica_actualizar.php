<?php
include_once("../../../CONNECTION/conexion.php");

// Debug (opcional)
echo '<pre>';
var_dump($_POST);
echo '</pre>';
// die();  // Descomenta si quieres parar aquí y ver el volcado

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $id = $_POST['id'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $tipo_pantalla = $_POST['tipo_pantalla'] ?? null;
    $cine_id = $_POST['cine_id'] ?? '';

    // Validación de datos
    if (empty($id) || empty($nombre) || empty($cine_id)) {
        die("Faltan datos obligatorios: Nombre y Cine son campos requeridos.");
    }

    try {
        // Verificar que el cine existe
        $stmtCheck = $conn->prepare("SELECT 1 FROM Cine WHERE idCine = ?");
        $stmtCheck->execute([$cine_id]);
        if (!$stmtCheck->fetch()) {
            die("El cine seleccionado no existe.");
        }

        // Actualizar la sala en la base de datos
        $sqlSala = "UPDATE Sala SET 
                   Nombre = ?, 
                   Tipo_pantalla = ?, 
                   Cine_idCine = ? 
                   WHERE idSala = ?";
        
        $stmtSala = $conn->prepare($sqlSala);
        $stmtSala->execute([$nombre, $tipo_pantalla, $cine_id, $id]);

        // Verificar si se realizaron cambios
        if ($stmtSala->rowCount() > 0) {
            header("Location: ../vista_salas.php?actualizado=1");
        } else {
            // No se modificaron registros (posiblemente los datos eran iguales)
            header("Location: ../vista_salas.php?actualizado=0");
        }
        exit;

    } catch (PDOException $e) {
        // Manejo específico de errores de clave foránea
        if ($e->getCode() == '23503') {
            die("Error: No se puede actualizar - El cine seleccionado no existe.");
        } else {
            die("Error en la actualización: " . htmlspecialchars($e->getMessage()));
        }
    }
} else {
    // Si no es método POST, redirigir
    header("Location: ../vista_salas.php");
    exit;
}