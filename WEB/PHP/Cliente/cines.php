<?php
include_once("../../CONNECTION/conexion.php");
session_start();

// Mapeo de nombres de imágenes a IDs de cines
$imagenesCines = [
  1 => 'cine1.jpeg',
  2 => 'cine2.jpeg',
  9 => 'cine3.jpeg',
  4 =>  'cine4.jpeg'
  // Agrega más mapeos según sea necesario
];

try {
  $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $db_user, $db_pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Consulta para obtener las películas
  $stmtPeliculas = $pdo->query("SELECT idPelicula, Nombre, Genero, Imagen FROM Pelicula");
  $peliculas = $stmtPeliculas->fetchAll(PDO::FETCH_ASSOC);

  // Consulta corregida para obtener los cines con su ciudad y tipos de pantalla
  $stmtCines = $pdo->query("
        SELECT 
            C.idCine, 
            C.Nombre_cine, 
            C.ubicacion AS direccion,
            CI.NombreCiudad AS ciudad,
            (SELECT string_agg(DISTINCT S.Tipo_pantalla, ', ') 
             FROM Sala S 
             WHERE S.Cine_idCine = C.idCine) AS tipos_pantalla
        FROM Cine C
        JOIN Ciudad CI ON C.idCiudad = CI.idCiudad
        GROUP BY C.idCine, CI.NombreCiudad
    ");
  $cines = $stmtCines->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Error de conexión: " . $e->getMessage());
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
  <link rel="stylesheet" href="../../CSS/Client/Cines.css">
    <link rel="stylesheet" href="../../CSS/Client/menusuario.css">
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
      <a href="#" class="active">Cines</a>
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

  <!-- Sección de cines -->
  <div class="cines-container">
    <div class="cines-header">
      <h1>Nuestros Cines</h1>
      <p>Descubre nuestras modernas salas de cine con la mejor tecnología para que disfrutes de tus películas favoritas</p>
    </div>

    <div class="cine-grid">
      <?php foreach ($cines as $cine): ?>
        <div class="cine-card">
          <div class="cine-image">
            <?php
            // Obtener el nombre de la imagen del mapeo
            $imageName = isset($imagenesCines[$cine['idcine']]) ? $imagenesCines[$cine['idcine']] : 'default.jpg';
            $imagePath = "../../images/" . $imageName;
            // Comprobar si existe la imagen específica, si no usar default
            $finalImage = file_exists($imagePath) ? $imagePath : "../../images/default.jpg";
            ?>
            <img src="<?php echo $finalImage; ?>" alt="<?php echo htmlspecialchars($cine['nombre_cine']); ?>">
          </div>
          <div class="cine-details">
            <h3 class="cine-name">
              <i class="fas fa-film"></i>
              <?php echo htmlspecialchars($cine['nombre_cine']); ?>
            </h3>
            <div class="cine-location">
              <i class="fas fa-map-marker-alt"></i>
              <span>
                <?php
                // Combinar dirección y ciudad
                $direccion = !empty($cine['direccion']) ? $cine['direccion'] : '';
                $ciudad = !empty($cine['ciudad']) ? $cine['ciudad'] : '';

                if ($direccion && $ciudad) {
                  echo htmlspecialchars($direccion . ', ' . $ciudad);
                } elseif ($direccion) {
                  echo htmlspecialchars($direccion);
                } elseif ($ciudad) {
                  echo htmlspecialchars($ciudad);
                } else {
                  echo 'Ubicación no disponible';
                }
                ?>
              </span>
            </div>

            <div class="cine-features">
              <div class="feature">
                <i class="fas fa-chair"></i>
                <span>Butacas Premium</span>
              </div>
              <div class="feature">
                <i class="fas fa-utensils"></i>
                <span>Confitería</span>
              </div>
              <div class="feature">
                <i class="fas fa-parking"></i>
                <span>Estacionamiento</span>
              </div>
              <div class="feature">
                <i class="fas fa-wifi"></i>
                <span>WiFi Gratis</span>
              </div>
            </div>

            <h4>Formatos disponibles:</h4>
            <div class="cine-formats">
              <?php
              // Convertir los tipos de pantalla en etiquetas
              if (!empty($cine['tipos_pantalla'])) {
                $formatos = explode(',', $cine['tipos_pantalla']);
                foreach ($formatos as $formato) {
                  echo '<span class="format-tag">' . trim($formato) . '</span>';
                }
              } else {
                echo '<span class="format-tag">Sin información</span>';
              }
              ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
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

  <script src="../../JS/Carrusel.js"></script>

</body>

</html>