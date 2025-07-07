<?php
require_once __DIR__ . '/../../../../CONNECTION/conexion.php';

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

    // Iniciar transacción
    $conn->beginTransaction();

    // Insertar la sala
    $sql = "INSERT INTO Sala (Nombre, Tipo_pantalla, Cine_idCine) 
            VALUES (:nombre, :tipo_pantalla, :cine_id) 
            RETURNING idSala"; // Usar RETURNING para obtener el ID
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindParam(':tipo_pantalla', $tipo_pantalla, PDO::PARAM_STR);
    $stmt->bindParam(':cine_id', $cine_id, PDO::PARAM_INT);
    $stmt->execute();
    
    // Obtener el ID de la sala insertada
    $sala = $stmt->fetch(PDO::FETCH_ASSOC);
    $sala_id = $sala['idsala'];
    
    if (!$sala_id) {
        throw new Exception("Error al obtener ID de la nueva sala");
    }

    // Insertar butacas (5 filas A-E, 8 columnas 1-8)
    $filas = ['A', 'B', 'C', 'D', 'E'];
    $id_tipo_butaca = 1; // Valor por defecto, ajustar si es necesario
    
    $stmt_butaca = $conn->prepare("
        INSERT INTO Butaca (Id_TipoButaca, Id_Sala, Fila, Columna)
        VALUES (:id_tipo_butaca, :id_sala, :fila, :columna)
    ");
    
    foreach ($filas as $fila) {
        for ($columna = 1; $columna <= 8; $columna++) {
            $stmt_butaca->bindValue(':id_tipo_butaca', $id_tipo_butaca, PDO::PARAM_INT);
            $stmt_butaca->bindValue(':id_sala', $sala_id, PDO::PARAM_INT);
            $stmt_butaca->bindValue(':fila', $fila, PDO::PARAM_STR);
            $stmt_butaca->bindValue(':columna', $columna, PDO::PARAM_INT);
            $stmt_butaca->execute();
        }
    }

    // Confirmar transacción
    $conn->commit();

    // Redirigir a la lista de salas con mensaje de éxito
    header("Location: ../../Salas.php?success=" . urlencode("Sala agregada correctamente"));
    exit;
    
} catch (PDOException $e) {
    // Si hay error, deshacer transacción
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    $mensaje_error = "Error de base de datos: " . $e->getMessage();
    header("Location: Agregar_sala.php?error=" . urlencode($mensaje_error));
    exit;
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    header("Location: Agregar_sala.php?error=" . urlencode($e->getMessage()));
    exit;
}