<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Verificar que solo los admins puedan acceder
if (!puedeRealizar('gestionar_usuarios')) {
    header('Location: index.php');
    exit;
}

$usuario = obtenerUsuarioActual();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Servicios - Hospital Angeles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .header {
            background: #ffffff;
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
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
        
        .container-custom {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .page-title {
            color: #1f2937;
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .actions-bar {
            background: #ffffff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .btn-primary-custom {
            background-color: #007bff;
            border-color: #007bff;
            color: #ffffff;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary-custom:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        
        .services-grid {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1.5rem;
        }
        
        .table-custom {
            margin-bottom: 0;
        }
        
        .table-custom th {
            background-color: #f8f9fa;
            color: #1f2937;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        
        .table-custom td {
            vertical-align: middle;
            color: #333;
        }
        
        .btn-action {
            padding: 0.25rem 0.5rem;
            margin: 0 0.125rem;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-edit {
            background-color: #28a745;
            color: #ffffff;
        }
        
        .btn-edit:hover {
            background-color: #1e7e34;
        }
        
        .btn-delete {
            background-color: #dc3545;
            color: #ffffff;
        }
        
        .btn-delete:hover {
            background-color: #bd2130;
        }
        
        .modal-content {
            border-radius: 8px;
            border: none;
        }
        
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            border-radius: 8px 8px 0 0;
        }
        
        .form-control {
            border: 1px solid #ced4da;
            border-radius: 4px;
            color: #333;
            font-size: 0.95rem;
        }
        
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #6c757d;
            cursor: pointer;
        }
        
        .close-btn:hover {
            color: #000000;
        }
        
        .user-info {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .loading {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="logo-section">
                <img src="images/logo.png" alt="Hospital Angeles">
                <div>
                    <div class="logo-text">HOSPITAL ÁNGELES</div>
                    <small style="color: #6c757d;">IMAGENOLOGÍA - Catálogo de Servicios</small>
                </div>
            </div>
            <div class="user-info">
                Administrador: <strong><?= htmlspecialchars($usuario['nombre']) ?></strong>
                <button class="btn btn-sm btn-outline-secondary ml-2" onclick="window.close()" title="Cerrar ventana">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-custom">
        <h1 class="page-title">Catálogo de Servicios</h1>
        
        <!-- Actions Bar -->
        <div class="actions-bar">
            <div>
                <button class="btn-primary-custom" onclick="abrirModalNuevoServicio()">
                    <i class="fas fa-plus"></i> Nuevo Servicio
                </button>
            </div>
            <div>
                <span id="total-servicios" class="text-muted">Cargando...</span>
            </div>
        </div>
        
        <!-- Services Grid -->
        <div class="services-grid">
            <div id="loading" class="loading">
                <i class="fas fa-spinner fa-spin"></i> Cargando servicios...
            </div>
            <div id="services-table" style="display: none;">
                <table class="table table-custom table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre del Servicio</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Duración</th>
                            <th>Modalidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="services-tbody">
                        <!-- Los servicios se cargarán aquí -->
                    </tbody>
                </table>
            </div>
            <div id="empty-state" class="empty-state" style="display: none;">
                <i class="fas fa-clipboard-list"></i>
                <h4>No hay servicios registrados</h4>
                <p>Comience agregando su primer servicio al catálogo</p>
            </div>
        </div>
    </div>

    <!-- Modal Nuevo/Editar Servicio -->
    <div class="modal fade" id="modalServicio" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalServicioTitle">Nuevo Servicio</h5>
                    <button type="button" class="close-btn" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formServicio">
                        <input type="hidden" id="servicio_id" name="id">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="nombre">Nombre del Servicio *</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="precio">Precio *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control" id="precio" name="precio" step="0.01" min="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="duracion_minutos">Duración (minutos)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="duracion_minutos" name="duracion_minutos" min="5" max="180" value="30">
                                        <div class="input-group-append">
                                            <span class="input-group-text">min</span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Tiempo estimado del procedimiento (5-180 minutos)</small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Modalidad Asociada</label>
                            <select class="form-control" id="modalidad_id" name="modalidad_id">
                                <option value="">Seleccionar modalidad...</option>
                                <!-- Las modalidades se cargarán aquí -->
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarServicio()">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let servicios = [];
        let modalidades = [];
        
        // Inicializar cuando el documento esté listo
        $(document).ready(function() {
            cargarModalidades();
            cargarServicios();
        });
        
        // Cargar modalidades disponibles
        function cargarModalidades() {
            fetch('citas/modalidades_json.php')
                .then(response => response.json())
                .then(data => {
                    modalidades = data;
                    renderizarModalidades();
                })
                .catch(error => {
                    console.error('Error al cargar modalidades:', error);
                });
        }
        
        // Renderizar dropdown de modalidades
        function renderizarModalidades() {
            const select = document.getElementById('modalidad_id');
            
            modalidades.forEach(modalidad => {
                const option = document.createElement('option');
                option.value = modalidad.id;
                option.textContent = modalidad.nombre;
                select.appendChild(option);
            });
        }
        
        // Cargar servicios
        function cargarServicios() {
            document.getElementById('loading').style.display = 'block';
            document.getElementById('services-table').style.display = 'none';
            document.getElementById('empty-state').style.display = 'none';
            
            fetch('citas/servicios_json.php')
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Datos recibidos:', data);
                    
                    // Verificar si la respuesta es un error
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    
                    // Verificar si es un array
                    if (!Array.isArray(data)) {
                        throw new Error('Los datos recibidos no son un array válido');
                    }
                    
                    servicios = data;
                    renderizarServicios();
                    actualizarContador();
                })
                .catch(error => {
                    console.error('Error al cargar servicios:', error);
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('empty-state').style.display = 'block';
                    
                    // Mostrar error más específico
                    const emptyState = document.getElementById('empty-state');
                    emptyState.innerHTML = `
                        <i class="fas fa-exclamation-triangle"></i>
                        <h4>Error al cargar servicios</h4>
                        <p>Error: ${error.message}</p>
                        <button class="btn btn-primary" onclick="cargarServicios()">
                            <i class="fas fa-refresh"></i> Intentar de nuevo
                        </button>
                    `;
                });
        }
        
        // Renderizar tabla de servicios
        function renderizarServicios() {
            const tbody = document.getElementById('services-tbody');
            tbody.innerHTML = '';
            
            document.getElementById('loading').style.display = 'none';
            
            // Verificar que servicios sea un array válido
            if (!Array.isArray(servicios) || servicios.length === 0) {
                document.getElementById('empty-state').style.display = 'block';
                return;
            }
            
            document.getElementById('services-table').style.display = 'block';
            
            servicios.forEach(servicio => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${servicio.id}</td>
                    <td><strong>${servicio.nombre}</strong></td>
                    <td>${servicio.descripcion || 'Sin descripción'}</td>
                    <td>
                        <strong class="text-success">$${servicio.precio ? parseFloat(servicio.precio).toLocaleString('es-MX', {minimumFractionDigits: 2}) : '0.00'}</strong>
                    </td>
                    <td>
                        <span class="badge badge-info">
                            ${servicio.duracion_minutos || 30} min
                        </span>
                    </td>
                    <td>
                        <small class="text-muted">
                            ${servicio.modalidad_nombre || 'Sin modalidad'}
                        </small>
                    </td>
                    <td>
                        <button class="btn-action btn-edit" onclick="editarServicio(${servicio.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action btn-delete" onclick="eliminarServicio(${servicio.id})" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
        
        // Actualizar contador
        function actualizarContador() {
            const total = Array.isArray(servicios) ? servicios.length : 0;
            document.getElementById('total-servicios').textContent = `${total} servicios registrados`;
        }
        
        // Abrir modal para nuevo servicio
        function abrirModalNuevoServicio() {
            document.getElementById('modalServicioTitle').textContent = 'Nuevo Servicio';
            document.getElementById('formServicio').reset();
            document.getElementById('servicio_id').value = '';
            document.getElementById('modalidad_id').value = '';
            
            $('#modalServicio').modal('show');
        }
        
        // Editar servicio
        function editarServicio(id) {
            const servicio = servicios.find(s => s.id === id);
            if (!servicio) return;
            
            document.getElementById('modalServicioTitle').textContent = 'Editar Servicio';
            document.getElementById('servicio_id').value = servicio.id;
            document.getElementById('nombre').value = servicio.nombre;
            document.getElementById('descripcion').value = servicio.descripcion || '';
            document.getElementById('precio').value = servicio.precio || '';
            document.getElementById('duracion_minutos').value = servicio.duracion_minutos || 30;
            document.getElementById('modalidad_id').value = servicio.modalidad_id || '';
            
            $('#modalServicio').modal('show');
        }
        
        // Guardar servicio
        function guardarServicio() {
            const formData = new FormData(document.getElementById('formServicio'));
            
            const isEdit = document.getElementById('servicio_id').value !== '';
            const url = isEdit ? 'citas/actualizar_servicio.php' : 'citas/crear_servicio.php';
            
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#modalServicio').modal('hide');
                    cargarServicios();
                    alert(isEdit ? 'Servicio actualizado correctamente' : 'Servicio creado correctamente');
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al guardar el servicio');
            });
        }
        
        // Eliminar servicio
        function eliminarServicio(id) {
            const servicio = servicios.find(s => s.id === id);
            if (!servicio) return;
            
            if (confirm(`¿Está seguro que desea eliminar el servicio "${servicio.nombre}"?`)) {
                fetch('citas/eliminar_servicio.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        cargarServicios();
                        alert('Servicio eliminado correctamente');
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el servicio');
                });
            }
        }
    </script>
</body>
</html>