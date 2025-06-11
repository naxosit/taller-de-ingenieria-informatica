<?php
include_once("../../../../CONNECTION/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pelicula_id = $_POST['id_pelicula'];
    $sala_id = $_POST['id_sala'];
    $fecha_hora = $_POST['fecha_hora'];

    if (empty($pelicula_id) || empty($sala_id) || empty($fecha_hora)) {
        header("Location: Agregar_Funcion.php?mensaje=" . urlencode("Todos los campos son obligatorios.") . "&error=1");
        exit;
    }

    //Validar que la fecha y hora  no estén en el pasado
    $fechaHoraIngresada = new DateTime($fecha_hora);
    $fechaHoraActual = new DateTime();

    if ($fechaHoraIngresada < $fechaHoraActual){
        header("Location: Agregar_Funcion.php?mensaje=".urldecode("La fecha y hora deben ser futuras.") . "&error=1");
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
        // Si el error es por clave duplicada
        if ($e->getCode() == 23000) {
            $msg = "Ya existe una función con esa película, sala y hora.";
        } else {
            $msg = "Error al guardar: " . $e->getMessage();
        }

        header("Location: ../../Funciones.php?mensaje=" . urlencode($msg) . "&error=1");
        exit;
    }
} else {
    header("Location: ../../Funciones.php?mensaje=" . urlencode("Acceso no permitido.") . "&error=1");
    exit;
}
