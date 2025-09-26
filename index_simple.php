<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Angeles - Servicios de Imagenología</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .header {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            color: white;
            padding: 2rem 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .logo-text {
            font-size: 2.2rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 0.5rem;
        }
        
        .subtitle {
            text-align: center;
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .main-content {
            padding: 3rem 0;
        }
        
        .modalidad-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        
        .modalidad-card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        
        .card-header i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .loading {
            text-align: center;
            padding: 3rem;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo-text">HOSPITAL ANGELES</div>
            <div class="subtitle">SERVICIOS DE IMAGENOLOGÍA</div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <h2 class="text-center mb-5">Nuestras Modalidades de Imagenología</h2>
            
            <div id="modalidades-grid" class="row">
                <!-- Modalidades estáticas para prueba -->
                <div class="col-lg-4 col-md-6">
                    <div class="modalidad-card" onclick="verServicios(1, 'Radiología')">
                        <div class="card-header">
                            <i class="fas fa-x-ray"></i>
                            <h3>Radiología</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-center">Servicios de rayos X tradicionales</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="modalidad-card" onclick="verServicios(2, 'Tomografía')">
                        <div class="card-header">
                            <i class="fas fa-brain"></i>
                            <h3>Tomografía</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-center">Estudios de TC con múltiples cortes</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="modalidad-card" onclick="verServicios(3, 'Resonancia Magnética')">
                        <div class="card-header">
                            <i class="fas fa-magnet"></i>
                            <h3>Resonancia Magnética</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-center">RM de alta resolución</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <button class="btn btn-primary btn-lg" onclick="cargarModalidadesDinamicas()">
                        <i class="fas fa-sync-alt"></i> Cargar Modalidades desde Base de Datos
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function verServicios(modalidadId, modalidadNombre) {
            window.location.href = `servicios.php?modalidad=${modalidadId}&nombre=${encodeURIComponent(modalidadNombre)}`;
        }
        
        function cargarModalidadesDinamicas() {
            $('#modalidades-grid').html('<div class="col-12 loading"><i class="fas fa-spinner fa-spin"></i><p>Cargando modalidades...</p></div>');
            
            fetch('api/modalidades.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Datos recibidos:', data);
                    mostrarModalidadesDinamicas(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    $('#modalidades-grid').html(`
                        <div class="col-12">
                            <div class="alert alert-danger text-center">
                                <i class="fas fa-exclamation-triangle"></i>
                                Error al cargar las modalidades: ${error.message}
                                <br><small>Por favor, revise la consola del navegador para más detalles.</small>
                            </div>
                        </div>
                    `);
                });
        }
        
        function mostrarModalidadesDinamicas(modalidades) {
            const iconos = {
                'Radiología': 'fas fa-x-ray',
                'Tomografía': 'fas fa-brain', 
                'Resonancia Magnética': 'fas fa-magnet',
                'Ultrasonido': 'fas fa-heartbeat',
                'Mamografía': 'fas fa-female'
            };
            
            let html = '';
            modalidades.forEach(modalidad => {
                const icono = iconos[modalidad.nombre] || 'fas fa-medical';
                html += `
                    <div class="col-lg-4 col-md-6">
                        <div class="modalidad-card" onclick="verServicios(${modalidad.id}, '${modalidad.nombre}')">
                            <div class="card-header">
                                <i class="${icono}"></i>
                                <h3>${modalidad.nombre}</h3>
                            </div>
                            <div class="card-body">
                                <p class="text-center">${modalidad.total_servicios} servicios disponibles</p>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            $('#modalidades-grid').html(html);
        }
    </script>
</body>
</html>