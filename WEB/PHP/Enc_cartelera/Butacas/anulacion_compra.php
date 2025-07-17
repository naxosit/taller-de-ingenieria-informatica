<?php
include_once("../../../CONNECTION/conexion.php");

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$mensaje = '';
$compras = [];
$rut_busqueda = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Procesar anulación de compras seleccionadas
    if (isset($_POST['boletos_anular'])) {
        $boletos_anular = $_POST['boletos_anular'];
        $rut = $_POST['rut'];

        if (!empty($boletos_anular)) {
            try {
                $conn->beginTransaction();
                $filas_afectadas = 0;

                foreach ($boletos_anular as $id_boleto) {
                    $stmt = $conn->prepare("UPDATE Boleto SET Activo = false WHERE Id_Boleto = :id_boleto");
                    $stmt->bindParam(':id_boleto', $id_boleto, PDO::PARAM_INT);
                    $stmt->execute();
                    $filas_afectadas += $stmt->rowCount();
                }

                $conn->commit();
                $mensaje = "Se anularon $filas_afectadas compras asociadas al RUT $rut";

                // Volver a cargar las compras para mostrar el estado actualizado
                $stmt = $conn->prepare("
                    SELECT 
                        b.Id_Boleto,
                        p.Nombre AS Pelicula,
                        s.Nombre AS Sala,
                        f.FechaHora,
                        bt.Fila,
                        bt.Columna,
                        b.Activo
                    FROM Boleto b
                    JOIN Funcion f ON b.IdFuncion = f.idFuncion
                    JOIN Pelicula p ON f.Id_Pelicula = p.idPelicula
                    JOIN Sala s ON f.Id_Sala = s.idSala
                    JOIN Butaca bt ON b.IdButaca = bt.Id_Butaca
                    WHERE b.RUT = :rut
                ");
                $stmt->bindParam(':rut', $rut);
                $stmt->execute();
                $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $conn->rollBack();
                $mensaje = "Error al anular los boletos: " . $e->getMessage();
            }
        } else {
            $mensaje = "No se seleccionaron compras para anular";
        }
    }
    // Buscar compras por RUT
    else if (isset($_POST['rut'])) {
        $rut_busqueda = $_POST['rut'];

        try {
            // Verificar si el RUT existe
            $stmt = $conn->prepare("SELECT COUNT(*) FROM Perfil WHERE RUT = :rut");
            $stmt->bindParam(':rut', $rut_busqueda);
            $stmt->execute();
            $existe = $stmt->fetchColumn();

            if ($existe > 0) {
                // Obtener todas las compras del RUT
                $stmt = $conn->prepare("
                    SELECT 
                        b.Id_Boleto,
                        p.Nombre AS Pelicula,
                        s.Nombre AS Sala,
                        f.FechaHora,
                        bt.Fila,
                        bt.Columna,
                        b.Activo
                    FROM Boleto b
                    JOIN Funcion f ON b.IdFuncion = f.idFuncion
                    JOIN Pelicula p ON f.Id_Pelicula = p.idPelicula
                    JOIN Sala s ON f.Id_Sala = s.idSala
                    JOIN Butaca bt ON b.IdButaca = bt.Id_Butaca
                    WHERE b.RUT = :rut
                ");
                $stmt->bindParam(':rut', $rut_busqueda);
                $stmt->execute();
                $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (empty($compras)) {
                    $mensaje = "No se encontraron compras para el RUT $rut_busqueda";
                }
            } else {
                $mensaje = "El RUT ingresado no existe en el sistema";
            }
        } catch (PDOException $e) {
            $mensaje = "Error al buscar compras: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anular Compras</title>
    <link rel="stylesheet" href="../../../CSS/styles.css">
    <link rel="stylesheet" href="../../../CSS/botones.css">
    <style>
        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        .form-input {
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

        .compras-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .compras-table th,
        .compras-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .compras-table th {
            background-color: rgb(79, 66, 181);
        }

        .btn-anular {
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }

        .btn-anular:hover {
            background-color: #d32f2f;
        }

        .estado-activo {
            color: #388e3c;
            font-weight: bold;
        }

        .estado-inactivo {
            color: #d32f2f;
            font-weight: bold;
            text-decoration: line-through;
        }

        .no-compras {
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 4px;
            margin-top: 20px;
            text-align: center;
        }

        .acciones {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <header>
        <div class="logo">Web Cine - Gestión de Compras</div>
        <nav>
            <a href="actualizar_compra.php">Actualizar Butacas</a>
            <a href="../Enc_cartelera.php">Volver</a>
        </nav>
    </header>

    <div class="container">
        <h1 class="page-title">Anular Compras por RUT</h1>

        <form method="post">
            <div class="form-group">
                <label for="rut">Ingrese el RUT del cliente:</label>
                <input type="text" id="rut" name="rut" class="form-input"
                    placeholder="Ej: 12.345.678-9" required
                    value="<?= htmlspecialchars($rut_busqueda) ?>">
            </div>
            <button type="submit" class="boton-agregar">Buscar Compras</button>
        </form>

        <?php if (!empty($mensaje)): ?>
            <div class="<?php echo (strpos($mensaje, 'Error') === false && strpos($mensaje, 'no') === false) ? 'mensaje-exito' : 'mensaje-error'; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($compras)): ?>
            <form method="post">
                <input type="hidden" name="rut" value="<?= htmlspecialchars($rut_busqueda) ?>">
                <h2>Compras para el RUT: <?= htmlspecialchars($rut_busqueda) ?></h2>
                <table class="compras-table">
                    <thead>
                        <tr>
                            <th>Seleccionar</th>
                            <th>Película</th>
                            <th>Sala</th>
                            <th>Fecha y Hora</th>
                            <th>Butaca</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($compras as $compra): ?>
                            <tr>
                                <td>
                                    <?php if ($compra['activo'] == 't' || $compra['activo'] === true): ?>
                                        <input type="checkbox" name="boletos_anular[]"
                                            value="<?= $compra['id_boleto'] ?>">
                                    <?php else: ?>
                                        <span>Anulado</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($compra['pelicula']) ?></td>
                                <td><?= htmlspecialchars($compra['sala']) ?></td>
                                <td><?= htmlspecialchars($compra['fechahora']) ?></td>
                                <td><?= htmlspecialchars($compra['fila']) . htmlspecialchars($compra['columna']) ?></td>
                                <td>
                                    <?php if ($compra['activo'] == 't' || $compra['activo'] === true): ?>
                                        <span class="estado-activo">Activa</span>
                                    <?php else: ?>
                                        <span class="estado-inactivo">Anulada</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="acciones">
                    <button type="submit" class="btn-anular">Anular Compras Seleccionadas</button>
                    <button type="button" class="boton-agregar" onclick="seleccionarTodas()">Seleccionar todas activas</button>
                </div>
            </form>
        <?php elseif (isset($rut_busqueda) && empty($compras) && empty($mensaje)): ?>
            <div class="no-compras">
                No se encontraron compras para el RUT ingresado
            </div>
        <?php endif; ?>
    </div>

    <script>
        function seleccionarTodas() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name="boletos_anular[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
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