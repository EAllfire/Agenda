<?php
require_once 'includes/db.php';

echo '<h2>VERIFICACIÓN DE ESTRUCTURA DE TABLA USUARIOS</h2>';

// Mostrar estructura de la tabla
echo '<h3>1. Estructura de la tabla usuarios:</h3>';
echo '<ul>';
$result = $conn->query('DESCRIBE agenda_usuarios');
while ($row = $result->fetch_assoc()) {
    echo '<li><strong>' . $row['Field'] . '</strong> (' . $row['Type'] . ')';
    if ($row['Null'] === 'NO') echo ' NOT NULL';
    if ($row['Key'] === 'PRI') echo ' PRIMARY KEY';
    if ($row['Default']) echo ' DEFAULT ' . $row['Default'];
    echo '</li>';
}
echo '</ul>';

echo '<h3>2. Usuarios existentes:</h3>';
$users = $conn->query('SELECT id, nombre, correo, nombre_usuario, tipo FROM agenda_usuarios LIMIT 5');
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
} else {
    echo '<p><strong>No hay usuarios en la base de datos</strong></p>';
}

echo '<h3>3. Total de usuarios:</h3>';
$count = $conn->query('SELECT COUNT(*) as total FROM agenda_usuarios');
echo '<p>Total: <strong>' . $count->fetch_assoc()['total'] . '</strong></p>';

// Verificar si existe algún campo de contraseña
echo '<h3>4. Verificación de campos de contraseña:</h3>';
$has_password = false;
$has_password_hash = false;

$result = $conn->query('DESCRIBE usuarios');
while ($row = $result->fetch_assoc()) {
    if ($row['Field'] === 'password') {
        $has_password = true;
    } elseif ($row['Field'] === 'password_hash') {
        $has_password_hash = true;
    }
}

echo '<ul>';
echo '<li>Campo "password": ' . ($has_password ? 'SÍ' : 'NO') . '</li>';
echo '<li>Campo "password_hash": ' . ($has_password_hash ? 'SÍ' : 'NO') . '</li>';
echo '</ul>';

$conn->close();
?>