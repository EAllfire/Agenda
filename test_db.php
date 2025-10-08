<?php
// Script de prueba para verificar la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "agenda_hospital";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    
    echo "Conexión exitosa a la base de datos!" . PHP_EOL;
    
    // Probar consulta de servicios
    $sql = "SELECT s.id, s.nombre, s.precio, m.nombre as modalidad_nombre 
            FROM portal_servicios s 
            LEFT JOIN agenda_modalidades m ON s.modalidad_id = m.id 
            LIMIT 3";
    
    $result = $conn->query($sql);
    
    if ($result) {
        echo "Consulta exitosa! Encontrados " . $result->num_rows . " resultados:" . PHP_EOL;
        while ($row = $result->fetch_assoc()) {
            echo "- ID: {$row['id']}, Nombre: {$row['nombre']}, Precio: {$row['precio']}, Modalidad: {$row['modalidad_nombre']}" . PHP_EOL;
        }
    } else {
        echo "Error en la consulta: " . $conn->error . PHP_EOL;
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
?>