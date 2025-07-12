<?php
// Conexión a PostgreSQL
include_once("../../CONNECTION/conexion.php");
session_start();

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener ID de la función desde la URL
    $idFuncion = isset($_GET['idFuncion']) ? $_GET['idFuncion'] : null;
    
    if ($idFuncion) {
        // Consulta para obtener detalles de la función
        $stmt = $pdo->prepare("
            SELECT 
                F.*, 
                P.nombre AS nombre_pelicula,
                P.imagen,
                P.duracion,
                P.genero,
                S.nombre AS nombre_sala,
                C.nombre_cine,
                C.ubicacion AS direccion,
                CI.nombreciudad AS ciudad
            FROM funcion F
            JOIN pelicula P ON F.id_pelicula = P.idpelicula
            JOIN sala S ON F.id_sala = S.idsala
            JOIN cine C ON S.cine_idcine = C.idcine
            JOIN ciudad CI ON C.idciudad = CI.idciudad  -- Unimos con la tabla Ciudad
            WHERE F.idfuncion = :idFuncion
        ");
        $stmt->bindParam(':idFuncion', $idFuncion);
        $stmt->execute();
        $funcion = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($funcion) {
            //Combiar la dirección y ciudad
            $ubicacion = '';
            if (!empty($funcion['direccion'])){
              $ubicacion = $funcion['direccion'];
            }
            if (!empty($funcion['ciudad'])){
              $ubicacion .= ($ubicacion ? ',':'').$funcion['ciudad'];
            }   
            $funcion['ubicacion'] = $ubicacion ?: 'Ubicación no disponible';       
            // Consulta para obtener asientos ocupados en esta función
            $stmt = $pdo->prepare("
                SELECT B.idbutaca
                FROM boleto B
                WHERE B.idfuncion = :idFuncion
                AND B.activo = true
            ");
            $stmt->bindParam(':idFuncion', $idFuncion);
            $stmt->execute();
            $asientosOcupados = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Consulta para obtener todos los asientos de la sala
            $stmt = $pdo->prepare("
                SELECT id_butaca, fila, columna 
                FROM butaca 
                WHERE id_sala = :idSala
                ORDER BY fila, CAST(columna AS INTEGER)
            ");
            $stmt->bindParam(':idSala', $funcion['id_sala']);
            $stmt->execute();
            $asientosSala = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Si no se encontró la función, redirigir
if (!$idFuncion || !$funcion || !$asientosSala) {
    header("Location: peliculas.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Butacas - Cine Azul</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../CSS/Client/Asientos.css">
    <link rel="stylesheet" href="../../CSS/Client/menusuario.css">
</head>
<body>
  <!-- Barra de navegación -->
  <nav class="navbar">
    <div class="logo">
      <i class="fas fa-film"></i>
      <span>Cine Azul</span>
    </div>

    <div class="menu">
      <a href="Index.php">Inicio</a>
      <a href="peliculas.php">Películas</a>
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
    </div>
  </nav>

  <!-- Contenido principal -->
  <div class="main-container">
    <div class="movie-header">
      <div class="movie-poster">
        <img src="<?php echo htmlspecialchars($funcion['imagen']); ?>" alt="<?php echo htmlspecialchars($funcion['nombre_pelicula']); ?>">
      </div>
      
      <div class="movie-info">
        <h1 class="movie-title"><?php echo htmlspecialchars($funcion['nombre_pelicula']); ?></h1>
        
        <div class="movie-meta">
          <span><i class="fas fa-film"></i> <?php echo htmlspecialchars($funcion['genero']); ?></span>
          <span><i class="fas fa-clock"></i> <?php echo htmlspecialchars($funcion['duracion']); ?> min</span>
          <span><i class="fas fa-user"></i> <?php echo '+14'; ?></span>
        </div>
        
        <h3 class="section-title">Detalles de la Función</h3>
      </div>
    </div>
    
    <!-- Información de la función -->
    <div class="function-info">
      <div class="info-item">
        <h3><i class="fas fa-calendar-alt"></i> Fecha y Hora</h3>
        <p><?php echo date('l, d F Y - H:i', strtotime($funcion['fechahora'])); ?></p>
      </div>
      
      <div class="info-item">
        <h3><i class="fas fa-map-marker-alt"></i> Cine</h3>
        <p><?php echo htmlspecialchars($funcion['nombre_cine']); ?></p>
        <p><?php echo htmlspecialchars($funcion['ubicacion']); ?></p>
      </div>
      
      <div class="info-item">
        <h3><i class="fas fa-door-open"></i> Sala</h3>
        <p><?php echo htmlspecialchars($funcion['nombre_sala']); ?></p>
      </div>
    </div>
    
    <!-- Sala de butacas -->
    <div class="seating-container">
      <h2 class="section-title">Selecciona tus Asientos</h2>
      
      <div class="screen"></div>
      
      <div class="seating-grid" id="seatingGrid">
        <?php
        // Agrupar asientos por fila
        $asientosPorFila = [];
        foreach ($asientosSala as $asiento) {
            $fila = $asiento['fila'];
            $asientosPorFila[$fila][] = $asiento;
        }
        
        // Generar filas de butacas
        foreach ($asientosPorFila as $fila => $asientos) {
            echo '<div class="seat-row">';
            foreach ($asientos as $asiento) {
                $ocupado = in_array($asiento['id_butaca'], $asientosOcupados);
                $label = $asiento['fila'] . $asiento['columna'];
                $clase = $ocupado ? 'occupied' : 'available';
                
                echo '<div class="seat ' . $clase . '" 
                        data-id="' . $asiento['id_butaca'] . '" 
                        data-label="' . $label . '"
                        data-row="' . $asiento['fila'] . '" 
                        data-col="' . $asiento['columna'] . '">';
                echo $label;
                echo '</div>';
            }
            echo '</div>'; // Cierre de seat-row
        }
        ?>
      
      <div class="seating-info">
        <div class="info-item">
          <div class="info-box available-box"></div>
          <span>Disponible</span>
        </div>
        <div class="info-item">
          <div class="info-box occupied-box"></div>
          <span>Ocupado</span>
        </div>
        <div class="info-item">
          <div class="info-box selected-box"></div>
          <span>Seleccionado</span>
        </div>
      </div>
      
      <div class="selection-summary">
        <h3 class="summary-title">Tu Selección</h3>
        <div class="selected-seats" id="selectedSeats">
          <!-- Las butacas seleccionadas aparecerán aquí -->
        </div>
        
        <div class="total-price" id="totalPrice">
          Total: $0.00
        </div>
        
        <button class="btn btn-confirm" id="confirmButton">
          <i class="fas fa-ticket-alt"></i>
          Confirmar y Pagar
        </button>
      </div>
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
    // Precio por asiento (podría venir de la base de datos)
    const seatPrice = 2500;
    const idFuncion = <?php echo $idFuncion; ?>;
    
    // Alternar selección de asiento
    function toggleSeatSelection(event) {
      const seat = event.currentTarget;
      
      // Solo permitir selección si está disponible
      if (seat.classList.contains('available')) {
        seat.classList.toggle('selected');
        updateSelectionSummary();
      }
    }
    
    // Actualizar resumen de selección
    function updateSelectionSummary() {
      const selectedSeats = document.querySelectorAll('.seat.selected');
      const selectedSeatsContainer = document.getElementById('selectedSeats');
      const totalPriceElement = document.getElementById('totalPrice');
      
      selectedSeatsContainer.innerHTML = '';
      
      selectedSeats.forEach(seat => {
        const seatLabel = seat.dataset.label;
        const seatTag = document.createElement('div');
        seatTag.className = 'seat-tag';
        seatTag.innerHTML = `<i class="fas fa-chair"></i> ${seatLabel}`;
        selectedSeatsContainer.appendChild(seatTag);
      });
      
      const totalPrice = selectedSeats.length * seatPrice;
      totalPriceElement.textContent = `Total: $${totalPrice.toFixed(2)}`;
      
      const confirmButton = document.getElementById('confirmButton');
      confirmButton.disabled = selectedSeats.length === 0;
    }
    
    // Evento para el botón de confirmación
    document.getElementById('confirmButton').addEventListener('click', function() {
      const selectedSeats = document.querySelectorAll('.seat.selected');
      
      if (selectedSeats.length > 0) {
        const asientosIds = Array.from(selectedSeats).map(seat => seat.dataset.id);
        
        // Crear formulario dinámico para enviar datos
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'procesar_compra.php';
        
        // Input para idFuncion
        const inputFuncion = document.createElement('input');
        inputFuncion.type = 'hidden';
        inputFuncion.name = 'idFuncion';
        inputFuncion.value = idFuncion;
        form.appendChild(inputFuncion);
        
        // Inputs para asientos seleccionados
        asientosIds.forEach(id => {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'asientos[]';
          input.value = id;
          form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
      }
    });
    
    // Añadir eventos a los asientos disponibles
    document.querySelectorAll('.seat.available').forEach(seat => {
      seat.addEventListener('click', toggleSeatSelection);
    });
  </script>
</body>
</html>