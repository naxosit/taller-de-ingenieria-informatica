<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require __DIR__ . '/../CONNECTION/conexion.php';

// Procesar cambio de rol mediante el rut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_rol'])) {
    $rut = $_POST['rut'];
    $nuevo_rol = $_POST['nuevo_rol'];
    
    try {
        $stmt = $db->prepare("UPDATE perfil SET rol = ? WHERE rut = ?");
        $stmt->execute([$nuevo_rol, $rut]);
        $mensaje = "Rol actualizado para ".htmlspecialchars($rut);
    } catch(PDOException $e) {
        $error = "Error: ".$e->getMessage();
    }
}

// Obtenemos los datos del perfil y los roles.
try {
    // Totales
    $total_usuarios = $db->query("SELECT COUNT(*) FROM perfil")->fetchColumn();
    
    // Por roles
    $roles = ['admin', 'encargado_sala', 'encargado_butaca', 'cliente'];
    $usuarios_por_rol = [];
    foreach ($roles as $rol) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM perfil WHERE rol = ?");
        $stmt->execute([$rol]);
        $usuarios_por_rol[$rol] = $stmt->fetchColumn();
    }
    
    // Últimos registros de la tabla perfil
    $ultimos_usuarios = $db->query("SELECT rut, nombre, apellido, rol FROM perfil ORDER BY fecha_registro DESC LIMIT 5")->fetchAll();
    
} catch(PDOException $e) {
    $error = "Error en la base de datos: ".$e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Admin - Web Cine</title>
  <link rel="stylesheet" href="../CSS/styles.css" />
</head>
<body>

  <!-- Encabezado simple -->
  <div>
    <div>Web Cine</div>
    <nav>
      <a href="vista_admin.php">Inicio</a>
      <a href="logout.php">Cerrar sesión</a>
    </nav>
  </div>

  <!-- Contenido principal -->
  <div>
    <h1>Panel de Administración</h1>
    <p>Usuario: <?= htmlspecialchars($_SESSION['rut']) ?></p> 
    <!-- htmlspecialchars: Funcion de seguridad de PHP convierte caracteres especiales
        en entidades HTML. Se usa cuando extraemos datos de la base de datos. -->
    
    <?php if (isset($mensaje)): ?>
      <div style="background: lightgreen; padding: 10px;"><?= $mensaje ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
      <div style="background: pink; padding: 10px;"><?= $error ?></div>
    <?php endif; ?>

    <!-- Tabla estadisticas de los registros y roles de los usuarios -->
    <h2>Estadísticas</h2>
    <table border="1">
      <tr>
        <th>Total Usuarios</th>
        <th>Administradores</th>
        <th>Enc. Sala</th>
      </tr>
      <tr>
        <td><?= $total_usuarios ?></td>
        <td><?= $usuarios_por_rol['admin'] ?></td>
        <td><?= $usuarios_por_rol['encargado_sala'] ?></td>
      </tr>
      <tr>
        <th>Enc. Butaca</th>
        <th>Clientes</th>
        <th>Total Activos</th>
      </tr>
      <tr>
        <td><?= $usuarios_por_rol['encargado_butaca'] ?></td>
        <td><?= $usuarios_por_rol['cliente'] ?></td>
        <td><?= array_sum($usuarios_por_rol) ?></td>
      </tr>
    </table>

    <!-- Últimos usuarios registrados en la Base de datos -->
    <h2>Últimos Registros</h2>
    <table border="1">
      <tr>
        <th>RUT</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Rol</th>
        <th>Acción</th>
      </tr>
      <?php foreach ($ultimos_usuarios as $usuario): ?>
      <tr>
        <td><?= htmlspecialchars($usuario['rut']) ?></td>
        <td><?= htmlspecialchars($usuario['nombre']) ?></td>
        <td><?= htmlspecialchars($usuario['apellido']) ?></td>
        <td><?= htmlspecialchars($usuario['rol']) ?></td>
        <td>
          <form method="post">
            <input type="hidden" name="rut" value="<?= $usuario['rut'] ?>">
            <select name="nuevo_rol">
              <?php foreach ($roles as $rol): ?>
              <option value="<?= $rol ?>" <?= $usuario['rol'] === $rol ? 'selected' : '' ?>>
                <?= ucfirst(str_replace('_', ' ', $rol)) ?>
              </option>
              <?php endforeach; ?>
            </select>
            <button type="submit" name="cambiar_rol">Actualizar</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
</body>
</html>