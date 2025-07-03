<?php
// Iniciar sesión para poder guardar mensajes de estado.
session_start();

// Incluye el archivo de conexión a la base de datos.
include_once("../.././../CONNECTION/conexion.php");

// --- LÓGICA DEL BACKEND (SE EJECUTA SI SE ENVÍA UN FORMULARIO) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Se obtiene el ID del cine de forma segura (usando 'idcine' en minúsculas).
    $id_cine = isset($_POST['idcine']) ? intval($_POST['idcine']) : 0;
    
    // Se identifica la acción: guardar (actualizar/insertar) o eliminar.
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    try {
        // --- INICIO LÓGICA DE ELIMINACIÓN ---
        if ($action === 'delete' && $id_cine > 0) {
            // MODIFICACIÓN: Nombres de tabla y columna en minúsculas.
            $sql_delete = "DELETE FROM direccionmaps WHERE idcine = :id_cine";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bindParam(':id_cine', $id_cine, PDO::PARAM_INT);
            $stmt_delete->execute();
            
            if ($stmt_delete->rowCount() > 0) {
                $_SESSION['status_message'] = "URL eliminada correctamente para el cine ID: $id_cine.";
            } else {
                $_SESSION['status_message'] = "La URL ya no existía o el cine es inválido.";
            }
        
        // --- INICIO LÓGICA DE ACTUALIZAR/INSERTAR ---
        } elseif ($action === 'save' && $id_cine > 0) {
            $nueva_url = isset($_POST['nueva_url']) ? trim($_POST['nueva_url']) : '';

            if (!empty($nueva_url) && filter_var($nueva_url, FILTER_VALIDATE_URL)) {
                // MODIFICACIÓN: Nombres en minúsculas.
                $sql_check = "SELECT idcine FROM direccionmaps WHERE idcine = :id_cine";
                $stmt_check = $conn->prepare($sql_check);
                $stmt_check->bindParam(':id_cine', $id_cine, PDO::PARAM_INT);
                $stmt_check->execute();
                
                if ($stmt_check->fetch()) {
                    // Si existe, ACTUALIZAR (UPDATE). Nombres en minúsculas.
                    $sql_update = "UPDATE direccionmaps SET url = :nueva_url WHERE idcine = :id_cine";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bindParam(':nueva_url', $nueva_url, PDO::PARAM_STR);
                    $stmt_update->bindParam(':id_cine', $id_cine, PDO::PARAM_INT);
                    $stmt_update->execute();
                    $_SESSION['status_message'] = "URL actualizada correctamente para el cine ID: $ .";
                } else {
                    // Si no existe, INSERTAR una nueva fila.
                    // Primero, necesitamos el idciudad del cine. Nombres en minúsculas.
                    $sql_ciudad = "SELECT idciudad FROM cine WHERE idcine = :id_cine";
                    $stmt_ciudad = $conn->prepare($sql_ciudad);
                    $stmt_ciudad->bindParam(':id_cine', $id_cine, PDO::PARAM_INT);
                    $stmt_ciudad->execute();
                    $cine_data = $stmt_ciudad->fetch(PDO::FETCH_ASSOC);

                    // MODIFICACIÓN: Usar 'idciudad' (minúsculas) para la comprobación y asignación.
                    if ($cine_data && isset($cine_data['idciudad']) && $cine_data['idciudad'] !== null) {
                        $id_ciudad = $cine_data['idciudad'];
                        
                        // Ahora sí, hacemos el INSERT. Nombres en minúsculas.
                        $sql_insert = "INSERT INTO direccionmaps (url, idcine, idciudad) VALUES (:nueva_url, :id_cine, :id_ciudad)";
                        $stmt_insert = $conn->prepare($sql_insert);
                        $stmt_insert->bindParam(':nueva_url', $nueva_url, PDO::PARAM_STR);
                        $stmt_insert->bindParam(':id_cine', $id_cine, PDO::PARAM_INT);
                        $stmt_insert->bindParam(':id_ciudad', $id_ciudad, PDO::PARAM_INT);
                        $stmt_insert->execute();
                        $_SESSION['status_message'] = "URL registrada correctamente para el cine ID: $id_cine.";
                    } else {
                        $_SESSION['status_message'] = "Error: No se pudo encontrar la ciudad para el cine ID: $id_cine. Asegúrese de que el cine tenga una ciudad asignada.";
                    }
                }
            } else {
                $_SESSION['status_message'] = "Error: La URL proporcionada no es válida.";
            }
        }
    } catch (PDOException $e) {
        $_SESSION['status_message'] = "Error en la base de datos: " . $e->getMessage();
    }

    // Redirigir a la misma página para evitar reenvío del formulario.
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// --- LÓGICA DE VISUALIZACIÓN (SE EJECUTA SIEMPRE) ---
try {
    // MODIFICACIÓN: Nombres de tablas y columnas en minúsculas para compatibilidad con PSQL.
    $sql_cines = "SELECT c.idcine, c.nombre_cine, c.idciudad, dm.url 
                  FROM cine AS c
                  LEFT JOIN direccionmaps AS dm ON c.idcine = dm.idcine
                  ORDER BY c.nombre_cine ASC";
    $stmt_cines = $conn->prepare($sql_cines);
    $stmt_cines->execute();
    $lista_cines = $stmt_cines->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al consultar la lista de cines: " . $e->getMessage());
}

