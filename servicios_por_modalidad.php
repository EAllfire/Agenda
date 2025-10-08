<?php
require_once("includes/db.php");
header('Content-Type: application/json');

$modalidad_id = isset($_GET['modalidad_id']) ? intval($_GET['modalidad_id']) : 0;

// Consulta para obtener servicios basado en modalidad_id -> modalidad (remoto)
$sql = "SELECT id, nombre, duracion AS duracion_minutos FROM portal_servicios WHERE modalidad = ?";
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