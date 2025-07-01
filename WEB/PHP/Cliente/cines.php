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
      <a href="#">Cines</a>
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