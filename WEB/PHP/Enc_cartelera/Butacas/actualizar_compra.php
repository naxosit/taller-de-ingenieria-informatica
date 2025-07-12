<?php
session_start();
include_once("../../../CONNECTION/conexion.php");
include_once("Actualizar_butaca_logica.php");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Actualizar Butaca</title>
    <link rel="stylesheet" href="../../../CSS/styles.css">
    <link rel="stylesheet" href="../../../CSS/botones.css">
    <link rel="stylesheet" href="../../../CSS/Client/Asientos.css">
    <style>
        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        .form-input,
        select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }

        .mensaje-exito,
        .mensaje-error {
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Estilos para la malla de asientos */
        .seating-container {
            margin-top: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .screen {
            width: 80%;
            height: 25px;
            background: linear-gradient(to bottom, #aaa, #666);
            margin: 0 auto 30px;
            border-radius: 4px;
            color: white;
            text-align: center;
            padding: 5px 0;
            font-weight: bold;
            font-size: 14px;
            line-height: 25px;
        }

        .seating-grid {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            margin: 0 auto;
            max-width: 900px;
        }

        .seat-row {
            display: flex;
            gap: 8px;
            justify-content: center;
            width: 100%;
        }

        .seat {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            cursor: pointer;
            font-size: 11px;
            font-weight: bold;
            transition: all 0.2s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .seat.actual {
            background-color: #FF9800;
            color: white;
            cursor: default;
        }

        .seat.ocupada {
            background-color: #F44336;
            color: white;
            cursor: not-allowed;
        }

        .seat.disponible {
            background-color: #4CAF50;
            color: white;
        }

        .seat.disponible:hover {
            transform: scale(1.1);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
        }

        .seat.selected {
            background-color: #2196F3;
            color: white;
            transform: scale(1.1);
            box-shadow: 0 3px 8px rgba(33, 150, 243, 0.4);
        }

        .seating-info {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin: 30px 0 20px;
            gap: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .info-box {
            width: 25px;
            height: 25px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .available-box {
            background-color: #4CAF50;
        }

        .occupied-box {
            background-color: #F44336;
        }

        .actual-box {
            background-color: #FF9800;
        }

        .selected-box {
            background-color: #2196F3;
        }

        .function-info {
            background-color: #eef2ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: bold;
            min-width: 120px;
            color: #555;
        }

        .info-value {
            color: #333;
        }

        #selectionSummary {
            background-color: #e3f2fd;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #2196F3;
        }

        #btnActualizar {
            display: block;
            margin: 0 auto;
            padding: 12px 25px;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <header>
        <div class="logo">Web Cine - Gestión de Compras</div>
        <nav>
            <a href="anulacion_compra.php">Anular Compra</a>
            <a href="../Enc_cartelera.php">Volver</a>
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

            <button type="submit" name="cargar_butacas" class="boton-agregar">Cargar Sala de Asientos</button>
        </form>

        <?php if ($mensaje): ?>
            <div class="<?= strpos($mensaje, 'correctamente') !== false ? 'mensaje-exito' : 'mensaje-error' ?>">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['butacas_data'])):
            $data = $_SESSION['butacas_data'];
            $funcion = $data['funcion_info'];
            unset($_SESSION['butacas_data']);
        ?>
            <div class="seating-container">
                <h2 class="section-title">Actualización de Butaca</h2>

                <div class="function-info">
                    <div class="info-row">
                        <span class="info-label">Película:</span>
                        <span class="info-value"><?= htmlspecialchars($funcion['nombre_pelicula']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Cine:</span>
                        <span class="info-value"><?= htmlspecialchars($funcion['nombre_cine']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Sala:</span>
                        <span class="info-value"><?= htmlspecialchars($funcion['nombre_sala']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Función:</span>
                        <span class="info-value"><?= date('d/m/Y H:i', strtotime($funcion['fechahora'])) ?></span>
                    </div>
                </div>

                <div class="screen">PANTALLA</div>


                <div class="seating-grid" id="seatingGrid">
                    <?php
                    // Agrupar butacas por fila
                    $butacas_por_fila = [];
                    foreach ($data['butacas'] as $butaca) {
                        $fila = $butaca['fila'];
                        if (!isset($butacas_por_fila[$fila])) {
                            $butacas_por_fila[$fila] = [];
                        }
                        $butacas_por_fila[$fila][] = $butaca;
                    }

                    // Mostrar las butacas organizadas en filas
                    foreach ($butacas_por_fila as $fila => $butacas_en_fila):
                        // Dividir cada fila en grupos de 8 butacas
                        $grupos = array_chunk($butacas_en_fila, 8);
                        foreach ($grupos as $grupo):
                    ?>
                            <div class="seat-row">
                                <?php foreach ($grupo as $butaca): ?>
                                    <?php
                                    $clase = 'seat ' . $butaca['estado'];
                                    $label = htmlspecialchars($butaca['fila'] . $butaca['columna']);
                                    $disabled = ($butaca['estado'] === 'ocupada' || $butaca['estado'] === 'actual') ? 'disabled' : '';
                                    ?>
                                    <div class="<?= $clase ?>"
                                        data-id="<?= $butaca['id_butaca'] ?>"
                                        data-label="<?= $label ?>"
                                        <?= $disabled ? 'onclick="return false;"' : '' ?>>
                                        <?= $label ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>

                <div class="seating-info">
                    <div class="info-item">
                        <div class="info-box actual-box"></div>
                        <span>Su butaca actual</span>
                    </div>
                    <div class="info-item">
                        <div class="info-box available-box"></div>
                        <span>Disponible</span>
                    </div>
                    <div class="info-item">
                        <div class="info-box occupied-box"></div>
                        <span>Ocupada</span>
                    </div>
                    <div class="info-item">
                        <div class="info-box selected-box"></div>
                        <span>Seleccionada</span>
                    </div>
                </div>

                <form method="post" id="updateForm">
                    <input type="hidden" name="id_boleto" value="<?= $data['id_boleto'] ?>">
                    <input type="hidden" name="nueva_butaca" id="nuevaButacaInput">

                    <div id="selectionSummary" style="display: none;">
                        <h3>Butaca seleccionada:</h3>
                        <p id="selectedSeatLabel" style="font-size: 18px; font-weight: bold;"></p>
                    </div>

                    <button type="submit" name="actualizar_butaca" class="boton-agregar" id="btnActualizar" disabled>
                        Actualizar Butaca
                    </button>
                </form>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const seatingGrid = document.getElementById('seatingGrid');
            const nuevaButacaInput = document.getElementById('nuevaButacaInput');
            const btnActualizar = document.getElementById('btnActualizar');
            const selectedSeatLabel = document.getElementById('selectedSeatLabel');
            const selectionSummary = document.getElementById('selectionSummary');

            if (seatingGrid) {
                seatingGrid.addEventListener('click', function(e) {
                    const seat = e.target;

                    // Solo procesar si es un asiento disponible
                    if (seat.classList.contains('disponible')) {
                        // Deseleccionar cualquier asiento previo
                        document.querySelectorAll('.seat.selected').forEach(s => {
                            s.classList.remove('selected');
                        });

                        // Seleccionar nuevo asiento
                        seat.classList.add('selected');

                        // Actualizar formulario
                        const seatId = seat.dataset.id;
                        const seatLabel = seat.dataset.label;

                        nuevaButacaInput.value = seatId;
                        selectedSeatLabel.textContent = seatLabel;
                        selectionSummary.style.display = 'block';
                        btnActualizar.disabled = false;
                    }
                });
            }
        });
    </script>
</body>

</html>