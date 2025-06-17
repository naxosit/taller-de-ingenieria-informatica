<?php
// Conexión a PostgreSQL
$host = 'localhost';
$dbname = 'BD_CINE';
$user = 'postgres';
$password = 'torresdiaz1811';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener cines
    $stmt = $pdo->query("SELECT idCine, Nombre_cine FROM Cine");
    $cines = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener películas con funciones disponibles (inicialmente sin filtros)
    $peliculas = [];
    $fecha_seleccionada = date('Y-m-d');
    
    // Manejar filtros
    $idCine = isset($_GET['idCine']) ? $_GET['idCine'] : null;
    $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : $fecha_seleccionada;
    
    if ($idCine && $fecha) {
        // Consulta para obtener películas disponibles para un cine y fecha específicos
        $query = "
            SELECT DISTINCT P.idPelicula, P.Nombre, P.Genero, P.Imagen, P.Duracion, P.Sinopsis 
            FROM Pelicula P
            INNER JOIN Funcion F ON P.idPelicula = F.Id_Pelicula
            INNER JOIN Sala S ON F.Id_Sala = S.idSala
            WHERE S.Cine_idCine = :idCine 
            AND DATE(F.FechaHora) = :fecha
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':idCine', $idCine);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->execute();
        $peliculas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Detalle de Películas - Cine Azul</title>
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
    .main-content {
      margin-top: 100px;
      padding: 30px 40px;
      display: flex;
      gap: 30px;
    }
    
    /* Panel de filtros */
    .filters-panel {
      flex: 0 0 300px;
      background: white;
      border-radius: 10px;
      box-shadow: var(--shadow);
      padding: 25px;
      height: fit-content;
    }
    
    .filters-title {
      font-size: 1.5rem;
      margin-bottom: 20px;
      color: var(--primary);
      border-bottom: 2px solid var(--primary);
      padding-bottom: 10px;
    }
    
    .filter-group {
      margin-bottom: 30px;
    }
    
    .filter-group h3 {
      font-size: 1.2rem;
      margin-bottom: 15px;
      color: var(--dark);
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .filter-group h3 i {
      color: var(--primary);
    }
    
    .filter-select {
      width: 100%;
      padding: 12px;
      border: 1px solid var(--gray-dark);
      border-radius: 5px;
      margin-top: 10px;
      background: white;
      font-family: 'Montserrat', sans-serif;
      font-size: 1rem;
      color: var(--text);
    }
    
    /* Listado de películas */
    .movies-list {
      flex: 1;
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
    
    .movie-detail {
      background: white;
      border-radius: 10px;
      box-shadow: var(--shadow);
      overflow: hidden;
      margin-bottom: 30px;
      display: flex;
    }
    
    .movie-poster {
      flex: 0 0 250px;
      height: 350px;
      background-size: cover;
      background-position: center;
    }
    
    .movie-info {
      flex: 1;
      padding: 30px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    
    .movie-header {
      margin-bottom: 20px;
    }
    
    .movie-title {
      font-size: 1.8rem;
      margin-bottom: 10px;
      color: var(--primary);
    }
    
    .movie-meta {
      display: flex;
      gap: 20px;
      margin-bottom: 15px;
      font-size: 1.1rem;
      color: #555;
    }
    
    .movie-meta span {
      display: flex;
      align-items: center;
      gap: 5px;
    }
    
    .movie-description {
      line-height: 1.7;
      color: #444;
      margin-bottom: 25px;
    }
    
    .movie-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .movie-buttons {
      display: flex;
      gap: 15px;
    }
    
    .btn-outline {
      background: transparent;
      border: 2px solid var(--primary);
      color: var(--primary);
    }
    
    .btn-outline:hover {
      background: var(--primary);
      color: white;
    }
    
    .showtimes {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
    }
    
    .showtime {
      padding: 10px 20px;
      background: var(--gray);
      border-radius: 5px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    
    .showtime:hover {
      background: var(--primary);
      color: white;
    }
    
    .no-results {
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
      .main-content {
        flex-direction: column;
      }
      
      .filters-panel {
        flex: 1;
        width: 100%;
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
      .movie-detail {
        flex-direction: column;
      }
      
      .movie-poster {
        flex: 0 0 300px;
        width: 100%;
      }
      
      .movie-footer {
        flex-direction: column;
        gap: 20px;
        align-items: flex-start;
      }
      
      .showtimes {
        width: 100%;
        justify-content: center;
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
      
      .main-content {
        padding: 20px;
      }
    }
    
    @media (max-width: 576px) {
      .menu {
        flex-wrap: wrap;
      }
      
      .movie-buttons {
        width: 100%;
        flex-direction: column;
      }
      
      .movie-meta {
        flex-direction: column;
        gap: 10px;
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
      <a href="#" class="active">Películas</a>
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
  <div class="main-content">
    <!-- Panel de filtros -->
    <div class="filters-panel">
      <h2 class="filters-title">Filtra Por:</h2>
      <form method="GET" action="">
        <div class="filter-group">
          <h3><i class="fas fa-film"></i> Cine</h3>
          <select class="filter-select" name="idCine" required>
            <option value="">Seleccione un cine</option>
            <?php foreach ($cines as $cine): ?>
              <option value="<?php echo $cine['idcine']; ?>" <?php echo (isset($_GET['idCine']) && $_GET['idCine'] == $cine['idcine']) ? 'selected' : ''; ?>>
                <?php echo $cine['nombre_cine']; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div class="filter-group">
          <h3><i class="fas fa-calendar-day"></i> Fecha</h3>
          <input type="date" class="filter-select" name="fecha" value="<?php echo isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d'); ?>" required>
        </div>
        
        <button type="submit" class="btn btn-tickets" style="width: 100%; margin-top: 20px;">
          <i class="fas fa-filter"></i> Aplicar Filtros
        </button>
      </form>
    </div>
    
    <!-- Listado de películas -->
    <div class="movies-list">
      <h2 class="section-title">Películas Disponibles</h2>
      
      <?php if (!empty($peliculas)): ?>
        <?php foreach ($peliculas as $pelicula): ?>
          <div class="movie-detail">
            <div class="movie-poster" style="background-image: url('<?php echo $pelicula['imagen']; ?>')"></div>
            <div class="movie-info">
              <div class="movie-header">
                <h3 class="movie-title"><?php echo $pelicula['nombre']; ?></h3>
                <div class="movie-meta">
                  <span><i class="fas fa-film"></i> <?php echo $pelicula['genero']; ?></span>
                  <span><i class="fas fa-clock"></i> <?php echo $pelicula['duracion']; ?> min</span>
                </div>
                <p class="movie-description"><?php echo $pelicula['sinopsis']; ?></p>
              </div>
              
              <div class="movie-footer">
                <div class="movie-buttons">
                  <button class="btn btn-tickets">
                    <a href="Funciones.php?id=<?php echo $pelicula['idpelicula']; ?>&idCine=<?php echo $idCine; ?>" class="btn btn-tickets">
                        <i class="fas fa-ticket-alt"></i> Comprar entradas
                    </a>
                  </button>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-results">
          <i class="fas fa-film fa-3x" style="color: #ddd; margin-bottom: 20px;"></i>
          <p>No se encontraron películas disponibles para los filtros seleccionados.</p>
          <p>Por favor, seleccione otro cine o fecha.</p>
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
    // Manejar el botón de aplicar filtros
    document.querySelector('.btn-tickets[style*="width: 100%"]').addEventListener('click', function() {
      const cine = document.querySelector('.filter-select[name="idCine"]').value;
      const fecha = document.querySelector('.filter-select[name="fecha"]').value;
      
      if (!cine || !fecha) {
        alert('Por favor seleccione un cine y una fecha');
        return false;
      }
    });
  </script>

</body>
</html>