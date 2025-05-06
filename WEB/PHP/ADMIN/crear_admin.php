<?php
require_once '../../CONNECTION/conexion.php';

$RUT = '21445918-2';
$Nombre = 'Admin';
$Apellido = 'General';
$Correo_electronico = 'admin@cine.com';
$Rol = 'admin';
$Contraseña = 'admin123';

try {
    // Verificar si el admin ya existe
    $stmt = $conn->prepare("SELECT 1 FROM Perfil WHERE RUT = :rut");
    $stmt->bindParam(':rut', $RUT);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        $conn->beginTransaction();

        // Insertar la contraseña y obtener el ID
        $stmt = $conn->prepare("INSERT INTO Contraseña (ContraseñaUsuario) VALUES (:pass) RETURNING Id_Contraseña");
        $stmt->execute([':pass' => $Contraseña]);
        $id_contraseña = $stmt->fetchColumn();

        // Insertar el perfil con el ID de la contraseña
        $stmt = $conn->prepare("INSERT INTO Perfil (RUT, Nombre, Apellido, Correo_electronico, Rol, Id_Contraseña) 
                                VALUES (:rut, :nombre, :apellido, :correo, :rol, :id_contra)");
        $stmt->execute([
            ':rut' => $RUT,
            ':nombre' => $Nombre,
            ':apellido' => $Apellido,
            ':correo' => $Correo_electronico,
            ':rol' => $Rol,
            ':id_contra' => $id_contraseña
        ]);

        $conn->commit();
        echo "Admin creado con éxito.";
    } else {
        echo "El admin ya existe.";
    }
} catch (PDOException $e) {
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
