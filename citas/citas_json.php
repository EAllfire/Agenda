<?php
require_once("../includes/db.php");


$sql = "SELECT c.id, c.fecha, c.hora_inicio, c.hora_fin, c.estado,
         p.nombre AS paciente, s.nombre AS servicio, s.id AS servicio_id
  FROM citas c
  JOIN pacientes p ON c.paciente_id = p.id
  JOIN servicios s ON c.servicio_id = s.id";

$result = $conn->query($sql);

$eventos = [];


while ($row = $result->fetch_assoc()) {
  // Validar formato de hora_inicio y hora_fin
  $hora_inicio = $row['hora_inicio'] ?? '';
  $hora_fin = $row['hora_fin'] ?? '';
  // Si no hay hora_fin, usar hora_inicio + 1 hora (opcional)
  if (!$hora_fin && $hora_inicio) {
    $hora = strtotime($hora_inicio);
    $hora_fin = date('H:i:s', $hora + 3600);
  }
    // Asignar color según el estado
    $color = null;
    if (isset($row['estado'])) {
      if ($row['estado'] === 'reservado') {
        $color = 'blue';
      } elseif ($row['estado'] === 'pendiente') {
        $color = 'orange';
      } elseif ($row['estado'] === 'confirmado') {
        $color = 'green';
      } else {
        $color = null;
      }
    }
    $eventos[] = [
      'id' => $row['id'],
      'title' => $row['paciente']." (".$row['servicio'].")",
      'start' => $row['fecha']."T".$hora_inicio,
      'end' => $row['fecha']."T".$hora_fin,
      'resourceId' => $row['servicio_id'],
      'color' => $color
    ];
}

echo json_encode($eventos);
?>
