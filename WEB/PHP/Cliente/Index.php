<?php
// Establecer conexión con la base de datos (ajusta estos valores)
$host = 'localhost';
$dbname = 'BD_CINE';
$username = 'postgres';
$password = 'torresdiaz1811';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
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
  <style>
    /* Variables con nuevo color azul */
    :root {
      --primary: #4F42B5;
      --primary-dark: #3a3087;
      --primary-light: #7a6fdf;
      --dark: #0d0d0d;
      --light: #ffffff;
      --gray: #f5f5f5;
      --text: #333333;
      --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      --gradient: linear-gradient(135deg, #4F42B5 0%, #6A5ACD 100%);
    }
    
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    
    body {
      font-family: 'Montserrat', sans-serif;
      background-color: var(--light);
      color: var(--text);
      line-height: 1.6;
    }
    
    /* Barra de navegación con azul */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 40px;
      background: var(--gradient);
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000;
      height: 80px;
      box-shadow: var(--shadow);
    }
    
    .logo {
      font-size: 28px;
      color: var(--light);
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 10px;
      text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    .logo i {
      color: var(--light);
    }
    
    .menu {
      display: flex;
      gap: 30px;
    }
    
    .menu a {
      color: rgba(255, 255, 255, 0.9);
      text-decoration: none;
      padding: 10px;
      font-weight: 500;
      font-size: 15px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      position: relative;
      transition: all 0.3s ease;
    }
    
    .menu a:hover {
      color: var(--light);
    }
    
    .menu a::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      width: 0;
      height: 2px;
      background: var(--light);
      transition: all 0.3s ease;
    }
    
    .menu a:hover::after {
      width: 80%;
      left: 10%;
    }
    
    .actions {
      display: flex;
      gap: 20px;
      align-items: center;
    }
    
    .btn {
      padding: 10px 20px;
      border-radius: 4px;
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      display: flex;
      align-items: center;
      gap: 8px;
      border: none;
    }
    
    .btn-login {
      background: rgba(255, 255, 255, 0.2);
      color: var(--light);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .btn-login:hover {
      background: rgba(255, 255, 255, 0.3);
      border-color: rgba(255, 255, 255, 0.5);
    }
    
    .btn-tickets {
      background: var(--light);
      color: var(--primary);
      box-shadow: var(--shadow);
    }
    
    .btn-tickets:hover {
      background: #f0f0f0;
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }
    
    /* Carrusel con estilo azul - CORREGIDO */
    .carousel-container {
      margin-top: 80px;
      position: relative;
      overflow: hidden;
      width: 100%;
      height: 70vh;
      max-height: 700px;
      min-height: 500px;
    }
    
    .carousel-slides {
      display: flex;
      transition: transform 0.5s ease-in-out;
      height: 100%;
    }
    
    .carousel-slide {
      min-width: 100%;
      height: 100%;
      background-size: cover;
      background-position: center;
      display: flex;
      align-items: flex-end;
      padding: 40px;
      position: relative;
    }
    
    .carousel-slide::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to top, rgba(79, 66, 181, 0.8) 0%, rgba(79, 66, 181, 0.3) 50%, transparent 100%);
    }
    
    .slide-content {
      position: relative;
      z-index: 2;
      max-width: 600px;
      color: var(--light);
    }
    
    .slide-content h2 {
      font-size: 3rem;
      margin-bottom: 15px;
      text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    
    .slide-content p {
      font-size: 1.2rem;
      margin-bottom: 25px;
      max-width: 80%;
    }
    
    /* CORRECCIÓN DE BOTONES DE CARRUSEL */
    .carousel-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(255, 255, 255, 0.3);
      border: none;
      color: var(--light);
      font-size: 25px;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      z-index: 10;
      transition: all 0.3s ease;
      backdrop-filter: blur(5px);
    }
    
    .carousel-btn:hover {
      background: rgba(255, 255, 255, 0.5);
      transform: translateY(-50%) scale(1.1);
    }
    
    .carousel-btn.left {
      left: 30px;
    }
    
    .carousel-btn.right {
      right: 30px;
    }
    
    .carousel-dots {
      position: absolute;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 10px;
      z-index: 10;
    }
    
    .dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.5);
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .dot.active {
      background: var(--light);
      transform: scale(1.2);
    }
    
    /* Sección de películas */
    .section {
      padding: 60px 40px;
      background: var(--light);
    }
    
    .section-title {
      font-size: 2rem;
      margin-bottom: 30px;
      color: var(--dark);
      position: relative;
      padding-bottom: 15px;
    }
    
    .section-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 60px;
      height: 4px;
      background: var(--primary);
    }
    
    .movies-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 30px;
    }
    
    .movie-card {
      background: var(--light);
      border-radius: 8px;
      overflow: hidden;
      box-shadow: var(--shadow);
      transition: all 0.3s ease;
      position: relative;
    }
    
    .movie-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 10px 20px rgba(79, 66, 181, 0.2);
    }
    
    .movie-poster {
      height: 320px;
      width: 100%;
      background-size: cover;
      background-position: center;
    }
    
    .movie-info {
      padding: 20px;
    }
    
    .movie-title {
      font-size: 1.1rem;
      margin-bottom: 8px;
      font-weight: 600;
      color: var(--primary);
    }
    
    .movie-genre {
      color: #666;
      font-size: 0.9rem;
      margin-bottom: 15px;
    }
    
    .movie-btn {
      display: block;
      width: 100%;
      padding: 10px;
      background: var(--gradient);
      color: white;
      text-align: center;
      border-radius: 4px;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
    }
    
    .movie-btn:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(79, 66, 181, 0.3);
    }
    
    .badge {
      position: absolute;
      top: 15px;
      right: 15px;
      background: var(--primary);
      color: white;
      padding: 5px 10px;
      border-radius: 3px;
      font-size: 12px;
      font-weight: bold;
    }
    
    /* Footer */
    footer {
      background: var(--dark);
      color: var(--light);
      padding: 40px;
      text-align: center;
    }
    
    .footer-content {
      max-width: 1000px;
      margin: 0 auto;
    }
    
    .social-icons {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin: 20px 0;
    }
    
    .social-icons a {
      color: var(--light);
      font-size: 1.5rem;
      transition: all 0.3s ease;
    }
    
    .social-icons a:hover {
      color: var(--primary-light);
      transform: translateY(-5px);
    }
    
    .copyright {
      margin-top: 20px;
      color: #aaa;
      font-size: 0.9rem;
    }
    
    /* Filtros */
    .filters {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-bottom: 30px;
      flex-wrap: wrap;
    }
    
    .filter-btn {
      padding: 8px 20px;
      background: var(--gray);
      border: none;
      border-radius: 30px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .filter-btn:hover, .filter-btn.active {
      background: var(--primary);
      color: white;
    }
    
    /* Nuevo: Indicador de carga */
    .carousel-loader {
      position: absolute;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 4px;
      background: rgba(255, 255, 255, 0.3);
      border-radius: 2px;
      overflow: hidden;
      z-index: 10;
    }
    
    .loader-progress {
      height: 100%;
      width: 33.33%;
      background: var(--light);
      border-radius: 2px;
      transition: transform 0.3s ease;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
      .navbar {
        padding: 0 20px;
      }
      
      .menu {
        gap: 15px;
      }
      
      .menu a {
        font-size: 14px;
      }
      
      .btn {
        padding: 8px 15px;
        font-size: 13px;
      }
    }
    
    @media (max-width: 768px) {
      .navbar {
        flex-wrap: wrap;
        height: auto;
        padding: 15px;
      }
      
      .logo {
        width: 100%;
        justify-content: center;
        margin-bottom: 15px;
      }
      
      .menu {
        width: 100%;
        justify-content: center;
        margin-bottom: 15px;
      }
      
      .actions {
        width: 100%;
        justify-content: center;
      }
      
      .carousel-container {
        height: 60vh;
      }
      
      .slide-content h2 {
        font-size: 2.2rem;
      }
      
      .slide-content p {
        font-size: 1rem;
      }
      
      .section {
        padding: 40px 20px;
      }
      
      .carousel-btn {
        width: 50px;
        height: 50px;
        font-size: 20px;
      }
    }
    
    @media (max-width: 576px) {
      .menu {
        flex-wrap: wrap;
      }
      
      .carousel-container {
        height: 50vh;
      }
      
      .slide-content {
        padding: 20px;
      }
      
      .slide-content h2 {
        font-size: 1.8rem;
      }
      
      .movies-container {
        grid-template-columns: 1fr 1fr;
        gap: 20px;
      }
      
      .carousel-btn {
        width: 40px;
        height: 40px;
        font-size: 16px;
      }
      
      .carousel-btn.left {
        left: 10px;
      }
      
      .carousel-btn.right {
        right: 10px;
      }
    }
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
      <a href="#">Cines</a>
      <a href="#">Promociones</a>
      <a href="#">Socios</a>
      <a href="#">Confitería</a>
    </div>

    <div class="actions">
      <button class="btn btn-login">
        <i class="fas fa-user"></i>
        <span>Iniciar sesión</span>
      </button>
      <button class="btn btn-tickets">
        <i class="fas fa-ticket-alt"></i>
        <span>Comprar entradas</span>
      </button>
    </div>
  </nav>

  <!-- Carrusel con estilo azul - CORREGIDO -->
  <div class="carousel-container">
    <button class="carousel-btn left" onclick="prevSlide()">
      <i class="fas fa-chevron-left"></i>
    </button>
    
    <div class="carousel-slides" id="slides">
      <div class="carousel-slide" style="background-image: url('https://images.unsplash.com/photo-1536440136628-849c177e76a1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')">
        <div class="slide-content">
          <h2>MISION: IMPOSIBLE</h2>
          <p>La sentencia final - Vive la experiencia cinematográfica más emocionante del año</p>
          <button class="btn btn-tickets">Comprar entradas</button>
        </div>
      </div>
      
      <div class="carousel-slide" style="background-image: url('https://images.unsplash.com/photo-1542204165-65bf26472b9b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')">
        <div class="slide-content">
          <h2>ESTRENOS EXCLUSIVOS</h2>
          <p>Disfruta de las mejores películas antes que nadie en nuestras salas premium</p>
          <button class="btn btn-tickets">Ver más</button>
        </div>
      </div>
      
      <div class="carousel-slide" style="background-image: url('https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')">
        <div class="slide-content">
          <h2>PROMOCIONES ESPECIALES</h2>
          <p>Martes y miércoles 2x1 en todas las películas con tu tarjeta de socio</p>
          <button class="btn btn-tickets">Ver promociones</button>
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
      <button class="filter-btn active">Todas</button>
      <button class="filter-btn">Estrenos</button>
      <button class="filter-btn">Próximamente</button>
      <button class="filter-btn">Más vistas</button>
    </div>
    
    <div class="movies-container">
      <?php if (count($peliculas) > 0): ?>
        <?php foreach ($peliculas as $pelicula): ?>
          <div class="movie-card">
            <div class="movie-poster" style="background-image: url('<?php echo htmlspecialchars($pelicula['imagen']); ?>')"></div>
            <div class="movie-info">
              <h3 class="movie-title"><?php echo htmlspecialchars($pelicula['nombre']); ?></h3>
              <div class="movie-genre"><?php echo htmlspecialchars($pelicula['genero']); ?></div>
              <button class="movie-btn">Comprar entradas</button>
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
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-youtube"></i></a>
        <a href="#"><i class="fab fa-tiktok"></i></a>
      </div>
      
      <p>Disfruta del mejor cine en nuestras modernas salas con tecnología de última generación.</p>
      <p>© 2023 Cine Azul. Todos los derechos reservados.</p>
      <div class="copyright">
        <p>Este sitio es solo con fines educativos. Las imágenes son de Unsplash.</p>
      </div>
    </div>
  </footer>

  <!-- Script para carrusel corregido -->
  <script>
    let index = 0;
    const slides = document.getElementById('slides');
    const slidesArray = slides.querySelectorAll(".carousel-slide");
    const total = slidesArray.length;
    
    // Crear dots
    const dotsContainer = document.getElementById('dots');
    for (let i = 0; i < total; i++) {
      const dot = document.createElement('div');
      dot.classList.add('dot');
      dot.addEventListener('click', () => showSlide(i));
      dotsContainer.appendChild(dot);
    }
    
    const dots = document.querySelectorAll('.dot');
    
    // Ajustar ancho del carrusel dinámicamente
    slides.style.width = `${100 * total}%`;
    
    function showSlide(i) {
      index = (i + total) % total;
      slides.style.transform = `translateX(-${index * (100 / total)}%)`;
      
      // Actualizar dots activos
      dots.forEach((dot, idx) => {
        if (idx === index) {
          dot.classList.add('active');
        } else {
          dot.classList.remove('active');
        }
      });
      
      // Reiniciar animación de progreso
      resetProgress();
    }
    
    function nextSlide() {
      showSlide(index + 1);
    }
    
    function prevSlide() {
      showSlide(index - 1);
    }
    
    // Iniciar con el primer dot activo
    if (dots.length > 0) {
      dots[0].classList.add('active');
    }
    
    // Animación de progreso
    const progress = document.getElementById('progress');
    let progressInterval;
    
    function resetProgress() {
      clearInterval(progressInterval);
      progress.style.transform = 'scaleX(0)';
      progress.style.transition = 'none';
      
      setTimeout(() => {
        progress.style.transition = 'transform 5s linear';
        progress.style.transform = 'scaleX(1)';
      }, 50);
    }
    
    // Auto avanzar
    let slideInterval = setInterval(() => {
      nextSlide();
    }, 5000);
    
    // Pausar al pasar el ratón
    const carousel = document.querySelector('.carousel-container');
    carousel.addEventListener('mouseenter', () => {
      clearInterval(slideInterval);
      clearInterval(progressInterval);
      progress.style.transition = 'none';
    });
    
    carousel.addEventListener('mouseleave', () => {
      resetProgress();
      slideInterval = setInterval(() => {
        nextSlide();
      }, 5000);
    });
    
    // Iniciar animación de progreso
    resetProgress();
    
    // Filtros interactivos
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        filterBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
      });
    });
  </script>

</body>
</html>