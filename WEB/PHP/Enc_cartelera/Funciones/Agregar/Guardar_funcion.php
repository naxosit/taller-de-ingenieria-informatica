<?php
include_once("../../../../CONNECTION/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pelicula_id = $_POST['id_pelicula'];
    $sala_id = $_POST['id_sala'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    
    // Combinar fecha y hora
    $fecha_hora = $fecha . ' ' . $hora;

    // Validar formato de hora
    if (!preg_match('/^\d{2}:\d{2}$/', $hora)) {
        $params = http_build_query([
            'id_pelicula' => $pelicula_id,
            'id_sala' => $sala_id,
            'fecha' => $fecha,
            'hora' => $hora,
            'error' => 1
        ]);
        header("Location: Agregar_Funcion.php?mensaje=" . urlencode("Formato de hora inválido") . "&$params");
        exit;
    }
    
    // Validar componentes de hora
    list($horas, $minutos) = explode(':', $hora);
    $horas = (int)$horas;
    $minutos = (int)$minutos;
    
    if ($horas < 0 || $horas > 23) {
        $params = http_build_query([
            'id_pelicula' => $pelicula_id,
            'id_sala' => $sala_id,
            'fecha' => $fecha,
            'hora' => $hora,
            'error' => 1
        ]);
        header("Location: Agregar_Funcion.php?mensaje=" . urlencode("Hora inválida (00-23)") . "&$params");
        exit;
    }
    
    if ($minutos < 0 || $minutos > 59) {
        $params = http_build_query([
            'id_pelicula' => $pelicula_id,
            'id_sala' => $sala_id,
            'fecha' => $fecha,
            'hora' => $hora,
            'error' => 1
        ]);
        header("Location: Agregar_Funcion.php?mensaje=" . urlencode("Minutos inválidos (00-59)") . "&$params");
        exit;
    }

    // Validar que la fecha sea futura
    $fechaHoraIngresada = new DateTime($fecha_hora);
    $fechaHoraActual = new DateTime();

    if ($fechaHoraIngresada <= $fechaHoraActual) {
        $params = http_build_query([
            'id_pelicula' => $pelicula_id,
            'id_sala' => $sala_id,
            'fecha' => $fecha,
            'hora' => $hora,
            'error' => 1
        ]);
        header("Location: Agregar_Funcion.php?mensaje=" . urlencode("La fecha y hora deben ser futuras") . "&$params");
        exit;
    }

    try {
        // Insertar función
        $stmt = $conn->prepare("
            INSERT INTO Funcion (Id_Pelicula, Id_Sala, FechaHora)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$pelicula_id, $sala_id, $fecha_hora]);

        header("Location: ../../Funciones.php?mensaje=" . urlencode("Función agregada correctamente."));
        exit;
    } catch (PDOException $e) {
        $params = http_build_query([
            'id_pelicula' => $pelicula_id,
            'id_sala' => $sala_id,
            'fecha' => $fecha,
            'hora' => $hora,
            'error' => 1
        ]);
        
        if ($e->getCode() == 23000) {
            $msg = "Ya existe una función con esa película, sala y hora.";
        } else {
            $msg = "Error al guardar: " . $e->getMessage();
        }

        header("Location: Agregar_Funcion.php?mensaje=" . urlencode($msg) . "&$params");
        exit;
    }
} else {
    header("Location: ../../Funciones.php?mensaje=" . urlencode("Acceso no permitido.") . "&error=1");
    exit;
}