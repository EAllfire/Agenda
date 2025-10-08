<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

$pacientes = [];
$result = $conn->query("SELECT id, nombre, apellido, telefono FROM agenda_pacientes ORDER BY nombre, apellido");
while ($row = $result->fetch_assoc()) {
    $pacientes[] = [
        'id' => $row['id'],
        'nombre' => $row['nombre'],
        'apellido' => $row['apellido'] ?: '',
        'telefono' => $row['telefono'] ?: '',
        'nombre_completo' => $row['nombre'] . ' ' . ($row['apellido'] ?: '')
    ];
}
echo json_encode($pacientes);
?>