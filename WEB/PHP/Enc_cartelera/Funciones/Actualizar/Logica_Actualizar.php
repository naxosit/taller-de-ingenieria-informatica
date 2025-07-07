<?php
include_once("../../../../CONNECTION/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibimos el idFuncion, fecha_nueva y hora_nueva
    $idFuncion = $_POST['idFuncion'] ?? '';
    $fecha_nueva = $_POST['fecha_nueva'] ?? '';
    $hora_nueva = $_POST['hora_nueva'] ?? '';

    // Validamos los datos
    if (empty($idFuncion) || empty($fecha_nueva) || empty($hora_nueva)) {
        die("Faltan datos obligatorios.");
    }

    // Validar formato de hora
    if (!preg_match('/^\d{2}:\d{2}$/', $hora_nueva)) {
        die("Formato de hora inválido.");
    }
    
    // Validar componentes de hora
    list($horas, $minutos) = explode(':', $hora_nueva);
    $horas = (int)$horas;
    $minutos = (int)$minutos;
    
    if ($horas < 0 || $horas > 23) {
        die("Hora inválida (00-23)");
    }
    
    if ($minutos < 0 || $minutos > 59) {
        die("Minutos inválidos (00-59)");
    }

    // Combinar fecha y hora
    $fechahora_nueva = $fecha_nueva . ' ' . $hora_nueva . ':00';

    // Validar que la fecha sea futura
    $fechaHoraIngresada = new DateTime($fechahora_nueva);
    $fechaHoraActual = new DateTime();

    if ($fechaHoraIngresada <= $fechaHoraActual) {
        die("La fecha y hora deben ser futuras");
    }

    try {
        $query = "UPDATE Funcion 
                  SET FechaHora = :nueva_fecha
                  WHERE idFuncion = :idFuncion";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nueva_fecha', $fechahora_nueva);
        $stmt->bindParam(':idFuncion', $idFuncion, PDO::PARAM_INT);

        $stmt->execute();

        header("Location: ../../Funciones.php?actualizado=1");
        exit;
    } catch (PDOException $e) {
        die("Error al actualizar función: " . htmlspecialchars($e->getMessage()));
    }
} else {
    header("Location: ../../Funciones.php");
    exit;
}