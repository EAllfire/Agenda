<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once '../includes/db.php';


$fecha = $_POST['fecha'] ?? '';
$hora_inicio = $_POST['hora_inicio'] ?? '';
$hora_fin = $_POST['hora_fin'] ?? '';
$paciente_id = $_POST['paciente_id'] ?? null;
$profesional_id = $_POST['profesional_id'] ?? null;
$servicio_id = $_POST['servicio_id'] ?? null;
$modalidad_id = $_POST['modalidad_id'] ?? null;
error_log('guardar_cita.php datos recibidos: ' . json_encode($_POST));
$estado = 'reservado'; // Valor fijo y válido para ENUM
$tipo = $_POST['tipo'] ?? '';
$nota_interna = $_POST['nota_interna'] ?? '';
$nota_paciente = $_POST['nota_paciente'] ?? '';

$response = [];
try {
    if ($fecha && $hora_inicio && $hora_fin && $paciente_id && $profesional_id && $servicio_id && $modalidad_id) {
        $stmt = $conn->prepare("INSERT INTO citas (fecha, paciente_id, profesional_id, servicio_id, estado, nota_paciente, nota_interna, hora_inicio, hora_fin, modalidad_id, tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Error en prepare: " . $conn->error);
        }
        $stmt->bind_param(
            "siiiissssis",
            $fecha,
            $paciente_id,
            $profesional_id,
            $servicio_id,
            $estado,
            $nota_paciente,
            $nota_interna,
            $hora_inicio,
            $hora_fin,
            $modalidad_id,
            $tipo
        );
        if ($stmt->execute()) {
            $id = $stmt->insert_id;
            $response = ["success" => true, "id" => $id];
        } else {
            $response = ["success" => false, "error" => $stmt->error];
        }
        $stmt->close();
    } else {
        $response = ["success" => false, "error" => "Faltan datos obligatorios."];
    }
} catch (Exception $e) {
    $response = ["success" => false, "error" => $e->getMessage()];
}

echo json_encode($response);
