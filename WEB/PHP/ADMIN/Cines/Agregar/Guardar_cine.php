<?php
session_start(); // Iniciar sesión para mantener datos
require_once __DIR__ . '/../../../../CONNECTION/conexion.php';

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: Agregar_Cine.php?error=" . urlencode("Método no permitido"));
    exit;
}

// Recoger y limpiar los datos del formulario
$campos = [
    'nombre' => trim($_POST['nombre'] ?? ''),
    'correo' => trim($_POST['correo'] ?? ''),
    'telefono' => trim($_POST['telefono'] ?? ''),
    'ubicacion' => trim($_POST['ubicacion'] ?? ''),
    'ciudad_id' => trim($_POST['ciudad_id'] ?? '')
];

// Validar los campos
$errores = [];

foreach ($campos as $campo => $valor) {
    if ($valor === '') {
        $nombreCampo = match($campo) {
            'nombre' => 'Nombre del cine',
            'correo' => 'Correo',
            'telefono' => 'Teléfono',
            'ubicacion' => 'Ubicación',
            'ciudad_id' => 'Ciudad',
            default => 'Campo'
        };
        $errores[] = "El campo '$nombreCampo' es obligatorio";
    }
    // Validar campos que solo contienen espacios
    elseif (preg_match('/^\s+$/', $valor)) {
        $nombreCampo = match($campo) {
            'nombre' => 'Nombre del cine',
            'correo' => 'Correo',
            'telefono' => 'Teléfono',
            'ubicacion' => 'Ubicación',
            default => 'Campo'
        };
        $errores[] = "El campo '$nombreCampo' no puede contener solo espacios";
    }
}

// Validación específica para teléfono
if (!empty($campos['telefono']) && !preg_match('/^\d+$/', $campos['telefono'])) {
    $errores[] = "El teléfono debe contener solo números";
}

// Validación específica para ciudad
if (!empty($campos['ciudad_id']) && !is_numeric($campos['ciudad_id'])) {
    $errores[] = "El ID de la ciudad no es válido";
}

// Si hay errores, guardar datos y redirigir
if (!empty($errores)) {
    $_SESSION['form_data'] = $campos;
    $mensaje_error = implode("<br>", $errores);
    header("Location: Agregar_Cine.php?error=" . urlencode($mensaje_error));
    exit;
}

try {
    // Verificar que la ciudad existe
    $stmt = $conn->prepare("SELECT idCiudad FROM Ciudad WHERE idCiudad = :ciudad_id");
    $stmt->bindParam(':ciudad_id', $campos['ciudad_id'], PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("La ciudad seleccionada no existe");
    }

    // Preparar la consulta SQL para insertar el cine
    $sql = "INSERT INTO Cine (Nombre_cine, correo_cine, telefono, ubicacion, idCiudad) 
            VALUES (:nombre, :correo, :telefono, :ubicacion, :ciudad_id)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre', $campos['nombre'], PDO::PARAM_STR);
    $stmt->bindParam(':correo', $campos['correo'], PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $campos['telefono'], PDO::PARAM_INT);
    $stmt->bindParam(':ubicacion', $campos['ubicacion'], PDO::PARAM_STR);
    $stmt->bindParam(':ciudad_id', $campos['ciudad_id'], PDO::PARAM_INT);
    
    // Ejecutar la inserción
    if ($stmt->execute()) {
        // Limpiar datos de formulario guardados
        if (isset($_SESSION['form_data'])) {
            unset($_SESSION['form_data']);
        }
        header("Location: ../../Cines.php?success=" . urlencode("Cine agregado correctamente"));
        exit;
    } else {
        throw new Exception("Error al guardar el cine en la base de datos");
    }
} catch (PDOException $e) {
    $_SESSION['form_data'] = $campos;
    $mensaje_error = "Error de base de datos: " . $e->getMessage();
    header("Location: Agregar_Cine.php?error=" . urlencode($mensaje_error));
    exit;
} catch (Exception $e) {
    $_SESSION['form_data'] = $campos;
    header("Location: Agregar_Cine.php?error=" . urlencode($e->getMessage()));
    exit;
}