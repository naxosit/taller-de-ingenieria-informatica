<?php
require __DIR__ . '/../CONNECTION/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnregistrar'])) {
    $rut = $_POST['rut'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($rut) || empty($nombre) || empty($apellido) || empty($email) || empty($password)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        try {
            // Insertar la contraseña y recuperar su ID.
            $stmt = $db->prepare("INSERT INTO Contraseña (ContraseñaUsuario) VALUES (?) RETURNING Id_Contraseña");
            $stmt->execute([$password]);
            $id_contraseña = $stmt->fetchColumn();

            // Insertar en "Perfil" con rol predeterminado 1 (cliente).
            $rol = 'cliente'; // Rol por defecto
            $stmt = $db->prepare("INSERT INTO Perfil (Rut, Nombre, Apellido, Correo_Electronico, Rol, Id_Contraseña) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$rut, $nombre, $apellido, $email, $rol, $id_contraseña]);

            exit;
        } catch(PDOException $e) {
            $error = "Error al registrar: " . $e->getMessage();
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
  <link rel="stylesheet" href="../CSS/styles.css" />
</head>
<body>
  <header>
    <div class="logo">Web Cine</div>
    <nav>
      <a href="#">Inicio</a>
      <a href="#">Cartelera</a>
      <a href="#">Películas</a>
      <a href="#"></a>
    </nav>
  </header>

  <main>
    <section class="formulario">
      <h1>Registro de Usuario</h1>
      
      <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      
      <form action="login.php" method="post">
        <div class="input-box">
          <input type="text" name="rut" required />
          <label>RUT (ej: 12345678-9)</label>
        </div>
        <div class="input-box">
          <input type="text" name="nombre" required />
          <label>Nombre</label>
        </div>
        <div class="input-box">
          <input type="text" name="apellido" required />
          <label>Apellido</label>
        </div>
        <div class="input-box">
          <input type="email" name="email" required />
          <label>Correo electrónico</label>
        </div>
        <div class="input-box">
          <input type="password" name="password" required />
          <label>Contraseña</label>
        </div>
        <input class="boton" name="btnregistrar" type="submit" value="Registrarse" />
        <div class="registrarse">
          <a href="login.php">¿Ya tienes una cuenta? Inicia sesión.</a>
        </div>
      </form>
    </section>
  </main>
</body>
</html>
