<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../includes/db.php");
header('Content-Type: application/json');

$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$correo = $_POST['correo'] ?? '';
$diagnostico = $_POST['diagnostico'] ?? '';
$tipo = $_POST['tipo'] ?? '';
$origen = $_POST['origen'] ?? '';
$comentarios = $_POST['comentarios'] ?? '';

error_log('guardar_paciente.php datos recibidos: ' . json_encode($_POST));

if ($nombre && $apellido) {
    $stmt = $conn->prepare("INSERT INTO pacientes (nombre, apellido, telefono, correo, diagnostico, tipo, origen, comentarios) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["success" => false, "error" => $conn->error]);
        exit;
    }
    $stmt->bind_param("ssssssss", $nombre, $apellido, $telefono, $correo, $diagnostico, $tipo, $origen, $comentarios);
    if ($stmt->execute()) {
        $paciente_id = $conn->insert_id;
        echo json_encode(["success" => true, "id" => $paciente_id, "nombre" => $nombre, "apellido" => $apellido]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "Nombre y apellido requeridos"]);
}
?>