<?php
require_once 'includes/db.php';

// Verificar que la migración fue exitosa
echo "<h2>Verificación del Sistema de Duración</h2>";

// Verificar la estructura de la tabla
$result = $conn->query("DESCRIBE servicios");
echo "<h3>Estructura de la tabla servicios:</h3>";
echo "<ul>";
while($row = $result->fetch_assoc()) {
    echo "<li><strong>{$row['Field']}</strong>: {$row['Type']} - {$row['Null']} - {$row['Key']} - {$row['Default']}</li>";
}
echo "</ul>";

// Verificar los datos actuales
$result = $conn->query("SELECT id, nombre, duracion_minutos, descripcion FROM portal_servicios LIMIT 5");
echo "<h3>Muestra de servicios con duración:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Nombre</th><th>Duración (min)</th><th>Descripción</th></tr>";
while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['nombre']}</td>";
    echo "<td>{$row['duracion_minutos']} min</td>";
    echo "<td>" . substr($row['descripcion'], 0, 50) . "...</td>";
    echo "</tr>";
}
echo "</table>";

$conn->close();
?>