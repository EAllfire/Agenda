<?php
require_once 'includes/db.php';

echo '<h2>CREAR USUARIO DE PRUEBA</h2>';

// Datos del usuario de prueba
$nombre = 'Administrador Test';
$correo = 'admin@test.com';
$nombre_usuario = 'admin';
$password = 'admin123';
$tipo = 'admin';

// Verificar si ya existe
$check = $conn->query("SELECT id FROM usuarios WHERE correo = '$correo' OR nombre_usuario = '$nombre_usuario'");

if ($check->num_rows > 0) {
    echo '<div style="color: orange; padding: 10px; border: 1px solid orange; background: #fff3cd;">';
    echo '<strong>Aviso:</strong> Ya existe un usuario con ese correo o nombre de usuario.';
    echo '</div>';
    
    echo '<h3>Usuarios existentes:</h3>';
    $users = $conn->query('SELECT id, nombre, correo, nombre_usuario, tipo FROM usuarios');
    if ($users->num_rows > 0) {
        echo '<table border="1" style="border-collapse: collapse;">';
        echo '<tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Usuario</th><th>Tipo</th></tr>';
        while ($user = $users->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $user['id'] . '</td>';
            echo '<td>' . $user['nombre'] . '</td>';
            echo '<td>' . $user['correo'] . '</td>';
            echo '<td>' . ($user['nombre_usuario'] ?? 'NULL') . '</td>';
            echo '<td>' . $user['tipo'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
} else {
    // Verificar qué campo de contraseña usar
    $check_fields = $conn->query("DESCRIBE usuarios");
    $password_field = 'password';
    
    while ($field = $check_fields->fetch_assoc()) {
        if ($field['Field'] === 'password_hash') {
            $password_field = 'password_hash';
            break;
        }
    }
    
    // Hash de la contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Verificar si existe campo nombre_usuario
    $has_username_field = false;
    $check_fields = $conn->query("DESCRIBE usuarios");
    while ($field = $check_fields->fetch_assoc()) {
        if ($field['Field'] === 'nombre_usuario') {
            $has_username_field = true;
            break;
        }
    }
    
    if ($has_username_field) {
        // Insertar con nombre_usuario
        $sql = "INSERT INTO usuarios (nombre, correo, nombre_usuario, $password_field, tipo) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nombre, $correo, $nombre_usuario, $password_hash, $tipo);
    } else {
        // Insertar sin nombre_usuario (campo no existe aún)
        $sql = "INSERT INTO usuarios (nombre, correo, $password_field, tipo) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $correo, $password_hash, $tipo);
    }
    
    if ($stmt->execute()) {
        echo '<div style="color: green; padding: 10px; border: 1px solid green; background: #d4edda;">';
        echo '<strong>Éxito:</strong> Usuario creado correctamente.<br>';
        echo '<strong>Usuario:</strong> ' . $nombre_usuario . '<br>';
        echo '<strong>Contraseña:</strong> ' . $password . '<br>';
        echo '<strong>Correo:</strong> ' . $correo . '<br>';
        echo '<strong>Tipo:</strong> ' . $tipo;
        echo '</div>';
        
        if (!$has_username_field) {
            echo '<div style="color: orange; padding: 10px; border: 1px solid orange; background: #fff3cd; margin-top: 10px;">';
            echo '<strong>Nota:</strong> El campo nombre_usuario no existe en la tabla. Para usar login por username, ejecuta el script actualizar_usuarios.php';
            echo '</div>';
        }
    } else {
        echo '<div style="color: red; padding: 10px; border: 1px solid red; background: #f8d7da;">';
        echo '<strong>Error:</strong> ' . $conn->error;
        echo '</div>';
    }
    
    $stmt->close();
}

echo '<br><br>';
echo '<a href="login.php" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Ir a Login</a> ';
echo '<a href="actualizar_usuarios.php" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Actualizar Schema</a> ';
echo '<a href="verificar_usuarios.php" style="background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Verificar Usuarios</a>';

$conn->close();
?>