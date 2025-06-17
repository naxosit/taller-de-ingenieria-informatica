<?php
// Conexión a PostgreSQL
$host = 'localhost';
$dbname = 'BD_CINE';
$user = 'postgres';
$password = 'torresdiaz1811';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener ID de la película y cine desde la URL
    $idPelicula = isset($_GET['id']) ? $_GET['id'] : null;
    $idCine = isset($_GET['idCine']) ? $_GET['idCine'] : null;
    
    if ($idPelicula && $idCine) {
        // Consulta para obtener detalles de la película
        $stmt = $pdo->prepare("SELECT * FROM Pelicula WHERE idPelicula = :id");
        $stmt->bindParam(':id', $idPelicula);
        $stmt->execute();
        $pelicula = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($pelicula) {
            // Consulta para obtener detalles del cine
            $stmt = $pdo->prepare("SELECT * FROM Cine WHERE idCine = :idCine");
            $stmt->bindParam(':idCine', $idCine);
            $stmt->execute();
            $cine = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cine) {
                // Obtener funciones para esta película en el cine específico
                $stmt = $pdo->prepare("
                    SELECT F.*, S.Nombre AS nombre_sala
                    FROM Funcion F
                    JOIN Sala S ON F.Id_Sala = S.idSala
                    WHERE F.Id_Pelicula = :idPelicula
                    AND S.Cine_idCine = :idCine
                    AND F.FechaHora > NOW() 
                    ORDER BY F.FechaHora
                ");
                $stmt->bindParam(':idPelicula', $idPelicula);
                $stmt->bindParam(':idCine', $idCine);
                $stmt->execute();
                $funciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Agrupar funciones por fecha
                $funcionesAgrupadas = [];
                foreach ($funciones as $funcion) {
                    $fecha = date('Y-m-d', strtotime($funcion['fechahora']));
                    
                    if (!isset($funcionesAgrupadas[$fecha])) {
                        $funcionesAgrupadas[$fecha] = [];
                    }
                    
                    $funcionesAgrupadas[$fecha][] = [
                        'idFuncion' => $funcion['idfuncion'],
                        'hora' => date('H:i', strtotime($funcion['fechahora'])),
                        'sala' => $funcion['nombre_sala']
                    ];
                }
            }
        }
    }
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Si no se encontró la película o cine, redirigir
if (!$idPelicula || !$idCine || !$pelicula || !$cine) {
    header("Location: peliculas.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?php echo $pelicula['nombre']; ?> - Cine Azul</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #4F42B5;
      --primary-dark: #3a3087;
      --primary-light: #7a6fdf;
      --dark: #0d0d0d;
      --light: #ffffff;
      --gray: #f5f5f5;
      --gray-dark: #e0e0e0;
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
    
    /* Contenido principal */
    .movie-detail-container {
      margin-top: 100px;
      padding: 40px;
      max-width: 1200px;
      margin-left: auto;
      margin-right: auto;
    }
    
    .movie-header {
      display: flex;
      gap: 40px;
      margin-bottom: 50px;
    }
    
    .movie-poster {
      flex: 0 0 300px;
      height: 450px;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: var(--shadow);
    }
    
    .movie-poster img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .movie-info {
      flex: 1;
    }
    
    .movie-title {
      font-size: 2.5rem;
      margin-bottom: 15px;
      color: var(--primary);
    }
    
    .movie-meta {
      display: flex;
      gap: 20px;
      margin-bottom: 25px;
      font-size: 1.1rem;
      color: #555;
      flex-wrap: wrap;
    }
    
    .movie-meta span {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 5px 15px;
      background: var(--gray);
      border-radius: 20px;
    }
    
    .section-title {
      font-size: 1.5rem;
      margin: 30px 0 20px;
      color: var(--dark);
      position: relative;
      padding-bottom: 10px;
    }
    
    .section-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 50px;
      height: 3px;
      background: var(--primary);
    }
    
    .movie-description {
      line-height: 1.7;
      color: #444;
      margin-bottom: 30px;
      font-size: 1.1rem;
    }
    
    .movie-details {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
      margin-bottom: 40px;
    }
    
    .detail-item {
      padding: 15px;
      background: var(--gray);
      border-radius: 8px;
    }
    
    .detail-item h4 {
      color: var(--primary);
      margin-bottom: 8px;
      font-size: 1.1rem;
    }
    
    .detail-item p {
      color: #555;
    }
    
    /* Sección de cine seleccionado */
    .cinema-info {
      background: var(--primary-light);
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 30px;
      display: flex;
      align-items: center;
      gap: 20px;
    }
    
    .cinema-icon {
      font-size: 2rem;
      color: white;
    }
    
    .cinema-text h3 {
      font-size: 1.5rem;
      color: white;
      margin-bottom: 5px;
    }
    
    .cinema-text p {
      color: rgba(255, 255, 255, 0.9);
      display: flex;
      align-items: center;
      gap: 8px;
    }
    
    /* Sección de funciones */
    .showtimes-container {
      background: white;
      border-radius: 10px;
      box-shadow: var(--shadow);
      padding: 30px;
      margin-bottom: 50px;
    }
    
    .date-group {
      margin-bottom: 25px;
      padding-bottom: 20px;
      border-bottom: 1px solid var(--gray-dark);
    }
    
    .date-group:last-child {
      border-bottom: none;
      margin-bottom: 0;
      padding-bottom: 0;
    }
    
    .date-title {
      font-size: 1.3rem;
      margin-bottom: 15px;
      color: var(--primary);
    }
    
    .showtimes-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
      gap: 15px;
    }
    
    .showtime-btn {
      padding: 12px;
      background: var(--gray);
      border-radius: 5px;
      text-align: center;
      cursor: pointer;
      transition: all 0.2s ease;
      border: 2px solid transparent;
      position: relative;
    }
    
    .showtime-btn:hover {
      background: var(--primary);
      color: white;
      transform: translateY(-3px);
      box-shadow: 0 4px 10px rgba(79, 66, 181, 0.2);
    }
    
    .showtime-btn.active {
      background: var(--primary);
      color: white;
      border-color: var(--primary-dark);
    }
    
    .sala-info {
      font-size: 0.9rem;
      color: #777;
      margin-top: 5px;
    }
    
    .showtime-btn:hover .sala-info {
      color: rgba(255, 255, 255, 0.8);
    }
    
    .no-showtimes {
      text-align: center;
      padding: 40px;
      font-size: 1.2rem;
      color: #666;
    }
    
    /* Footer */
    footer {
      background: var(--dark);
      color: var(--light);
      padding: 40px;
      text-align: center;
      margin-top: 50px;
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
    
    /* Responsive */
    @media (max-width: 992px) {
      .movie-header {
        flex-direction: column;
        gap: 30px;
      }
      
      .movie-poster {
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
      }
      
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
      .movie-detail-container {
        padding: 20px;
      }
      
      .movie-title {
        font-size: 2rem;
      }
      
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
    }
    
    @media (max-width: 576px) {
      .menu {
        flex-wrap: wrap;
      }
      
      .movie-meta {
        flex-direction: column;
        align-items: flex-start;
      }
      
      .movie-meta span {
        width: 100%;
      }
      
      .showtimes-grid {
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
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

  <!-- Contenido principal -->
  <div class="movie-detail-container">
    <div class="movie-header">
      <div class="movie-poster">
        <img src="<?php echo htmlspecialchars($pelicula['imagen']); ?>" alt="<?php echo htmlspecialchars($pelicula['nombre']); ?>">
      </div>
      
      <div class="movie-info">
        <h1 class="movie-title"><?php echo htmlspecialchars($pelicula['nombre']); ?></h1>
        
        <div class="movie-meta">
          <span><i class="fas fa-film"></i> <?php echo htmlspecialchars($pelicula['genero']); ?></span>
          <span><i class="fas fa-clock"></i> <?php echo htmlspecialchars($pelicula['duracion']); ?> min</span>
          <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($pelicula['clasificacion'] ?? '+14'); ?></span>
        </div>
        
        <h3 class="section-title">Sinopsis</h3>
        <p class="movie-description"><?php echo htmlspecialchars($pelicula['sinopsis']); ?></p>
        
        <div class="movie-details">
          <div class="detail-item">
            <h4>Director</h4>
            <p><?php echo htmlspecialchars($pelicula['director'] ?? 'No disponible'); ?></p>
          </div>
          
          <div class="detail-item">
            <h4>Idioma</h4>
            <p>Subtitulada | Doblada</p>
          </div>
          
          <div class="detail-item">
            <h4>Formato Disponible</h4>
            <p>2D.CONV</p>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Información del cine seleccionado -->
    <div class="cinema-info">
      <div class="cinema-icon">
        <i class="fas fa-film"></i>
      </div>
      <div class="cinema-text">
        <h3><?php echo htmlspecialchars($cine['nombre_cine']); ?></h3>
        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($cine['ubicacion']); ?></p>
      </div>
    </div>
    
    <!-- Sección de funciones -->
    <div class="showtimes-container">
      <h2 class="section-title">Funciones Disponibles</h2>
      
      <?php if (!empty($funcionesAgrupadas)): ?>
        <?php foreach ($funcionesAgrupadas as $fecha => $horarios): ?>
          <div class="date-group">
            <h3 class="date-title"><?php echo date('l, d F', strtotime($fecha)); ?></h3>
            
            <div class="showtimes-grid">
              <?php foreach ($horarios as $horario): ?>
                <a href="seleccion_asientos.php?idFuncion=<?php echo $horario['idFuncion']; ?>" class="showtime-btn">
                  <?php echo $horario['hora']; ?>
                  <div class="sala-info">Sala <?php echo $horario['sala']; ?></div>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-showtimes">
          <i class="fas fa-calendar-times fa-3x" style="color: #ddd; margin-bottom: 20px;"></i>
          <p>No hay funciones disponibles para esta película en este cine.</p>
          <p>Por favor, seleccione otro cine o consulte más tarde.</p>
        </div>
      <?php endif; ?>
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

  <script>
    // Selección de horario
    document.querySelectorAll('.showtime-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        // Quitar selección anterior
        document.querySelectorAll('.showtime-btn').forEach(b => {
          b.classList.remove('active');
        });
        
        // Seleccionar este horario
        this.classList.add('active');
      });
    });
  </script>

</body>
</html>