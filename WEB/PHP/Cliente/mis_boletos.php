<?php
session_start();
if (!isset($_SESSION['rut'])) {
    header("Location: ../login.php");
    exit();
}

require __DIR__ . '/../../CONNECTION/conexion.php';

// Obtener boletos del usuario
try {
    $sql = "SELECT 
                b.Id_Boleto AS idBoleto,
                b.Fecha_inicio_boleto AS fechaCompra,
                f.FechaHora,
                p.Nombre AS pelicula,
                s.Nombre AS sala,
                c.Nombre_cine AS cine,
                but.Fila AS fila,
                but.Columna AS columna
            FROM Boleto b
            JOIN Funcion f ON b.IdFuncion = f.idFuncion
            JOIN Pelicula p ON f.Id_Pelicula = p.idPelicula
            JOIN Sala s ON f.Id_Sala = s.idSala
            JOIN Cine c ON s.Cine_idCine = c.idCine
            JOIN Butaca but ON b.IdButaca = but.Id_Butaca
            WHERE b.RUT = :rut
            ORDER BY f.FechaHora DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':rut', $_SESSION['rut']);
    $stmt->execute();
    $boletos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener boletos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Cine Azul</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../CSS/Client/styles.css">
  <link rel="stylesheet" href="../../CSS/Client/index.css">
  <link rel="stylesheet" href="../../CSS/Client/menusuario.css">
    <link rel="stylesheet" href="../../CSS/Client/misboletos.css">
  <style>
  </style>
</head>

<body>

  <!-- Barra de navegación con azul -->
  <nav class="navbar">
    <div class="logo">
      <i class="fas fa-film"></i>
      <span>Cine Azul</span>
    </div>

    <div class="menu">
      <a href="Index.php">Inicio</a>
      <a href="peliculas.php">Películas</a>
      <a href="cines.php">Cines</a>
      <a href="Promociones/promociones.php">Promociones</a>
      <a href="../Enc_promoyconfi/confiteriacliente.php">Confitería</a>
    </div>

    <div class="actions">
      <?php if (isset($_SESSION['rut'])): ?>
        <div class="user-menu">
          <button class="user-btn">
            <i class="fas fa-user-circle"></i>
            <span><?= htmlspecialchars($_SESSION['nombre'] . ' ' . $_SESSION['apellido']) ?></span>
            <i class="fas fa-chevron-down"></i>
          </button>
          <div class="user-dropdown">
            <div class="user-info">
              <span class="user-name"><?= htmlspecialchars($_SESSION['nombre'] . ' ' . $_SESSION['apellido']) ?></span>
              <span class="user-rut"><?= htmlspecialchars($_SESSION['rut']) ?></span>
            </div>
            <a href="mis_boletos.php" class="dropdown-item">
              <i class="fas fa-ticket-alt"></i> Mis Boletos
            </a>
            <a href="../logout.php" class="dropdown-item">
              <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
          </div>
        </div>
      <?php else: ?>
        <a href="../login.php" class="btn btn-login">
          <i class="fas fa-user"></i>
          <span>Iniciar sesión</span>
        </a>
        <a href="../Registro.php" class="btn btn-tickets">
          <i class="fas fa-user-plus"></i>
          <span>Registrarse</span>
        </a>
      <?php endif; ?>
    </div>
  </nav>

  <div class="container">
    <h1>Mis Boletos</h1>
    
    <?php if (count($boletos) > 0): ?>
      <div class="boletos-container">
        <?php foreach ($boletos as $boleto): 
          $fechaFuncion = new DateTime($boleto['fechahora']);
          $fechaActual = new DateTime();
          $esFutura = $fechaFuncion > $fechaActual;
        ?>
          <div class="boleto-card <?= $esFutura ? 'futuro' : 'pasado' ?>">
            <div class="boleto-header">
              <h2><?= htmlspecialchars($boleto['pelicula']) ?></h2>
              <span class="estado"><?= $esFutura ? 'Próxima función' : 'Función pasada' ?></span>
            </div>
            
            <div class="boleto-info">
              <div class="info-item">
                <i class="fas fa-calendar-alt"></i>
                <span><?= date('d/m/Y H:i', strtotime($boleto['fechahora'])) ?></span>
              </div>
              
              <div class="info-item">
                <i class="fas fa-map-marker-alt"></i>
                <span><?= htmlspecialchars($boleto['cine']) ?> - <?= htmlspecialchars($boleto['sala']) ?></span>
              </div>
              
              <div class="info-item">
                <i class="fas fa-chair"></i>
                <span>Fila <?= $boleto['fila'] ?>, Asiento <?= $boleto['columna'] ?></span>
              </div>
              
              <div class="info-item">
                <i class="fas fa-ticket-alt"></i>
                <span>Boleto #<?= $boleto['idboleto'] ?></span>
              </div>
            </div>
            
            <div class="boleto-footer">
              <span class="fecha-compra">
                Comprado el <?= date('d/m/Y', strtotime($boleto['fechacompra'])) ?>
              </span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="no-boletos">
        <i class="fas fa-ticket-alt"></i>
        <p>No tienes boletos comprados</p>
        <a href="peliculas.php" class="btn-ver-peliculas">Ver películas disponibles</a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Footer -->
  <footer>
    <div class="footer-content">
      <div class="logo">
        <i class="fas fa-film"></i>
        <span>Cine Azul</span>
      </div>

      <div class="social-icons">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="https://www.instagram.com/_.victor.alexis._/"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-youtube"></i></a>
        <a href="#"><i class="fab fa-tiktok"></i></a>
        <a href="Ciudades/vista_ciudades.php"><i class="fas fa-map-marker-alt"></i></a>
      </div>

      <p>Disfruta del mejor cine en nuestras modernas salas con tecnología de última generación.</p>
      <p>© 2025 Cine Azul. Todos los derechos reservados.</p>
      <div class="copyright">
        <p>Este sitio es solo con fines educativos.</p>
      </div>
    </div>
  </footer>

</body>

</html>