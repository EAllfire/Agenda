<?php
require_once("../includes/db.php");

$sql = "SELECT id, nombre FROM modalidades";
$result = $conn->query($sql);

$recursos = [];

while ($row = $result->fetch_assoc()) {
    $recursos[] = [
        'id' => $row['id'],
        'title' => $row['nombre']
        // Puedes agregar 'image' => 'url' si quieres mostrar imágenes
    ];
}

echo json_encode($recursos);
?>