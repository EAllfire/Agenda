<?php

require_once("../../includes/db.php");
header('Content-Type: application/json');

// Cambia el nombre de la tabla y campos según tu BD de modalidades
$sql = "SELECT id, nombre AS title FROM modalidades";
$result = $conn->query($sql);

$resources = [];
while ($row = $result->fetch_assoc()) {
  $resources[] = [
    'id' => $row['id'],
    'title' => $row['title']
  ];
}

echo json_encode($resources);
?>