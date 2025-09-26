<?php
// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Función simple para verificar permisos básicos sin bucles de redirección
function puedeVerCitas() {
    $tipo_usuario = $_SESSION['usuario_tipo'] ?? $_SESSION['user_tipo'] ?? '';
    return in_array($tipo_usuario, ['admin', 'caja', 'lectura']);
}

// Solo verificar permiiso básico sin redirección adicional
if (!puedeVerCitas()) {
    header('Location: login.php');
    exit;
}
?>