<?php
include_once("../../CONNECTION/conexion.php");
session_start();
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
  <link rel="stylesheet" href="../../CSS/confiteria.css">

    <style>
    /* Estilos para la sección de confitería */
    .titulo-seccion {
        color: #4F42B5;
        text-align: center;
        margin: 30px 0;
        font-size: 2.2em;
    }

    .subtitulo-categoria {
        color: #4F42B5;
        margin: 30px 0 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #4F42B5;
        font-size: 1.5em;
    }

    .pelicula-card {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 25px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .pelicula-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }

    .pelicula-content {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
    }

    .pelicula-imagen {
        width: 200px;
        height: 200px;
        object-fit: cover;
        border-radius: 8px 0 0 8px;
    }

    .detalles-pelicula {
        padding: 20px;
        flex: 1;
    }

    .detalles-pelicula h2 {
        color: #333;
        margin-top: 0;
        margin-bottom: 10px;
        font-size: 1.4em;
    }

    .meta-info {
        display: flex;
        gap: 20px;
        margin-bottom: 10px;
    }

    .meta-info p {
        margin: 0;
        color: #555;
        font-size: 0.95em;
    }

    .meta-info strong {
        color: #333;
    }

    .sinopsis {
        color: #555;
        line-height: 1.5;
        margin-bottom: 0;
    }

    .mensaje-vacio {
        text-align: center;
        padding: 30px;
        background: #f8f8f8;
        border-radius: 8px;
        color: #666;
        margin: 30px 0;
    }

    .mensaje-error {
        text-align: center;
        padding: 20px;
        background: #ffecec;
        border-radius: 8px;
        color: #4F42B5;
        margin: 30px 0;
        border: 1px solid #ffb8b8;
    }

    /* Estilos responsive */
    @media (max-width: 768px) {
        .pelicula-content {
            flex-direction: column;
        }

        .pelicula-imagen {
            width: 100%;
            height: auto;
            border-radius: 8px 8px 0 0;
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
      <a href="../Cliente/Index.php">Incio</a>
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

 <main class="cartelera-container">
        <h1 class="titulo-seccion">Nuestra Confitería</h1>
        
        <?php
        try {
            // Obtener productos de confitería
            $queryProductos = "SELECT nombre, descripcion, categoria, precio, imagen FROM confiteria ORDER BY categoria, nombre";
            $stmtProductos = $conn->prepare($queryProductos);
            $stmtProductos->execute();
            $productos = $stmtProductos->fetchAll(PDO::FETCH_ASSOC);

            if (count($productos) > 0):
                $categoria_actual = '';
                foreach ($productos as $producto):
                    // Mostrar título de categoría si cambió
                    if ($producto['categoria'] != $categoria_actual) {
                        $categoria_actual = $producto['categoria'];
                        echo '<h2 class="subtitulo-categoria">' . htmlspecialchars(ucfirst($categoria_actual)) . '</h2>';
                    }
        ?>
        
        <div class="pelicula-card">
            <div class="pelicula-content">
                <img src="<?= htmlspecialchars($producto['imagen']) ?>" 
                     class="pelicula-imagen" 
                     alt="<?= htmlspecialchars($producto['nombre']) ?>"
                     onerror="this.src='https://via.placeholder.com/150'">

                <div class="detalles-pelicula">
                    <h2><?= htmlspecialchars($producto['nombre']) ?></h2>
                    <div class="meta-info">
                        <p><strong>Precio:</strong> $<?= number_format($producto['precio'], 0, ',', '.') ?></p>
                        <p><strong>Categoría:</strong> <?= htmlspecialchars(ucfirst($producto['categoria'])) ?></p>
                    </div>
                    <p class="sinopsis"><?= nl2br(htmlspecialchars($producto['descripcion'])) ?></p>
                </div>
            </div>
        </div>

        <?php
                endforeach;
            else:
                echo '<div class="mensaje-vacio">No hay productos disponibles en este momento.</div>';
            endif;
        } catch (PDOException $e) {
            echo '<div class="mensaje-error">Error al cargar los productos: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
    </main>

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
        <p>Este sitio es solo con fines educativos.</p>
      </div>
    </div>
  </footer>


</body>

</html>