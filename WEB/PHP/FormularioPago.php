<?php
include_once("../CONNECTION/conexion.php");

// Configurar zona horaria correcta
date_default_timezone_set('America/Santiago');  // Cambia por tu zona horaria

// Recibimos los datos enviados desde Boleteria.php
$butacasSeleccionadas = $_POST['butacasSeleccionadas'];
$idPelicula = $_POST['pelicula'];
$idSala = $_POST['sala'];
$fechaInicio = $_POST['fechaInicio'];
$fechaFin = $_POST['fechaFin'];

$fechaHoraActual = date('Y-m-d\TH:i'); // Ahora toma la hora correcta

// Consultamos el nombre de la película
$sql = "SELECT Nombre FROM Pelicula WHERE IdPelicula = :id";
$stmt = $conn->prepare($sql);
$stmt->execute(['id' => $idPelicula]);
$info = $stmt->fetch(PDO::FETCH_ASSOC);
?>  

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Butacas - <?= htmlspecialchars($info['nombre']) ?></title>
  <link rel="stylesheet" href="../CSS/styles.css">
  <link rel="stylesheet" href="../CSS/formulario.css">
  <link rel="stylesheet" href="../CSS/botones.css">
</head>
<body>
<header>
  <div class="logo">Web Cine - <?= htmlspecialchars($info['nombre']) ?></div>
  <nav>
    <a href="vista_encargado.php">Peliculas</a>
    <a href="vista_salas.php">Salas</a>
    <a href="Funciones.php">Funciones</a>
  </nav>
</header>
<div class="formulario-agregar">
    <h2>Formulario de Pago</h2>
    <form method="POST" action="Cargar_datos.php">
    <label>Tipo de Tarjeta:
        <input type="text" name="tipo" required>
    </label><br>
    <label>Marca:
        <input type="text" name="marca" required>
    </label><br>
    <label>Últimos 4 dígitos:
        <input type="text" name="cuatroDig" maxlength="4" pattern="\d{4}" required>
    </label><br>
    <label>Fecha y hora de transferencia:
    <input type="datetime-local" name="fecha_transf" value="<?= date('Y-m-d\TH:i') ?>" required>
    </label><br><br>


    <!-- Datos ocultos para el procesamiento -->
    <input type="hidden" name="butacasSeleccionadas" value="<?= htmlspecialchars($butacasSeleccionadas) ?>">
    <input type="hidden" name="pelicula" value="<?= htmlspecialchars($idPelicula) ?>">
    <input type="hidden" name="sala" value="<?= htmlspecialchars($idSala) ?>">
    <input type="hidden" name="fechaInicio" value="<?= htmlspecialchars($fechaInicio) ?>">
    <input type="hidden" name="fechaFin" value="<?= htmlspecialchars($fechaFin) ?>">

    <button type="submit">Pagar</button>
    </form> 
</div>
</body>
</html>
