<?php
require_once __DIR__ . '/../../../CONNECTION/conexion.php';

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: Agregar_sala.php?error=" . urlencode("Método no permitido"));
    exit;
}

// Recoger y limpiar los datos del formulario
$nombre = trim($_POST['nombre'] ?? '');
$tipo_pantalla = trim($_POST['tipo_pantalla'] ?? '');
$cine_id = trim($_POST['cine_id'] ?? '');

// Validar los campos obligatorios
$errores = [];

if (empty($nombre)) {
    $errores[] = "El nombre de la sala es obligatorio";
}

if (empty($cine_id)) {
    $errores[] = "Debe seleccionar un cine";
} elseif (!is_numeric($cine_id)) {
    $errores[] = "El ID del cine no es válido";
}

// Si hay errores, redirigir mostrándolos
if (!empty($errores)) {
    $mensaje_error = implode("<br>", $errores);
    header("Location: Agregar_sala.php?error=" . urlencode($mensaje_error));
    exit;
}

try {
    // Verificar que el cine existe
    $stmt = $conn->prepare("SELECT idCine FROM Cine WHERE idCine = :cine_id");
    $stmt->bindParam(':cine_id', $cine_id, PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("El cine seleccionado no existe");
    }

    // Preparar la consulta SQL para insertar la sala
    $sql = "INSERT INTO Sala (Nombre, Tipo_pantalla, Cine_idCine) 
            VALUES (:nombre, :tipo_pantalla, :cine_id)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindParam(':tipo_pantalla', $tipo_pantalla, PDO::PARAM_STR);
    $stmt->bindParam(':cine_id', $cine_id, PDO::PARAM_INT);
    
    // Ejecutar la inserción
    if ($stmt->execute()) {
        // Redirigir a la lista de salas con mensaje de éxito
        header("Location: ../vista_salas.php?success=" . urlencode("Sala agregada correctamente"));
        exit;
    } else {
        throw new Exception("Error al guardar la sala en la base de datos");
    }
} catch (PDOException $e) {
    // Manejar errores de base de datos
    $mensaje_error = "Error de base de datos: " . $e->getMessage();
    header("Location: Agregar_sala.php?error=" . urlencode($mensaje_error));
    exit;
} catch (Exception $e) {
    // Manejar otros errores
    header("Location: Agregar_sala.php?error=" . urlencode($e->getMessage()));
    exit;
}