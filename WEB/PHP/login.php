<?php
require __DIR__ . '/../CONNECTION/conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnlogin'])) {
    $rut = $_POST['rut'];
    $password = $_POST['password'];

    if (empty($rut) || empty($password)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        // Normalizar el formato del RUT (asegurar formato con puntos y guión)
        $rut = strtoupper($rut);  // Convertir a mayúsculas
        
        // Verificar si ya tiene formato completo
        if (!preg_match('/^\d{1,2}\.\d{3}\.\d{3}-[0-9K]$/', $rut)) {
            // Quitar puntos y guiones existentes
            $rutClean = str_replace(['.', '-'], '', $rut);
            
            // Extraer cuerpo y dígito verificador
            $cuerpo = substr($rutClean, 0, -1);
            $dv = substr($rutClean, -1);
            
            // Formatear correctamente con puntos y guión
            if (strlen($cuerpo) > 6) {
                $rut = substr($cuerpo, 0, -6) . '.' . substr($cuerpo, -6, -3) . '.' . substr($cuerpo, -3) . '-' . $dv;
            } elseif (strlen($cuerpo) > 3) {
                $rut = substr($cuerpo, 0, -3) . '.' . substr($cuerpo, -3) . '-' . $dv;
            } else {
                $rut = $cuerpo . '-' . $dv;
            }
        }
        
        $stmt = $db->prepare("SELECT c.contraseñausuario AS password, p.rol as rol
                            FROM perfil p
                            INNER JOIN contraseña c ON p.rut = c.rut
                            WHERE p.rut = ?");
        $stmt->execute([$rut]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && $usuario['password'] === $password) {
            $_SESSION['rut'] = $rut;
            $_SESSION['rol'] = $usuario['rol'];
        
            switch (strtolower($usuario['rol'])) {
                case 'admin':
                    header("Location: admin/vista_admin.php");
                    break;
                case 'encargado cartelera':
                    header("Location: Enc_cartelera/vista_encargado.php");
                    break;
                case 'encargado butaca':
                    header("Location: encargado_butaca.php");
                    break;
                default:
                    header("Location: Cliente/Index.php");
            }
            exit();
        } else {
            $error = "RUT o contraseña incorrectos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Web Cine</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../CSS/Client/styles.css" />
  <link rel="stylesheet" href="../CSS/Client/login.css" />
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

  <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
  <div class="mensaje-exito">Cuenta creada exitosamente. Por favor, inicia sesión.</div>
  <?php endif; ?>

  <div class="login-container">
    <div class="login-box">
      <div class="login-header">
        <h1>Iniciar Sesión</h1>
        <p>Accede a tu cuenta para disfrutar de la mejor experiencia cinematográfica</p>
      </div>

      <div class="login-body">
        <?php if (isset($error)): ?>
          <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="login.php" method="post" class="form-container">
          <div class="input-group">
            <label for="rut">RUT</label>
            <input type="text" id="rut" name="rut" required placeholder="Ingresa tu RUT" />
            <i class="fas fa-id-card"></i>
          </div>
          
          <div class="input-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required placeholder="Ingresa tu contraseña" />
            <i class="fas fa-lock"></i>
          </div>
          
          <button class="btn-submit" name="btnlogin" type="submit">
            <i class="fas fa-sign-in-alt"></i> Ingresar
          </button>
          
          <div class="form-footer">
            <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
            <p><a href="#">¿Olvidaste tu contraseña?</a></p>
          </div>
        </form>
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

  <script>
    // Pequeña animación para los inputs al enfocar
    document.querySelectorAll('.input-group input').forEach(input => {
      input.addEventListener('focus', function() {
        this.parentNode.querySelector('i').style.color = 'var(--primary-dark)';
      });
      
      input.addEventListener('blur', function() {
        this.parentNode.querySelector('i').style.color = 'var(--primary)';
      });
    });
  </script>

  <script>
// Función para validar RUT chileno con módulo 11
function validarRut(rutCompleto) {
  // Eliminar puntos y guiones, convertir a mayúsculas
  rutCompleto = rutCompleto.replace(/\./g, '').replace(/\-/g, '').toUpperCase();
  
  // Separar cuerpo y dígito verificador
  const cuerpo = rutCompleto.slice(0, -1);
  const dv = rutCompleto.slice(-1);
  
  // Validar que el cuerpo sea numérico y tenga 7-8 dígitos
  if (!/^\d{7,8}$/.test(cuerpo)) {
    return false;
  }

}

// Función para formatear RUT mientras se escribe
function formatearRut(rut) {
  // Eliminar caracteres no válidos
  rut = rut.replace(/[^0-9kK\-]/g, '').toUpperCase();
  
  // Separar cuerpo y DV
  let cuerpo = rut.slice(0, -1);
  const dv = rut.slice(-1);
  
  // Eliminar puntos existentes
  cuerpo = cuerpo.replace(/\./g, '');
  
  // Limitar cuerpo a 8 dígitos (máximo permitido en Chile)
  if (cuerpo.length > 8) cuerpo = cuerpo.substring(0, 8);
  
  // Agregar puntos cada 3 dígitos (de derecha a izquierda)
  let cuerpoFormateado = '';
  for (let i = cuerpo.length - 1, j = 1; i >= 0; i--, j++) {
    cuerpoFormateado = cuerpo[i] + cuerpoFormateado;
    if (j % 3 === 0 && i > 0) {
      cuerpoFormateado = '.' + cuerpoFormateado;
    }
  }
  
  // Combinar cuerpo formateado con DV
  return cuerpoFormateado + '-' + dv;
}

// Reemplazar el script existente por este:
document.getElementById('rut').addEventListener('input', function(e) {
    let rut = e.target.value.replace(/\./g, '').replace(/-/g, '');
    
    // Limitar a 9 caracteres (8 números + 1 dígito verificador)
    if (rut.length > 9) rut = rut.substring(0, 9);
    
    // Formatear solo si hay más de 1 carácter
    if (rut.length > 1) {
        // Separar cuerpo y DV
        const cuerpo = rut.slice(0, -1);
        const dv = rut.slice(-1).toUpperCase();
        
        // Formatear con puntos cada 3 dígitos
        let formatted = '';
        for (let i = 0, j = cuerpo.length - 1; i < cuerpo.length; i++, j--) {
            formatted = cuerpo[j] + formatted;
            if (i % 3 === 2 && i < cuerpo.length - 1) {
                formatted = '.' + formatted;
            }
        }
        
        e.target.value = formatted + '-' + dv;
    }
});
</script>
</body>
</html>