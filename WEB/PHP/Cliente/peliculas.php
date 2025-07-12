<?php
// Conexión a PostgreSQL
include_once("../../CONNECTION/conexion.php");
session_start();
try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener todas las ciudades
    $stmt = $pdo->query("SELECT idCiudad, NombreCiudad FROM Ciudad");
    $ciudades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener TODOS los cines con su ciudad asociada
    $stmt = $pdo->query("SELECT c.idCine, c.Nombre_cine, c.idCiudad FROM Cine c");
    $todosLosCines = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Convertir cines a JSON para JavaScript
    $cinesJson = json_encode($todosLosCines);
    
    // Manejar filtros
    $idCiudad = isset($_GET['idCiudad']) ? $_GET['idCiudad'] : null;
    $idCine = isset($_GET['idCine']) ? $_GET['idCine'] : null;
    $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
    
    // Obtener cines según ciudad seleccionada (para carga inicial)
    $cines = [];
    if ($idCiudad) {
        $query = "SELECT idCine, Nombre_cine FROM Cine WHERE idCiudad = :idCiudad";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':idCiudad', $idCiudad);
        $stmt->execute();
        $cines = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener películas con funciones disponibles
    $peliculas = [];
    if ($idCiudad && $idCine && $fecha) {
        $query = "SELECT DISTINCT P.idPelicula, P.Nombre, P.Genero, P.Imagen, P.Duracion, P.Sinopsis 
                  FROM Pelicula P
                  INNER JOIN Funcion F ON P.idPelicula = F.Id_Pelicula
                  INNER JOIN Sala S ON F.Id_Sala = S.idSala
                  WHERE S.Cine_idCine = :idCine 
                  AND DATE(F.FechaHora) = :fecha";
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
  <link rel="stylesheet" href="../../CSS/Client/Peliculas.css">
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
      <a href="#" class="active">Películas</a>
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

  <!-- Contenido principal -->
  <div class="main-content">
    <!-- Panel de filtros -->
    <div class="filters-panel">
      <h2 class="filters-title">Filtra Por:</h2>
      <form method="GET" action="" id="filters-form">
        <div class="filter-group-city">
          <h3><i class="fas fa-city city-icon"></i> Ciudad</h3>
          <select class="filter-select-city" name="idCiudad" id="idCiudad" required>
            <option value="">Seleccione una ciudad</option>
            <?php foreach ($ciudades as $ciudad): ?>
              <option value="<?php echo $ciudad['idciudad']; ?>" <?php echo (isset($_GET['idCiudad']) && $_GET['idCiudad'] == $ciudad['idciudad']) ? 'selected' : ''; ?>>
                <?php echo $ciudad['nombreciudad']; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div class="filter-group">
          <h3><i class="fas fa-film"></i> Cine</h3>
          <select class="filter-select" name="idCine" id="idCine" required>
            <option value="">Seleccione un cine</option>
            <?php if (!empty($cines)): ?>
              <?php foreach ($cines as $cine): ?>
                <option value="<?php echo $cine['idcine']; ?>" 
                  <?php echo (isset($_GET['idCine']) && $_GET['idCine'] == $cine['idcine']) ? 'selected' : ''; ?>>
                  <?php echo $cine['nombre_cine']; ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <div class="filter-group">
          <h3><i class="fas fa-calendar-day"></i> Fecha</h3>
          <input type="date" class="filter-select" name="fecha" id="fecha" value="<?php echo $fecha; ?>" required>
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
    // Almacenar todos los cines en JavaScript
    const todosLosCines = <?php echo $cinesJson; ?>;
    
    // Actualizar cines cuando cambia la ciudad
    document.getElementById('idCiudad').addEventListener('change', function() {
      const ciudadId = this.value;
      const cineSelect = document.getElementById('idCine');
      
      // Vaciar y resetear selector de cines
      cineSelect.innerHTML = '<option value="">Seleccione un cine</option>';
      
      if (ciudadId) {
        // Filtrar cines por la ciudad seleccionada
        const cinesFiltrados = todosLosCines.filter(cine => cine.idciudad == ciudadId);
        
        // Agregar opciones al selector
        cinesFiltrados.forEach(cine => {
          const option = document.createElement('option');
          option.value = cine.idcine;
          option.textContent = cine.nombre_cine;
          cineSelect.appendChild(option);
        });
      }
    });
    
    // Validar formulario antes de enviar
    document.getElementById('filters-form').addEventListener('submit', function(e) {
      const ciudad = document.getElementById('idCiudad').value;
      const cine = document.getElementById('idCine').value;
      const fecha = document.getElementById('fecha').value;
      
      if (!ciudad || !cine || !fecha) {
        alert('Por favor seleccione ciudad, cine y fecha');
        e.preventDefault();
      }
    });
  </script>

</body>
</html>