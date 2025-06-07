<?php
require_once __DIR__ . '/../../../CONNECTION/conexion.php'; // Asegúrate de que $conn es una instancia válida de PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $tipo_pantalla = $_POST['tipo_pantalla'] ?? null;
    $cine_id = $_POST['cine_id'] ?? '';

    // Validación de campos obligatorios
    if (empty($nombre) || empty($cine_id)) {
        header("Location: Agregar_sala.php?mensaje=" . urlencode("Nombre y cine son campos obligatorios") . "&error=1");
        exit;
    }

    try {
        // Validar que el cine exista
        $stmt = $conn->prepare("SELECT 1 FROM Cine WHERE idCine = :cine_id");
        $stmt->execute([':cine_id' => $cine_id]);
        if (!$stmt->fetch()) {
            throw new Exception("El cine seleccionado no existe");
        }

        // Insertar la sala
        $sqlSala = "INSERT INTO Sala (Nombre, Tipo_pantalla, Cine_idCine)
                    VALUES (:nombre, :tipo_pantalla, :cine_id)";
        
        $stmt = $conn->prepare($sqlSala);
        $stmt->execute([
            ':nombre' => $nombre,
            ':tipo_pantalla' => $tipo_pantalla ?: null,
            ':cine_id' => $cine_id
        ]);

        // Redireccionar con mensaje de éxito
        header("Location: ../vista_sala.php?mensaje=" . urlencode("Sala guardada con éxito") . "&creado=1");
        exit;
        
    } catch (PDOException $e) {
        // Manejar errores de base de datos
        $mensajeError = "Error al guardar la sala: " . $e->getMessage();
        header("Location: Agregar_sala.php?mensaje=" . urlencode($mensajeError) . "&error=1");
        exit;
    } catch (Exception $e) {
        // Manejar otras excepciones
        header("Location: Agregar_sala.php?mensaje=" . urlencode($e->getMessage()) . "&error=1");
        exit;
    }
} else {
    // Redireccionar si no es POST
    header("Location: Agregar_sala.php?mensaje=" . urlencode("Acceso inválido") . "&error=1");
    exit;
}