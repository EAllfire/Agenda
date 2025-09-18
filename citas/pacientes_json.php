<?php
require_once '../includes/db.php';
header('Content-Type: application/json');
$pacientes = [];
$result = $conn->query("SELECT id, CONCAT(nombre, ' ', apellido) AS nombre_completo FROM pacientes ORDER BY nombre, apellido");
while ($row = $result->fetch_assoc()) {
    $pacientes[] = [
        'id' => $row['id'],
        'nombre' => $row['nombre_completo']
    ];
}
echo json_encode($pacientes);