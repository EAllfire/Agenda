<?php
require_once("includes/db.php");

$sql = "SELECT * FROM agenda_estado_cita ORDER BY id";
$result = $conn->query($sql);

$estados = [];
while ($row = $result->fetch_assoc()) {
    $estados[] = [
        'id' => $row['id'],
        'nombre' => $row['nombre']
    ];
}

echo json_encode($estados);
?>