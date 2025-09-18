<?php
$servername = "localhost";
$username = "root";
$password = "root"; // en MAMP el default es root
$dbname = "agenda_hospital";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Revisar conexión
if ($conn->connect_error) {
  if (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false) {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "error" => "Conexión fallida: " . $conn->connect_error]);
    exit;
  } else {
    die("Conexión fallida: " . $conn->connect_error);
  }
}
?>
