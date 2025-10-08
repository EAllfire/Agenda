<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Solo admins pueden acceder a este panel
if (!puedeRealizar('gestionar_usuarios')) {
    header('Location: index.php');
    exit;
}

// Obtener información del usuario actual para el header
$user_nombre = $_SESSION['usuario_nombre'] ?? 'Usuario';
$user_tipo = $_SESSION['usuario_tipo'] ?? 'usuario';

$error = '';
$success = '';

// Procesar eliminación de usuario
if ($_POST && isset($_POST['eliminar_usuario'])) {
    $usuario_id = intval($_POST['usuario_id']);
    
    if ($usuario_id && $usuario_id != $_SESSION['usuario_id']) {
        $stmt = $conn->prepare("DELETE FROM agenda_usuarios WHERE id = ?");
        $stmt->bind_param("i", $usuario_id);
        
        if ($stmt->execute()) {
            $success = 'Usuario eliminado exitosamente.';
        } else {
            $error = 'Error al eliminar el usuario.';
        }
        $stmt->close();
    } else {
        $error = 'No puedes eliminar tu propio usuario.';
    }
}

// Procesar edición de usuario
if ($_POST && isset($_POST['editar_usuario'])) {
    $usuario_id = intval($_POST['usuario_id']);
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $tipo = $_POST['tipo'] ?? '';
    $cambiar_password = !empty($_POST['password']);
    
    if ($usuario_id && $nombre && $correo && $tipo) {
        // Verificar si el correo ya existe en otro usuario
        $stmt = $conn->prepare("SELECT id FROM agenda_usuarios WHERE correo = ? AND id != ?");
        $stmt->bind_param("si", $correo, $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'El correo electrónico ya está registrado por otro usuario';
        } else {
            if ($cambiar_password) {
                $password = $_POST['password'];
                if (strlen($password) < 6) {
                    $error = 'La contraseña debe tener al menos 6 caracteres';
                } else {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE agenda_usuarios SET nombre = ?, correo = ?, tipo = ?, password = ? WHERE id = ?");
                    $stmt->bind_param("ssssi", $nombre, $correo, $tipo, $password_hash, $usuario_id);
                }
            } else {
                $stmt = $conn->prepare("UPDATE agenda_usuarios SET nombre = ?, correo = ?, tipo = ? WHERE id = ?");
                $stmt->bind_param("sssi", $nombre, $correo, $tipo, $usuario_id);
            }
            
            if (!isset($error) && $stmt->execute()) {
                $success = 'Usuario actualizado exitosamente.';
            } elseif (!isset($error)) {
                $error = 'Error al actualizar el usuario.';
            }
        }
        $stmt->close();
    } else {
        $error = 'Por favor complete todos los campos obligatorios.';
    }
}

