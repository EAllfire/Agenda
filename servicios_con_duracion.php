<?php
require_once("includes/db.php");

$modalidad_id = $_GET['modalidad_id'] ?? '';

if (!$modalidad_id || !is_numeric($modalidad_id)) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT id, nombre, duracion_minutos FROM servicios WHERE modalidad_id = ? ORDER BY nombre";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $modalidad_id);
$stmt->execute();
$result = $stmt->get_result();

$servicios = [];
while ($row = $result->fetch_assoc()) {
    $servicios[] = [
        'id' => $row['id'],
        'nombre' => $row['nombre'],
        'duracion_minutos' => $row['duracion_minutos']
    ];
}

echo json_encode($servicios);
?>