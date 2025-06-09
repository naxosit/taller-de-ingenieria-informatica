<?php
require __DIR__ . '/../CONNECTION/conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnlogin'])) {
    $rut = $_POST['rut'];
    $password = $_POST['password'];

    if (empty($rut) || empty($password)) {
        $error = "Todos los campos son obligatorios.";
    } else {
      $stmt = $db->prepare(" SELECT c.contraseñausuario AS password, p.rol as rol
          FROM perfil p
          INNER JOIN contraseña c ON p.id_contraseña = c.id_contraseña
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
              header("Location: Enc_cartelera/Cartelera.php");
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
  <link rel="stylesheet" href="../CSS/styles.css" />
  <link rel="stylesheet" href="../CSS/botones.css" />
</head>
<body>
  <header>
    <div class="logo">Web Cine</div>
    <nav>
      <a href="Enc_cartelera/Cartelera.php">Cartelera</a>
      <a href="#"></a>
      <a href="#"></a>
      <a href="#"></a>
    </nav>
  </header>

  <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
  <div class="mensaje-exito">Cuenta creada exitosamente. Por favor, inicia sesión.</div>
  <?php endif; ?>


  <main>
    <section class="formulario">
      <h1>Iniciar Sesión</h1>

      <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form action="login.php" method="post">
        <div class="input-box">
          <input type="text" name="rut" required />
          <label>RUT</label>
        </div>
        <div class="input-box">
          <input type="password" name="password" required />
          <label>Contraseña</label>
        </div>
        <input class="boton" name="btnlogin" type="submit" value="Ingresar" />
        <div class="registrarse">
          <a href="registro.php">¿No tienes cuenta?, Regístrate</a>
        </div>
      </form>
    </section>
  </main>
</body>
</html>
