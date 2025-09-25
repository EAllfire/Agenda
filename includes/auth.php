<?php
// Función para verificar si el usuario está logueado
function verificarSesion() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit;
    }
}

// Función para verificar permisos por tipo de usuario
function verificarPermisos($permisos_requeridos) {
    verificarSesion();
    
    $tipo_usuario = $_SESSION['usuario_tipo'] ?? '';
    
    // Admin tiene todos los permisos
    if ($tipo_usuario === 'admin') {
        return true;
    }
    
    // Verificar permisos específicos
    if (is_array($permisos_requeridos)) {
        return in_array($tipo_usuario, $permisos_requeridos);
    } else {
        return $tipo_usuario === $permisos_requeridos;
    }
}

// Función para obtener información del usuario actual
function obtenerUsuarioActual() {
    return [
        'id' => $_SESSION['usuario_id'] ?? null,
        'nombre' => $_SESSION['usuario_nombre'] ?? '',
        'correo' => $_SESSION['usuario_correo'] ?? '',
        'tipo' => $_SESSION['usuario_tipo'] ?? ''
    ];
}

// Función para generar badge de tipo de usuario
function getBadgeTipoUsuario($tipo) {
    $badges = [
        'admin' => '<span class="badge badge-danger">Admin</span>',
        'caja' => '<span class="badge badge-warning">Caja</span>',
        'lectura' => '<span class="badge badge-secondary">Lectura</span>'
    ];
    return $badges[$tipo] ?? '<span class="badge badge-dark">Desconocido</span>';
}

// Función para verificar si el usuario puede realizar una acción específica
function puedeRealizar($accion) {
    $tipo_usuario = $_SESSION['usuario_tipo'] ?? '';
    
    $permisos = [
        'crear_citas' => ['admin', 'caja'],
        'editar_citas' => ['admin', 'caja'],
        'eliminar_citas' => ['admin'],
        'cambiar_estados' => ['admin', 'caja'],
        'ver_citas' => ['admin', 'caja', 'lectura'],
        'gestionar_usuarios' => ['admin'],
        'acceder_reportes' => ['admin', 'caja'],
        'configurar_sistema' => ['admin']
    ];
    
    return in_array($tipo_usuario, $permisos[$accion] ?? []);
}

// Función para cerrar sesión
function cerrarSesion() {
    session_start();
    session_destroy();
    header('Location: login.php');
    exit;
}
?>