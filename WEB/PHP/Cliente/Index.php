<?php
include_once("../../CONNECTION/conexion.php");
session_start();
// Establecer conexión con la base de datos (ajusta estos valores)

try {
  $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $db_user, $db_pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Consulta para obtener las películas
  $stmt = $pdo->query("SELECT idPelicula, Nombre, Genero, Imagen FROM Pelicula");
  $peliculas = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
  <link rel="stylesheet" href="../../CSS/Client/index.css">
  <link rel="stylesheet" href="../../CSS/Client/menusuario.css">
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
      <a href="peliculas.php">Películas</a>
      <a href="cines.php">Cines</a>
      <a href="#">Promociones</a>
      <a href="#">Confitería</a>
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

  <!-- Carrusel con estilo azul -->
  <div class="carousel-container">
    <button class="carousel-btn left" onclick="prevSlide()">
      <i class="fas fa-chevron-left"></i>
    </button>

    <div class="carousel-slides" id="slides">
      <div class="carousel-slide" style="background-image: url('https://i.ytimg.com/vi/hJVL8U6ROck/maxresdefault.jpg')">
        <div class="slide-content">
          <h2>ESTRENOS EXCLUSIVOS</h2>
          <p>Disfruta de las mejores películas antes que nadie en nuestros cines con salas premium.</p>
        </div>
      </div>

      <div class="carousel-slide" style="background-image: url('https://images.unsplash.com/photo-1542204165-65bf26472b9b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')">
        <div class="slide-content">
          <h2>CONFITERIA DELICIOSA</h2>
          <p>Date un gusto y disfruta de mejor manera con nuestros productos de confiteria.</p>
        </div>
      </div>

      <div class="carousel-slide" style="background-image: url('https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')">
        <div class="slide-content">
          <h2>PROMOCIONES ESPECIALES</h2>
          <p>Martes y miércoles 2x1 en todas las películas (Si estudias Ing Civil Informatica precio normal).</p>
        </div>
      </div>
    </div>

    <button class="carousel-btn right" onclick="nextSlide()">
      <i class="fas fa-chevron-right"></i>
    </button>

    <div class="carousel-dots" id="dots"></div>
    <div class="carousel-loader" id="loader">
      <div class="loader-progress" id="progress"></div>
    </div>
  </div>

  <!-- Sección de películas desde la base de datos -->
  <section class="section">
    <h2 class="section-title">Películas en cartelera</h2>

    <div class="filters">
      <button class="filter-btn active">Estrenos</button>
    </div>

    <div class="movies-container">
      <?php if (count($peliculas) > 0): ?>
        <?php foreach ($peliculas as $pelicula): ?>
          <div class="movie-card">
            <div class="movie-poster" style="background-image: url('<?php echo htmlspecialchars($pelicula['imagen']); ?>')"></div>
            <div class="movie-info">
              <h3 class="movie-title"><?php echo htmlspecialchars($pelicula['nombre']); ?></h3>
              <div class="movie-genre"><?php echo htmlspecialchars($pelicula['genero']); ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="no-movies">No hay películas disponibles en este momento.</p>
      <?php endif; ?>
    </div>
  </section>

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