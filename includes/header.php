<?php
// Función para generar el header consistente
function generarHeader($titulo = "IMAGENOLOGÍA", $subtitulo = "", $mostrarUsuario = true) {
    $usuario = obtenerUsuarioActual();
    ?>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="logo-section">
                <img src="images/logo.png" alt="Hospital Angeles">
                <div>
                    <div class="logo-text">HOSPITAL ÁNGELES</div>
                    <small style="color: #6c757d;"><?= $titulo ?><?= $subtitulo ? " - " . $subtitulo : "" ?></small>
                </div>
            </div>
            <?php if ($mostrarUsuario): ?>
            <div class="user-info">
                <?= htmlspecialchars($usuario['nombre']) ?> 
                <?= getBadgeTipoUsuario($usuario['tipo']) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

// CSS común para el header
function generarHeaderCSS() {
    ?>
    <style>
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
        }
        
        .logo-section img {
            height: 50px;
            margin-right: 15px;
        }
        
        .logo-text {
            color: #1f2937;
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .user-info {
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
    <?php
}
?>