<?php
include_once("../../CONNECTION/conexion.php");
include_once("Actualizar_butaca_logica.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Actualizar Butaca</title>
    <link rel="stylesheet" href="../../CSS/styles.css">
    <link rel="stylesheet" href="../../CSS/botones.css">
    <style>
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
        }
        .form-input, select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }
        .mensaje-exito, .mensaje-error {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
            font-weight: bold;
        }
        .mensaje-exito {
            background-color: #e8f5e9;
            color: #1b5e20;
            border-left: 5px solid #2e7d32;
        }
        .mensaje-error {
            background-color: #ffebee;
            color: #b71c1c;
            border-left: 5px solid #c62828;
        }
        .butacas-info {
            margin-top: 15px;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 4px;
        }
        .page-title {
            text-align: center;
            margin-bottom: 25px;
            color: #6b51e1;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            padding: 15px 0;
            color: #6b51e1;
            text-align: center;
        }
        nav {
            text-align: center;
            margin-bottom: 20px;
        }
        nav a {
            margin: 0 15px;
            text-decoration: none;
            color: #6b51e1;
            font-weight: bold;
        }
        nav a:hover {
            text-decoration: underline;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        select.form-select {
            background-color: #fff;
            border: 2px solid #ccc;
            border-radius: 6px;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 140 140' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill='gray' d='M70 95L30 55h80z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px 16px;
            cursor: pointer;
            transition: border-color 0.3s ease;
        }

        select.form-select:hover {
            border-color: #888;
        }

        select.form-select:focus {
            outline: none;
            border-color: #6b51e1;
        }

        input[type="number"].form-input {
            background-color: #fff;
            border: 2px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            padding: 10px;
            transition: border-color 0.3s ease;
        }

        input[type="number"].form-input:focus {
            border-color: #1976D2;
            outline: none;
        }

    </style>
</head>
<body>
<header>
    <div class="logo">Web Cine - Gestión de Compras</div>
    <nav>
        <a href="Cartelera.php">Cartelera</a>
        <a href="vista_encargado.php">Películas</a>
        <a href="vista_salas.php">Salas</a>
    </nav>
</header>

<div class="container">
    <h1 class="page-title">Actualizar Butaca de Boleto</h1>

    <form method="post">
        <div class="form-group">
            <label for="id_boleto">ID del Boleto:</label>
            <input type="number" id="id_boleto" name="id_boleto" class="form-input" required
                value="<?= isset($_POST['id_boleto']) ? htmlspecialchars($_POST['id_boleto']) : '' ?>">
        </div>

        <button type="submit" name="cargar_butacas" class="boton-agregar">Cargar Butacas Disponibles</button>

        <?php if (!empty($butacas_disponibles)): ?>
            <div class="form-group" style="margin-top: 20px;">
                <label for="nueva_butaca">Nueva Butaca Disponible:</label>
                <select id="nueva_butaca" name="nueva_butaca" class="form-select" required>
                    <?php foreach ($butacas_disponibles as $butaca): ?>
                        <option value="<?= $butaca['id_butaca'] ?>">
                            <?= htmlspecialchars($butaca['fila'] . $butaca['columna']) ?> (ID: <?= $butaca['id_butaca'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" name="actualizar_butaca" class="boton-agregar">Actualizar Butaca</button>
        <?php endif; ?>
    </form>

    <?php if ($mensaje): ?>
        <div class="<?= strpos($mensaje, 'correctamente') !== false ? 'mensaje-exito' : 'mensaje-error' ?>">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($detalles_butacas)): ?>
        <div class="butacas-info">
            <h3>Detalles de las Butacas:</h3>
            <ul>
                <?php foreach ($detalles_butacas as $butaca):
                    $fila = $butaca['fila'] ?? $butaca['Fila'] ?? '';
                    $columna = $butaca['columna'] ?? $butaca['Columna'] ?? '';
                    ?>
                    <li>Butaca <?= htmlspecialchars($fila . $columna) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
