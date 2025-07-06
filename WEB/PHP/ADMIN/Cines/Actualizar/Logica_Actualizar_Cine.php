<?php
session_start();
require_once __DIR__ . '/../../../../CONNECTION/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../Cines.php?error=" . urlencode("Método no permitido"));
    exit;
}

$campos = [
    'idCine' => trim($_POST['idCine'] ?? ''),
    'nombre' => trim($_POST['nombre'] ?? ''),
    'correo' => trim($_POST['correo'] ?? ''),
    'telefono' => trim($_POST['telefono'] ?? ''),
    'ubicacion' => trim($_POST['ubicacion'] ?? ''),
    'ciudad_id' => trim($_POST['ciudad_id'] ?? '')
];

$errores = [];

foreach ($campos as $campo => $valor) {
    if ($campo !== 'idCine' && $valor === '') {
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
    elseif ($campo !== 'idCine' && preg_match('/^\s+$/', $valor)) {
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

if (!empty($campos['telefono']) && !preg_match('/^\d+$/', $campos['telefono'])) {
    $errores[] = "El teléfono debe contener solo números";
}

if (!empty($campos['ciudad_id']) && !is_numeric($campos['ciudad_id'])) {
    $errores[] = "El ID de la ciudad no es válido";
}

if (!empty($errores)) {
    $_SESSION['form_data'] = $campos;
    $mensaje_error = implode("<br>", $errores);
    header("Location: Actualizar_Cine.php?idCine=" . $campos['idCine'] . "&error=" . urlencode($mensaje_error));
    exit;
}

try {
    $stmt = $conn->prepare("SELECT idCiudad FROM Ciudad WHERE idCiudad = :ciudad_id");
    $stmt->bindParam(':ciudad_id', $campos['ciudad_id'], PDO::PARAM_INT);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        throw new Exception("La ciudad seleccionada no existe");
    }

    $sql = "UPDATE Cine 
            SET Nombre_cine = :nombre, 
                correo_cine = :correo, 
                telefono = :telefono, 
                ubicacion = :ubicacion, 
                idCiudad = :ciudad_id
            WHERE idCine = :idCine";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre', $campos['nombre'], PDO::PARAM_STR);
    $stmt->bindParam(':correo', $campos['correo'], PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $campos['telefono'], PDO::PARAM_INT);
    $stmt->bindParam(':ubicacion', $campos['ubicacion'], PDO::PARAM_STR);
    $stmt->bindParam(':ciudad_id', $campos['ciudad_id'], PDO::PARAM_INT);
    $stmt->bindParam(':idCine', $campos['idCine'], PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        if (isset($_SESSION['form_data'])) {
            unset($_SESSION['form_data']);
        }
        header("Location: ../../Cines.php?success=" . urlencode("Cine actualizado correctamente"));
        exit;
    } else {
        throw new Exception("Error al actualizar el cine");
    }
} catch (PDOException $e) {
    $_SESSION['form_data'] = $campos;
    $mensaje_error = "Error de base de datos: " . $e->getMessage();
    header("Location: Actualizar_Cine.php?idCine=" . $campos['idCine'] . "&error=" . urlencode($mensaje_error));
    exit;
} catch (Exception $e) {
    $_SESSION['form_data'] = $campos;
    header("Location: Actualizar_Cine.php?idCine=" . $campos['idCine'] . "&error=" . urlencode($e->getMessage()));
    exit;
}