// Procesar creación de usuario admin
if ($_POST && isset($_POST['crear_admin'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if ($nombre && $correo && $password && $confirm_password) {
        if ($password !== $confirm_password) {
            $error = 'Las contraseñas no coinciden';
        } elseif (strlen($password) < 6) {
            $error = 'La contraseña debe tener al menos 6 caracteres';
        } else {
            // Verificar si el correo ya existe
            $stmt = $conn->prepare("SELECT id FROM agenda_usuarios WHERE correo = ?");
            $stmt->bind_param("s", $correo);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = 'El correo electrónico ya está registrado';
            } else {
                // Crear usuario admin
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO agenda_usuarios (nombre, correo, password, tipo) VALUES (?, ?, ?, 'admin')");
                $stmt->bind_param("sss", $nombre, $correo, $password_hash);
                
                if ($stmt->execute()) {
                    $success = 'Usuario administrador creado exitosamente.';
                } else {
                    $error = 'Error al crear el usuario administrador';
                }
            }
            $stmt->close();
        }
    } else {
        $error = 'Por favor complete todos los campos';
    }
}

// Obtener todos los usuarios para mostrar
$usuarios = [];
$result = $conn->query("SELECT id, nombre, correo, tipo FROM agenda_usuarios ORDER BY tipo DESC, nombre ASC");
while ($row = $result->fetch_assoc()) {
    $usuarios[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: #f8f9fa; 
            font-family: Arial, sans-serif;
            margin: 0;
            padding-top: 100px;
        }
        
        /* Header Styles - Same as index.php */
        .main-header {
            background: #1275a0;
            color: white;
            height: 80px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            font-family: Arial, sans-serif;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .header-right {
            display: flex;
            align-items: center;
        }
        
        .logo-section {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            flex-direction: column;
            text-align: center;
        }
        
        .header-logo img {
            max-height: 60px;
            margin-left: 10px;
            width: auto;
            filter: drop-shadow(1px 1px 2px rgba(0,0,0,0.1)) brightness(1.1);
        }
        
        .logo-text {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            color: white;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
            letter-spacing: 0.5px;
            text-align: center;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
            color: white;
            font-size: 14px;
            background: rgba(255,255,255,0.1);
            padding: 8px 12px;
            border-radius: 6px;
        }
        
        .user-type {
            font-size: 12px;
            opacity: 0.8;
        }
        
        .btn-header {
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
            background: none;
            border: none;
            padding: 0.5rem 1rem;
            font-size: 13px;
            cursor: pointer;
        }
        
        .btn-header:hover {
            text-decoration: underline;
            color: #cce7ff;
        }
        
        .admin-container { max-width: 1000px; margin: 20px auto; padding: 20px; }
        .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .badge-admin { background: #dc3545; }
        .badge-caja { background: #fd7e14; }
        .badge-lectura { background: #6c757d; }
        .back-btn { margin-bottom: 20px; }
        .btn-sm { padding: 4px 8px; font-size: 12px; }
        .modal-header.bg-warning { background: #ffc107 !important; color: #000; }
        .modal-header.bg-danger { background: #dc3545 !important; color: #fff; }
        
        /* Modern Select Styles */
        select, .form-control select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: white;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px 40px 10px 12px;
            font-size: 14px;
            color: #374151;
            transition: all 0.2s ease;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        
        select:hover {
            border-color: #1275a0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        select:focus {
            outline: none;
            border-color: #1275a0;
            box-shadow: 0 0 0 3px rgba(18, 117, 160, 0.1);
        }
        
        select:disabled {
            background-color: #f9fafb;
            color: #9ca3af;
            cursor: not-allowed;
        }
        
        /* Form Control Override */
        .form-control {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: white;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px 40px 10px 12px;
            font-size: 14px;
            color: #374151;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        
        .form-control:hover {
            border-color: #1275a0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .form-control:focus {
            outline: none;
            border-color: #1275a0;
            box-shadow: 0 0 0 3px rgba(18, 117, 160, 0.1);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="header-left">
            <div class="header-logo">
                <img src="https://angelescuauhtemoc.com/wp-content/uploads/2020/09/logo-50-300x187.png" alt="Hospital Angeles">
            </div>
            
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?php echo htmlspecialchars($user_nombre); ?></span>
                <span class="user-type">(<?php echo ucfirst($user_tipo); ?>)</span>
            </div>
        </div>
        
        <div class="logo-section">
            <div class="logo-text">IMAGENOLOGÍA</div>
        </div>
        
        <div class="header-right">
            <div class="header-buttons">
                <a href="index.php" class="btn-header">
                    <i class="fas fa-calendar"></i> Calendario
                </a>
                <a href="catalogo_servicios.php" class="btn-header">
                    <i class="fas fa-list"></i> Catálogo
                </a>
                <a href="cliente.php" class="btn-header">
                    <i class="fas fa-user-friends"></i> Vista Cliente
                </a>
                <a href="logout.php" class="btn-header">
                    <i class="fas fa-sign-out-alt"></i> Salir
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->

    <div class="admin-container">
        <div class="back-btn">
            <a href="index.php" class="btn btn-outline-primary">
                ← Volver al Calendario
            </a>
        </div>
        
        <div class="row">
            <!-- Crear Usuario Admin -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0">🔑 Crear Administrador</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <?= htmlspecialchars($success) ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="form-group">
                                <label for="nombre">Nombre Completo</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="correo">Correo Electrónico</label>
                                <input type="email" class="form-control" id="correo" name="correo" 
                                       value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <small class="form-text text-muted">Mínimo 6 caracteres</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            
                            <button type="submit" name="crear_admin" class="btn btn-danger btn-block">
                                Crear Administrador
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Lista de Usuarios -->
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">👥 Lista de Usuarios</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Tipo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                                        <td><?= htmlspecialchars($usuario['correo']) ?></td>
                                        <td>
                                            <?= getBadgeTipoUsuario($usuario['tipo']) ?>
                                        </td>
                                        <td>
                                            <?php if ($usuario['id'] != $_SESSION['usuario_id']): ?>
                                                <button class="btn btn-warning btn-sm" onclick="editarUsuario(<?= $usuario['id'] ?>, '<?= htmlspecialchars($usuario['nombre'], ENT_QUOTES) ?>', '<?= htmlspecialchars($usuario['correo'], ENT_QUOTES) ?>', '<?= $usuario['tipo'] ?>')">
                                                    ✏️ Editar
                                                </button>
                                                <button class="btn btn-danger btn-sm ml-1" onclick="eliminarUsuario(<?= $usuario['id'] ?>, '<?= htmlspecialchars($usuario['nombre'], ENT_QUOTES) ?>')">
                                                    🗑️ Eliminar
                                                </button>
                                            <?php else: ?>
                                                <small class="text-success">Tu usuario</small>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <strong>Registrar otros usuarios:</strong> 
                                <a href="registro.php" target="_blank">Usuario Caja/Lectura</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="modalEditar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">✏️ Editar Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="usuario_id" id="edit_usuario_id">
                        
                        <div class="form-group">
                            <label for="edit_nombre">Nombre Completo</label>
                            <input type="text" class="form-control" name="nombre" id="edit_nombre" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_correo">Correo Electrónico</label>
                            <input type="email" class="form-control" name="correo" id="edit_correo" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_tipo">Tipo de Usuario</label>
                            <select class="form-control" name="tipo" id="edit_tipo" required>
                                <option value="admin">Administrador</option>
                                <option value="caja">Caja</option>
                                <option value="lectura">Lectura</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_password">Nueva Contraseña (opcional)</label>
                            <input type="password" class="form-control" name="password" id="edit_password">
                            <small class="form-text text-muted">Dejar vacío para mantener la contraseña actual</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" name="editar_usuario" class="btn btn-warning">Actualizar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Eliminar Usuario -->
    <div class="modal fade" id="modalEliminar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title">🗑️ Eliminar Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="usuario_id" id="delete_usuario_id">
                        <p>¿Está seguro que desea eliminar al usuario <strong id="delete_usuario_nombre"></strong>?</p>
                        <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" name="eliminar_usuario" class="btn btn-danger">Eliminar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function editarUsuario(id, nombre, correo, tipo) {
            document.getElementById('edit_usuario_id').value = id;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_correo').value = correo;
            document.getElementById('edit_tipo').value = tipo;
            document.getElementById('edit_password').value = '';
            $('#modalEditar').modal('show');
        }
        
        function eliminarUsuario(id, nombre) {
            document.getElementById('delete_usuario_id').value = id;
            document.getElementById('delete_usuario_nombre').textContent = nombre;
            $('#modalEliminar').modal('show');
        }
        
        // Auto-cerrar alertas después de 5 segundos
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
</body>
</html>