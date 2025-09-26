<?php
require_once("includes/db.php");

$sql = "SELECT id, CONCAT(nombre, ' ', apellido) as nombre FROM pacientes ORDER BY nombre";
$result = $conn->query($sql);

$pacientes = [];
while ($row = $result->fetch_assoc()) {
    $pacientes[] = [
        'id' => $row['id'],
        'nombre' => $row['nombre']
    ];
}

echo json_encode($pacientes);
?>