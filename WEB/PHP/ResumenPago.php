<?php
session_start();
include_once("../CONNECTION/conexion.php");

if (!isset($_GET['idPago'])) {
    die("ID de pago no proporcionado.");
}

$idPago = (int)$_GET['idPago'];
$rutSesion = $_SESSION['rut'] ?? null;
if (!$rutSesion) {
    die("Usuario no autenticado.");
}

// Paso 1: Obtener los datos del pago original
$sqlPago = "SELECT Tipo, Marca, CuatroDig, Fecha_Transf FROM Pago WHERE Id_Pago = :idPago";
$stmtPago = $conn->prepare($sqlPago);
$stmtPago->execute([':idPago' => $idPago]);
$pago = $stmtPago->fetch(PDO::FETCH_ASSOC);

if (!$pago) {
    die("Pago no encontrado.");
}

// Para cubrir una ventana de tiempo, por ejemplo +/- 5 minutos
$fechaInicio = date('Y-m-d H:i:s', strtotime($pago['fecha_transf'] . ' -5 minutes'));
$fechaFin = date('Y-m-d H:i:s', strtotime($pago['fecha_transf'] . ' +5 minutes'));

$sql = "
SELECT
    p.Id_Pago,
    b.RUT,
    pel.Nombre AS pelicula,
    s.Nombre AS sala,
    c.Nombre_cine AS cine,
    STRING_AGG(but.Fila || but.Columna::text, ', ' ORDER BY but.Fila, but.Columna) AS butacas,
    p.Fecha_Transf
FROM Pago p
INNER JOIN Boleto b ON p.IdBoleto = b.Id_Boleto
INNER JOIN Pelicula pel ON b.IdPelicula = pel.idPelicula
INNER JOIN Butaca but ON b.IdButaca = but.Id_Butaca
INNER JOIN Sala s ON but.Id_Sala = s.idSala
INNER JOIN Cine c ON s.Cine_idCine = c.idCine
WHERE p.Tipo = :tipo
  AND p.Marca = :marca
  AND p.CuatroDig = :cuatroDig
  AND p.Fecha_Transf BETWEEN :fechaInicio AND :fechaFin
  AND b.RUT = :rut
GROUP BY p.Id_Pago, b.RUT, pel.Nombre, s.Nombre, c.Nombre_cine, p.Fecha_Transf
ORDER BY p.Fecha_Transf
";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':tipo' => $pago['tipo'],
    ':marca' => $pago['marca'],
    ':cuatroDig' => $pago['cuatrodig'],
    ':fechaInicio' => $fechaInicio,
    ':fechaFin' => $fechaFin,
    ':rut' => $rutSesion
]);

$pagosRelacionados = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$pagosRelacionados) {
    die("No se encontraron pagos relacionados.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gestión de Películas - Web Cine</title>
    <link rel="stylesheet" href="../CSS/styles.css" />
    <link rel="stylesheet" href="../CSS/botones.css" />
    <link rel="stylesheet" href="../CSS/ticket.css" />

</head>
<body>
<header>
    <div class="logo">Web Cine - Resumen del Pago</div>
    <nav>
        <a href="Enc_cartelera/Cartelera.php">Cartelera</a>
    </nav>
</header>

<div class="container">
    <h1 class="page-title" style="text-align:center;">Resumen de Pagos Relacionados</h1>
    <?php foreach ($pagosRelacionados as $resumen): ?>
        <div class="ticket">
            <h2>Pago ID: <?= htmlspecialchars($resumen['id_pago']) ?></h2>
            <p><strong>RUT:</strong> <?= htmlspecialchars($resumen['rut']) ?></p>
            <p><strong>Película:</strong> <?= htmlspecialchars($resumen['pelicula']) ?></p>
            <p><strong>Sala:</strong> <?= htmlspecialchars($resumen['sala']) ?></p>
            <p><strong>Cine:</strong> <?= htmlspecialchars($resumen['cine']) ?></p>
            <p><strong>Butacas asignadas:</strong> <?= htmlspecialchars($resumen['butacas']) ?></p>
            <p><small><em>Fecha pago: <?= htmlspecialchars($resumen['fecha_transf']) ?></em></small></p>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
