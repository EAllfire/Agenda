<?php
// Generar hash para la contraseña admin123
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Hash generado para 'admin123': " . $hash . "\n";
echo "\nSQL para insertar usuario:\n";
echo "INSERT INTO agenda_usuarios (nombre, correo, password, tipo) VALUES \n";
echo "('Administrador', 'admin@hospital.com', '" . $hash . "', 'admin');\n";
?>