// Recuperar y limpiar el mensaje de estado de la sesión.
$status_message = '';
if (isset($_SESSION['status_message'])) {
    $status_message = $_SESSION['status_message'];
    unset($_SESSION['status_message']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar URLs de Cines</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 900px; margin: 20px auto; background-color: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 6px 20px rgba(0,0,0,0.08); }
        h1 { text-align: center; color: #343a40; border-bottom: 2px solid #e9ecef; padding-bottom: 15px; margin-bottom: 30px; }
        .cine-item { border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin-bottom: 20px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; transition: box-shadow 0.3s ease; }
        .cine-item:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .cine-info { flex: 1 1 300px; margin-right: 20px; }
        .cine-info h2 { margin: 0 0 10px; font-size: 1.4em; color: #495057; }
        .cine-info p { margin: 0; color: #6c757d; font-size: 0.9em; display: flex; align-items: center; flex-wrap: wrap; }
        .cine-info .url { color: #007bff; word-break: break-all; margin-right: 15px; }
        .cine-forms-container { flex: 1 1 400px; display: flex; flex-direction: column; gap: 10px; }
        .form-group { display: flex; }
        input[type="url"] { flex-grow: 1; padding: 10px; border: 1px solid #ced4da; border-radius: 5px 0 0 5px; transition: border-color 0.2s; }
        input[type="url"]:focus { outline: none; border-color: #80bdff; }
        .btn { padding: 10px 15px; border: none; color: white; cursor: pointer; transition: background-color 0.2s; font-weight: bold; }
        .btn-save { background-color: #28a745; border-radius: 0 5px 5px 0; }
        .btn-save:hover { background-color: #218838; }
        .btn-delete { background-color: #dc3545; border-radius: 5px; font-size: 0.8em; padding: 5px 10px;}
        .btn-delete:hover { background-color: #c82333; }
        .status-message { padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; font-weight: bold; }
        .status-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .back-button-container { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e9ecef; }
        .back-button { display: inline-block; padding: 12px 25px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background-color 0.3s ease; }
        .back-button:hover { background-color: #5a6268; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestionar URLs de Google Maps</h1>

        <?php if ($status_message): ?>
            <div class="status-message <?php echo strpos(strtolower($status_message), 'error') !== false ? 'status-error' : 'status-success'; ?>">
                <?php echo htmlspecialchars($status_message); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($lista_cines)): ?>
            <p>No hay cines registrados en la base de datos.</p>
        <?php else: ?>
            <?php foreach ($lista_cines as $cine): ?>
                <div class="cine-item">
                    <div class="cine-info">
                        <h2><?php echo htmlspecialchars($cine['nombre_cine']); ?></h2>
                        <p>
                            <span>URL Actual:</span>&nbsp;
                            <?php if (!empty($cine['url'])): ?>
                                <a href="<?php echo htmlspecialchars($cine['url']); ?>" target="_blank" class="url">Ver URL actual</a>
                            <?php else: ?>
                                <span>No registrada</span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="cine-forms-container">
                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="form-group">
                            <input type="hidden" name="idcine" value="<?php echo $cine['idcine']; ?>">
                            <input type="hidden" name="action" value="save">
                            <input type="url" name="nueva_url" placeholder="Pegar nueva URL de Google Maps" required>
                            <input type="submit" value="Guardar" class="btn btn-save">
                        </form>
                        
                        <?php if (!empty($cine['url'])): ?>
                            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta URL?');">
                                <input type="hidden" name="idcine" value="<?php echo $cine['idcine']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="submit" value="Eliminar URL" class="btn btn-delete">
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="back-button-container">
            <a href="../vista_admin.php" class="back-button">Volver al Panel de Administrador</a>
        </div>
    </div>
</body>
</html>