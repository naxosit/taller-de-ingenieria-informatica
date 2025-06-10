<?php
session_start(); 
include_once("../CONNECTION/conexion.php");
date_default_timezone_set('America/Santiago');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Acceso no válido.");
}
// Obtener RUT de la sesión
$rut = $_SESSION['rut'] ?? null;
if (!$rut) {
    die("Usuario no autenticado.");
}

// Recibir datos del formulario
$tipo = $_POST['tipo'] ?? '';
$marca = $_POST['marca'] ?? '';
$cuatroDig = $_POST['cuatroDig'] ?? '';
$fechaTransf = $_POST['fecha_transf'] ?? '';

$butacasSeleccionadas = $_POST['butacasSeleccionadas'] ?? '';
$idPelicula = $_POST['pelicula'] ?? '';
$idSala = $_POST['sala'] ?? '';
$fechaInicio = $_POST['fechaInicio'] ?? '';
$fechaFin = $_POST['fechaFin'] ?? '';
$idFuncion = $_POST['idFuncion'] ?? '';

if (!$tipo || !$marca || !$cuatroDig || !$fechaTransf || !$butacasSeleccionadas || !$idFuncion) {
    die("Faltan datos para procesar el pago.");
}

try {
    $conn->beginTransaction();

    $butacasArray = explode(',', $butacasSeleccionadas);
    $sqlBoleto = "INSERT INTO Boleto (RUT, IdButaca, IdPelicula, Estado_Butaca, Fecha_inicio_boleto, Fecha_fin_boleto, Activo)
                VALUES (:rut, :idButaca, :idPelicula, 'ocupada', :fechaInicio, :fechaFin, true)";
    $stmtBoleto = $conn->prepare($sqlBoleto);

    $sqlPago = "INSERT INTO Pago (Tipo, Marca, CuatroDig, Fecha_Transf, IdBoleto)
                VALUES (:tipo, :marca, :cuatroDig, :fechaTransf, :idBoleto)";
    $stmtPago = $conn->prepare($sqlPago);

    $ultimoIdPago = null; // Inicializamos

    foreach ($butacasArray as $idButaca) {
        $idButaca = (int)trim($idButaca);

        // Insertar boleto
        $stmtBoleto->execute([
            ':rut' => $_SESSION['rut'],
            ':idButaca' => $idButaca,
            ':idPelicula' => $idPelicula,
            ':fechaInicio' => $fechaInicio,
            ':fechaFin' => $fechaFin
        ]);
        $idBoleto = $conn->lastInsertId();

        // Insertar pago vinculado al boleto
        $stmtPago->execute([
            ':tipo' => $tipo,
            ':marca' => $marca,
            ':cuatroDig' => $cuatroDig,
            ':fechaTransf' => $fechaTransf,
            ':idBoleto' => $idBoleto
        ]);

         $ultimoIdPago = $conn->lastInsertId();
    }

    $conn->commit();

    header("Location: ResumenPago.php?idPago=".$ultimoIdPago);
    exit;

} catch (PDOException $e) {
    $conn->rollBack();
    die("Error al procesar el pago: " . $e->getMessage());
}
