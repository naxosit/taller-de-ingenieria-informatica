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
        .welcome-message {
    font-size: 1.2rem;
    max-width: 700px;
    text-align: center;
    margin: 0 auto 50px;
    line-height: 1.7;
    padding: 20px;
    border-radius: 15px;
    background: rgba(25, 118, 210, 0.2);
    border-left: 4px solid #4F42B5;
    }
    
    /* Contenedor de botones */
    .buttons-container {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 40px;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .big-button {
      width: 300px;
      height: 300px;
      background: rgba(13, 71, 161, 0.8);
      border-radius: 20px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-decoration: none;
      color: white;
      transition: all 0.4s ease;
      position: relative;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
      border: 2px solid rgba(255, 152, 0, 0.3);
      text-align: center;
      padding: 20px;
    }
    
    .big-button:hover {
      transform: translateY(-15px) scale(1.05);
      background: rgba(13, 71, 161, 0.95);
      box-shadow: 0 15px 35px rgba(25, 118, 210, 0.4);
      border-color: #ff9800;
    }
    
    .big-button i {
      font-size: 70px;
      margin-bottom: 25px;
      color: #ff9800;
      transition: all 0.3s ease;
    }
    
    .big-button:hover i {
      transform: scale(1.2);
      color: #ffeb3b;
    }
    
    .big-button h2 {
      font-size: 28px;
      margin-bottom: 15px;
      font-weight: 600;
      letter-spacing: 1px;
    }
    
    .big-button p {
      font-size: 16px;
      line-height: 1.5;
      color: #e0e0e0;
    }
    
    .big-button::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(45deg, transparent, rgba(255, 152, 0, 0.1), transparent);
      transition: all 0.6s ease;
      transform: rotate(45deg);
    }
    
    .big-button:hover::before {
      top: 0;
      left: 0;
      transform: rotate(0);
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
      <a href="../Cliente/Index.php">Inicio</a>
      <a href="../Cliente/peliculas.php">Películas</a>
      <a href="../Cliente/cines.php">Cines</a>
      <a href="../Cliente/Promociones/promociones.php">Promociones</a>
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
            <a href="../Cliente/mis_boletos.php" class="dropdown-item">
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

  <div class="main-container">
    <h1 class="page-title">Gestión confitería y promociones</h1>
    
    <div class="welcome-message">
      <p>Bienvenido al panel de gestión de confiteria y promociones. Desde aquí podras agregar productos
        que se añadirán a la confitería del cine y gestionarlos, además de programar y gestionar las promociones semanales.
      </p>
    </div>
    
    <div class="buttons-container">
      <a href="Confiteria/confiteriadmin.php" class="big-button">
        <i class="fa-solid fa-burger"></i>
        <h2>Productos</h2>
        <p>Gestiona los productos que se encontrarán en la sección de confitería en tu cine.</p>
      </a>
      
      <a href="ModPromos/modificar_promociones.php" class="big-button">
        <i class="fa-solid fa-gift"></i>
        <h2>Promociones</h2>
        <p>Desde aquí podrá ver las promociones existentes correspondiende a su día, además de agregar algunas nuevas.
        </p>
      </a>
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
        <p>Este sitio es únicamente para fines educativos.</p>
      </div>
    </div>
  </footer>

  <script src="../../JS/Carrusel.js"></script>

</body>

</html>