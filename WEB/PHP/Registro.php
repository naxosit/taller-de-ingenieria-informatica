<?php
require __DIR__ . '/../CONNECTION/conexion.php';

// 1. Función para validar RUT chileno (módulo 11)
function validarRut($rutCompleto) {
    $rut = preg_replace('/[^0-9kK]/', '', $rutCompleto);
    $dv    = strtoupper(substr($rut, -1));
    $cuerpo = substr($rut, 0, -1);

    if (!ctype_digit($cuerpo) || strlen($cuerpo) < 7) {
        return false;
    }

    $suma = 0;
    $multiplo = 2;
    for ($i = strlen($cuerpo) - 1; $i >= 0; $i--) {
        $suma += $cuerpo[$i] * $multiplo;
        $multiplo = ($multiplo == 7) ? 2 : $multiplo + 1;
    }

    $resto = $suma % 11;
    $dvEsperado = 11 - $resto;
    if ($dvEsperado == 11) {
        $dvEsperado = '0';
    } elseif ($dvEsperado == 10) {
        $dvEsperado = 'K';
    } else {
        $dvEsperado = (string)$dvEsperado;
    }

    return $dv === $dvEsperado;
}

// 2. Procesar envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnregistrar'])) {
    $rut = trim($_POST['rut']);
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // 2a. Validar RUT antes de cualquier otra validación
    if (!validarRut($rut)) {
        $error = "RUT inválido. Por favor ingresa un RUT válido.";
    } else {
        // Campos obligatorios
        if (empty($rut) || empty($nombre) || empty($apellido) || empty($email) || empty($password)) {
            $error = "Todos los campos son obligatorios y no pueden contener solo espacios.";
        } else {
            try {
                // Verificar si el RUT ya existe
                $stmt = $db->prepare("SELECT COUNT(*) FROM Perfil WHERE Rut = ?");
                $stmt->execute([$rut]);
                $existe = $stmt->fetchColumn();

                if ($existe > 0) {
                    $error = "El RUT $rut ya se encuentra registrado. Por favor, utiliza otro RUT.";
                } else {
                    $stmt = $db->prepare("INSERT INTO Perfil (Rut, Nombre, Apellido, Correo_Electronico, Rol)
                                          VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$rut, $nombre, $apellido, $email, 'cliente']);

                    $stmt = $db->prepare("INSERT INTO Contraseña (ContraseñaUsuario, Rut) VALUES (?, ?)");
                    $stmt->execute([$password, $rut]);

                    header("Location: login.php?success=1");
                    exit;
                }
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'chk_nombre_apellido_perfil') !== false) {
                    $error = "El RUT $rut ya se encuentra registrado. Por favor, utiliza otro RUT.";
                } else {
                    $error = "Error al registrar: " . $e->getMessage();
                }
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registro - Web Cine</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../CSS/Client/styles.css" />
  <link rel="stylesheet" href="../CSS/Client/registrar.css" />
</head>

<body>
  <header>
    <div class="logo">Web Cine</div>
    <!-- Barra de navegación-->
    <nav class="navbar">
      <div class="logo">
        <i class="fas fa-film"></i>
        <span>Cine Azul</span>
      </div>

      <div class="menu">
        <a href="Cliente/peliculas.php">Películas</a>
        <a href="Cliente/cines.php">Cines</a>
        <a href="#">Promociones</a>
        <a href="#">Confitería</a>
      </div>

      <div class="actions">
        <a href="login.php" class="btn btn-login">
          <i class="fas fa-user"></i>
          <span>Iniciar sesión</span>
        </a>
        <a href="Registro.php" class="btn btn-tickets">
          <i class="fas fa-user-plus"></i>
          <span>Registrarse</span>
        </a>
      </div>
    </nav>
  </header>

  <div class="login-container">
    <div class="login-box">
      <div class="login-header">
        <i class="fas fa-user-plus login-icon"></i>
        <h1>Registro de Usuario</h1>
        <p>Crea tu cuenta para disfrutar una mejor experiencia cinematografica</p>
      </div>

      <div class="login-body">
        <?php if (isset($error)): ?>
          <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="" method="post" class="form-container">
          <div class="input-group">
            <label for="rut">RUT (ej: 12345678-9)</label>
            <input type="text" id="rut" name="rut" required value="<?= isset($_POST['rut']) ? htmlspecialchars($_POST['rut']) : '' ?>" />
            <i class="fas fa-id-card"></i>
          </div>

          <div class="input-group">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" required value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>" />
            <i class="fas fa-user"></i>
          </div>

          <div class="input-group">
            <label for="apellido">Apellido</label>
            <input type="text" id="apellido" name="apellido" required value="<?= isset($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : '' ?>" />
            <i class="fas fa-signature"></i>
          </div>

          <div class="input-group">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" />
            <i class="fas fa-envelope"></i>
          </div>

          <div class="input-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required />
            <i class="fas fa-lock"></i>
          </div>

          <button class="btn-submit" name="btnregistrar" type="submit">
            <i class="fas fa-user-plus"></i> Registrarse
          </button>

          <div class="form-footer">
            <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
          </div>
        </form>
      </div>
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
      </div>

      <p>Disfruta del mejor cine en nuestras modernas salas con tecnología de última generación.</p>
      <p>© 2023 Cine Azul. Todos los derechos reservados.</p>
      <div class="copyright">
        <p>Este sitio es solo con fines educativos. Las imágenes son de Unsplash.</p>
      </div>
    </div>
  </footer>
  <script src="../JS/Modulo11.js"></script>
</body>

</html>