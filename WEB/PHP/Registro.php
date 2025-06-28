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
  <style>
    .login-container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: calc(100vh - 80px - 100px);
      /* Altura total menos navbar y footer */
      padding: 40px 20px;
      background-color: var(--gray);
    }

    .login-box {
      width: 100%;
      max-width: 500px;
      background: var(--light);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: var(--shadow);
      transform: translateY(0);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .login-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    .login-header {
      background: var(--gradient);
      padding: 30px;
      text-align: center;
      color: var(--light);
    }

    .login-header h1 {
      font-size: 1.8rem;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .login-header p {
      opacity: 0.9;
      font-size: 0.95rem;
    }

    .login-icon {
      font-size: 3rem;
      margin-bottom: 15px;
      display: inline-block;
      background: rgba(255, 255, 255, 0.2);
      width: 80px;
      height: 80px;
      border-radius: 50%;
      line-height: 80px;
    }

    .login-body {
      padding: 30px;
    }

    .input-group {
      position: relative;
      margin-bottom: 25px;
    }

    .input-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: var(--text);
      font-size: 0.95rem;
    }

    .input-group input {
      width: 100%;
      padding: 14px 15px 14px 45px;
      border: 1px solid var(--gray-dark);
      border-radius: 6px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background-color: var(--gray);
    }

    .input-group input:focus {
      border-color: var(--primary-light);
      outline: none;
      box-shadow: 0 0 0 3px rgba(79, 66, 181, 0.2);
    }

    .input-group i {
      position: absolute;
      left: 15px;
      bottom: 14px;
      color: var(--primary);
      font-size: 1.1rem;
    }

    .btn-submit {
      width: 100%;
      padding: 14px;
      background: var(--gradient);
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 10px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    .btn-submit:hover {
      background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(79, 66, 181, 0.3);
    }

    .form-footer {
      text-align: center;
      margin-top: 25px;
      padding-top: 20px;
      border-top: 1px solid var(--gray-dark);
    }

    .form-footer p {
      margin: 8px 0;
      font-size: 0.9rem;
      color: var(--text);
    }

    .form-footer a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .form-footer a:hover {
      color: var(--primary-dark);
      text-decoration: underline;
    }

    .error {
      background-color: #ffebee;
      color: var(--danger);
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 6px;
      text-align: center;
      font-size: 0.95rem;
      border: 1px solid rgba(244, 67, 54, 0.2);
    }

    /* Animaciones */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .login-box {
      animation: fadeIn 0.5s ease-out;
    }

    /* Responsive */
    @media (max-width: 576px) {
      .login-header {
        padding: 20px;
      }

      .login-body {
        padding: 25px 20px;
      }

      .login-header h1 {
        font-size: 1.5rem;
      }
    }
  </style>
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
        <a href="peliculas.php">Películas</a>
        <a href="#">Cines</a>
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