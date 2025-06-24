<?php include_once("Cargar_Datos.php");?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Película - Web Cine</title>
    <link rel="stylesheet" href="../../../../CSS/styles.css">
    <link rel="stylesheet" href="../../../../CSS/formulario.css">
</head>
<body>
    <header class="header">
    <div class="logo">Web Cine - Gestión de Funciones</div>
        <nav>
            <a href="../../Funciones.php">Lista de Funciones</a>
            <a href="#"></a>
            <a href="#"></a>
        </nav>
    </header>

    <div class="capa"></div>

    <div class="container">
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="mensaje <?= isset($_GET['error']) ? 'error' : 'success' ?>">
                <?= htmlspecialchars(urldecode($_GET['mensaje'])) ?>
            </div>
        <?php endif; ?>

        <div class="formulario-agregar">
            <form id="form-pelicula" action="Guardar_funcion.php" method="POST">
                <table class="form-table">
                    <tr>
                        <td><label for="pelicula">Película:</label></td>
                        <td>
                            <!-- Película -->
                            <select name="id_pelicula" required>
                                <option value="">Seleccione una película</option>
                                <?php foreach ($peliculas as $p): ?>
                                    <option value="<?= $p['idpelicula'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    
                    <!-- Sala y Cine -->
                    <tr>
                        <td><label for="sala">Sala:</label></td>
                        <td>
                            <select id="id_sala" name="id_sala" required class="form-input">
                                <option value="">Seleccione una sala</option>
                                <?php foreach ($salas as $s): ?>
                                    <option value="<?= $s['idsala'] ?>">
                                        <?= htmlspecialchars($s['nombre_sala'] . " - " . $s['nombre_cine']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>

                    <!-- Fecha y hora -->
                    <tr>
                        <td><label for="fecha_hora">Fecha y Hora:</label></td>
                        <td>
                            <input type="datetime-local" id="fecha_hora" name="fecha_hora" min="<?=date('Y-m-d\TH:i')?> required class="form-input">
                        </td>
                    </tr>

                    
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <button type="submit" class="btn-submit">Guardar Funcion</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</body>
</html>