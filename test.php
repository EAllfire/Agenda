<?php
// Archivo de prueba para diagnosticar el error
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Diagnóstico del Sistema</h2>";

// 1. Verificar PHP
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

// 2. Verificar conexión a base de datos
echo "<p><strong>Probando conexión a base de datos...</strong></p>";

try {
    $ruta_db = "../../includes/db.php";
    echo "<p>Intentando cargar: $ruta_db</p>";
    
    if (file_exists($ruta_db)) {
        echo "<p>✅ Archivo db.php existe</p>";
        require_once($ruta_db);
        echo "<p>✅ Archivo db.php cargado correctamente</p>";
        
        if (isset($conn)) {
            echo "<p>✅ Variable \$conn existe</p>";
            
            // Probar consulta simple
            $result = $conn->query("SELECT COUNT(*) as total FROM modalidades");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "<p>✅ Conexión a base de datos funciona - Total modalidades: " . $row['total'] . "</p>";
            } else {
                echo "<p>❌ Error en consulta: " . $conn->error . "</p>";
            }
        } else {
            echo "<p>❌ Variable \$conn no existe</p>";
        }
    } else {
        echo "<p>❌ Archivo db.php NO existe en la ruta: " . realpath($ruta_db) . "</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

// 3. Verificar permisos de archivos
echo "<p><strong>Verificando permisos...</strong></p>";
$archivos = ['index.php', 'api/modalidades.php', 'api/servicios.php'];
foreach ($archivos as $archivo) {
    if (file_exists($archivo)) {
        $permisos = substr(sprintf('%o', fileperms($archivo)), -4);
        echo "<p>$archivo: $permisos</p>";
    } else {
        echo "<p>❌ $archivo no existe</p>";
    }
}

echo "<p><strong>Diagnóstico completado</strong></p>";
?>