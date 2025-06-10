<?php
include_once("../CONNECTION/conexion.php");
session_start();

// Zona horaria
date_default_timezone_set('America/Santiago');

// Obtener datos del formulario
$butacas = explode(',', $_POST['butacasSeleccionadas']);
$idPelicula = $_POST['pelicula'];
$idSala = $_POST['sala'];
$fechaInicio = $_POST['fechaInicio'];
$fechaFin = $_POST['fechaFin'];
$tipo = $_POST['tipo'];
$marca = $_POST['marca'];
$cuatroDig = $_POST['cuatroDig'];
$fechaTransf = $_POST['fecha_transf'];
$rut = $_SESSION['rut']; // Suponiendo que el usuario ya está logueado

try {
    $conn->beginTransaction();

    $boletosIds = [];

    // Insertar cada boleto
    foreach ($butacas as $idButaca) {
        // Validar si ya está ocupada en esa función
        $check = $conn->prepare("SELECT COUNT(*) FROM Boleto 
            WHERE IdButaca = :idButaca AND Fecha_inicio_boleto = :fecha AND Activo = true");
        $check->execute(['idButaca' => $idButaca, 'fecha' => $fechaInicio]);

        if ($check->fetchColumn() > 0) {
            throw new Exception("La butaca $idButaca ya está ocupada.");
        }

        // Insertar boleto
        $stmt = $conn->prepare("INSERT INTO Boleto 
            (RUT, IdPelicula, IdButaca, Estado_Butaca, Fecha_inicio_boleto, Fecha_fin_boleto, Activo)
            VALUES (:rut, :pelicula, :butaca, 'ocupada', :inicio, :fin, true)");
        $stmt->execute([
            'rut' => $rut,
            'pelicula' => $idPelicula,
            'butaca' => $idButaca,
            'inicio' => $fechaInicio,
            'fin' => $fechaFin
        ]);

        $boletosIds[] = $conn->lastInsertId();
    }

    // Insertar un pago por cada boleto (puedes adaptarlo si prefieres un solo pago agrupado)
    foreach ($boletosIds as $idBoleto) {
        $stmtPago = $conn->prepare("INSERT INTO Pago 
            (IdBoleto, Tipo, Marca, CuatroDig, Fecha_Transf)
            VALUES (:idBoleto, :tipo, :marca, :cuatroDig, :fecha)");
        $stmtPago->execute([
            'idBoleto' => $idBoleto,
            'tipo' => $tipo,
            'marca' => $marca,
            'cuatroDig' => $cuatroDig,
            'fecha' => $fechaTransf
        ]);
    }

    $conn->commit();
    echo "Compra realizada exitosamente.";

} catch (Exception $e) {
    $conn->rollBack();
    echo "Error en la compra: " . $e->getMessage();
}
?>
