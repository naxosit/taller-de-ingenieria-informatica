<?php
include_once("../CONNECTION/conexion.php");

$idPelicula = $_GET['pelicula'] ?? null;
$idSala     = $_GET['sala']     ?? null;
$fechaHora  = $_GET['fecha']    ?? null;

if (!$idPelicula || !$idSala || !$fechaHora) {
    die("Faltan parámetros de la función.");
}

/* === datos de la función y la película ===================== */
$sql = "
  SELECT p.nombre  AS nombre_pelicula,
         p.duracion,
         f.fechahora
  FROM   funcion f
  JOIN   pelicula p ON p.idpelicula = f.id_pelicula
  WHERE  f.id_pelicula = :pel
    AND  f.id_sala     = :sal
    AND  f.fechahora   = :fec
";
$stmt = $conn->prepare($sql);
$stmt->execute([
    'pel' => $idPelicula,
    'sal' => $idSala,
    'fec' => $fechaHora
]);
$info = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$info) {
    die("Función no encontrada.");
}

/* === fechas inicio / fin ================================== */
$fechaInicio = new DateTime($info['fechahora']);
$fechaFin    = (clone $fechaInicio);
$fechaFin->modify("+{$info['duracion']} minutes");

/* === butacas con disponibilidad ============================ */
$butacasSql = "
  SELECT b.id_butaca,
         b.fila,
         b.columna,
         CASE
           WHEN EXISTS (
                 SELECT 1
                 FROM   boleto bol
                 WHERE  bol.idbutaca            = b.id_butaca
                   AND  bol.fecha_inicio_boleto = :inicio
                   AND  bol.fecha_fin_boleto    = :fin
                   AND  bol.estado_butaca       = 'ocupada'
                   AND  bol.activo              = true
           ) THEN 'ocupada'
           ELSE 'disponible'
         END AS estado
  FROM   butaca b
  WHERE  b.id_sala = :sala
  ORDER  BY b.fila, b.columna
";
$stmt = $conn->prepare($butacasSql);
$stmt->execute([
    'sala'   => $idSala,
    'inicio' => $fechaInicio->format('Y-m-d H:i:s'),
    'fin'    => $fechaFin->format('Y-m-d H:i:s')
]);
$butacas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Butacas - <?= htmlspecialchars($info['nombrePelicula']) ?></title>
  <link rel="stylesheet" href="../CSS/styles.css">
  <link rel="stylesheet" href="../CSS/cartelera.css">
  <link rel="stylesheet" href="../CSS/botones.css">
  <style>
    .sala {
      display: grid;
      grid-template-columns: repeat(8,40px);
      gap:8px;
      justify-content: center;
      margin:20px 0;
    }
    .butaca {
      width:40px;height:40px;
      line-height:40px;text-align:center;
      border:1px solid #ccc;
      cursor:pointer;
      font-weight:bold;
      border-radius:6px;
    }
    .disponible { background:#4CAF50;color:white; }
    .ocupada { background:#F44336;color:white;pointer-events:none; }
    .seleccionada { background:#FFD600;color:black; }
  </style>
</head>
<body>
<header>
  <div class="logo">Web Cine - <?= htmlspecialchars($info['nombre_pelicula']) ?></div>
  <nav>
    <a href="vista_encargado.php">Peliculas</a>
    <a href="vista_salas.php">Salas</a>
    <a href="Funciones.php">Funciones</a>
  </nav>
</header>
<main class="cartelera-container">
  <h2><?= htmlspecialchars($info['nombre_pelicula']) ?></h2>
  <p><strong>Inicio:</strong> <?= $fechaInicio->format('d-m-Y H:i') ?>
     | <strong>Fin:</strong> <?= $fechaFin->format('d-m-Y H:i') ?></p>

  <form method="POST" action="FormularioPago.php">
    <div class="sala">
      <?php foreach ($butacas as $b): ?>
        <div class="butaca <?= $b['estado'] ?>"
             data-id="<?= $b['id_butaca'] ?>">
          <?= htmlspecialchars($b['fila'].$b['columna']) ?>
        </div>
      <?php endforeach; ?>
    </div>

    <input type="hidden" name="butacasSeleccionadas" id="butacasSeleccionadas">
    <input type="hidden" name="pelicula" value="<?= htmlspecialchars($idPelicula) ?>">
    <input type="hidden" name="sala" value="<?= htmlspecialchars($idSala) ?>">
    <input type="hidden" name="fechaInicio" value="<?= $fechaInicio->format('Y-m-d H:i:s') ?>">
    <input type="hidden" name="fechaFin" value="<?= $fechaFin->format('Y-m-d H:i:s') ?>">

    <button type="submit" class="boton-agregar">Confirmar Compra</button>
  </form>
</main>
<script>
const btns = document.querySelectorAll('.butaca.disponible');
const sel = document.getElementById('butacasSeleccionadas');
let selSet = new Set();
btns.forEach(div => {
  div.addEventListener('click', () => {
    const id = div.dataset.id;
    if (selSet.has(id)) { selSet.delete(id); div.classList.remove('seleccionada'); }
    else { selSet.add(id); div.classList.add('seleccionada'); }
    sel.value = Array.from(selSet).join(',');
  });
});
</script>
</body>
</html>
