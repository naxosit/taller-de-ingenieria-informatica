<?php
include_once("../../CONNECTION/conexion.php");
session_start();

// Mapeo de nombres de imágenes a IDs de cines
$imagenesCines = [
    1 => 'cine1.jpeg',
    2 => 'cine2.jpeg',
    3 => 'cine3.jpeg',
    4 => 'cine4.jpeg'
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
            CI.NombreCiudad AS ubicacion,
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
  <link rel="stylesheet" href="../../CSS/Client/index.css">
  <style>
    /* Estilos específicos para la página de cines */
    .cines-container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
    }
    
    .cines-header {
      text-align: center;
      margin-bottom: 40px;
    }
    
    .cines-header h1 {
      color: #1a237e;
      font-size: 2.5rem;
      margin-bottom: 10px;
    }
    
    .cines-header p {
      color: #555;
      font-size: 1.2rem;
      max-width: 700px;
      margin: 0 auto;
    }
    
    .cine-card {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      margin-bottom: 30px;
      transition: transform 0.3s ease;
      display: flex;
    }
    
    .cine-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    
    .cine-image {
      width: 300px;
      overflow: hidden;
      position: relative;
    }
    
    .cine-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }
    
    .cine-card:hover .cine-image img {
      transform: scale(1.05);
    }
    
    .cine-details {
      padding: 20px;
      flex: 1;
    }
    
    .cine-name {
      font-size: 1.5rem;
      color: #1a237e;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
    }
    
    .cine-name i {
      margin-right: 10px;
      color: #ff5722;
    }
    
    .cine-location {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
      color: #555;
      font-size: 1.1rem;
    }
    
    .cine-location i {
      margin-right: 8px;
      color: #1a237e;
      font-size: 1.2rem;
    }
    
    .cine-formats {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 15px;
    }
    
    .format-tag {
      background: #e3f2fd;
      color: #1a237e;
      padding: 6px 15px;
      border-radius: 20px;
      font-size: 0.95rem;
      font-weight: 500;
    }
    
    .cine-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 30px;
    }
    
    .cine-features {
      display: flex;
      gap: 15px;
      margin-top: 15px;
      flex-wrap: wrap;
    }
    
    .feature {
      display: flex;
      align-items: center;
      gap: 5px;
      color: #555;
      background: #f5f5f5;
      padding: 5px 10px;
      border-radius: 5px;
    }
    
    .feature i {
      color: #1a237e;
    }
    
    .logo-section {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 15px;
    }
    
    .cine-logo {
      width: 40px;
      height: 40px;
      object-fit: contain;
    }
    
    @media (max-width: 768px) {
      .cine-card {
        flex-direction: column;
      }
      
      .cine-image {
        width: 100%;
        height: 200px;
      }
      
      .cines-header h1 {
        font-size: 2rem;
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
      <a href="#" class="active">Cines</a>
      <a href="#">Promociones</a>
      <a href="#">Confitería</a>
    </div>

    <div class="actions">
      <a href="../login.php" class="btn btn-login">
        <i class="fas fa-user"></i>
        <span>Iniciar sesión</span>
      </a>
      <button class="btn btn-register">
        <i class="fas fa-user-plus"></i>
        <span>Registrarse</span>
      </button>
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
              <span><?php echo htmlspecialchars($cine['ubicacion']); ?></span>
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