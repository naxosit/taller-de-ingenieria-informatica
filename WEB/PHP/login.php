<?php
require __DIR__ . '/../CONNECTION/conexion.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnlogin'])) {
    $rut = $_POST['rut'];
    $password = $_POST['password'];

    if (empty($rut) || empty($password)) {
        $error = "Todos los campos son obligatorios";
    } else {
        try {
            // Consulta optimizada con alias seguro para obtener el rut y la contraseña
            $stmt = $db->prepare("SELECT c.contraseña, p.rol 
                     FROM contraseña c
                     INNER JOIN perfil p ON c.rut = p.rut
                     WHERE c.rut = ?");
            $stmt->execute([$rut]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            //Aqui con el rut comparamos el rol que tenga el perfil y dependiendo de esta
            //redirecciona a su pagina.
            if ($usuario && $usuario['contraseña'] === $password) {
                $_SESSION['rut'] = $rut;
                $_SESSION['rol'] = $usuario['rol'];  

                // strtolower --> Pasa a todos los caracteres a minusculas
                if (strtolower($usuario['rol']) === 'admin') {
                    header("Location: vista_admin.php");
                    exit();
                } else {
                    header("Location: index.php");
                    exit();
                }
            } else {
                $error = "RUT o contraseña incorrectos";
            }
        } catch(PDOException $e) {
            $error = "Error en la base de datos: " . $e->getMessage();
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
</head>
<body>
  <header>
    <div class="logo">Web Cine</div>
    <nav>
      <a href="#">Inicio</a>
      <a href="#">Servicios</a>
      <a href="#">Productos</a>
      <a href="#">Contacto</a>
    </nav>
  </header>

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
          <a href="registro.php">¿No tienes cuenta? Regístrate</a>
        </div>
      </form>
    </section>
  </main>
</body>
</html>