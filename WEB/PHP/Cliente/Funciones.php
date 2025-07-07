<?php
// Conexión a PostgreSQL
include_once("../../CONNECTION/conexion.php");
session_start();

try {
  $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $db_user, $db_pass);
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
      // Consulta ACTUALIZADA para obtener cine con dirección y ciudad
      $stmt = $pdo->prepare("
        SELECT 
            Cine.idCine, 
            Cine.Nombre_cine AS nombre_cine,
            Cine.ubicacion AS direccion, 
            Ciudad.NombreCiudad AS ciudad
        FROM Cine 
        LEFT JOIN Ciudad ON Cine.idCiudad = Ciudad.idCiudad
        WHERE Cine.idCine = :idCine
    ");
      $stmt->bindParam(':idCine', $idCine);
      $stmt->execute();
      $cine = $stmt->fetch(PDO::FETCH_ASSOC);

      // Combinar dirección y ciudad
      if ($cine) {
        $ubicacion = '';
        if (!empty($cine['direccion'])) {
          $ubicacion = $cine['direccion'];
        }
        if (!empty($cine['ciudad'])) {
          $ubicacion .= ($ubicacion ? ', ' : '') . $cine['ciudad'];
        }
        $cine['ubicacion'] = $ubicacion ?: 'Ubicación no disponible'; // Guardar en misma clave
      }
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
  <link rel="stylesheet" href="../../CSS/Client/Funciones.css">
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
      <a href="peliculas.php">Películas</a>
      <a href="cines.php">Cines</a>
      <a href="#">Promociones</a>
      <a href="#">Socios</a>
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
          <span><i class="fas fa-user"></i> <?php echo '+14'; ?></span>
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
                <a href="Asientos.php?idFuncion=<?php echo $horario['idFuncion']; ?>" class="showtime-btn">
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
        <a href="Ciudades/vista_ciudades.php"><i class="fas fa-map-marker-alt"></i></a>
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