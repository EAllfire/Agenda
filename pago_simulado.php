<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulador de Pago - Hospital Angeles</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #1f2937;
            --secondary-color: #3b82f6;
            --accent-color: #10b981;
            --light-bg: #f8fafc;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #374151;
            background: var(--light-bg);
        }

        .payment-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .payment-card {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: var(--card-shadow);
            max-width: 500px;
            width: 100%;
        }

        .payment-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .payment-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, var(--warning-color), #d97706);
        }

        .payment-amount {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 1rem;
        }

        .payment-description {
            color: #6b7280;
            text-align: center;
            margin-bottom: 2rem;
        }

        .simulator-notice {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 1px solid #f59e0b;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .simulator-notice h5 {
            color: #92400e;
            margin-bottom: 0.5rem;
        }

        .simulator-notice p {
            color: #a16207;
            margin-bottom: 0;
            font-size: 0.9rem;
        }

        .payment-options {
            display: grid;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .payment-option {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .payment-option:hover {
            border-color: var(--accent-color);
            background: #f0fdf4;
        }

        .payment-option.selected {
            border-color: var(--accent-color);
            background: #f0fdf4;
        }

        .payment-option i {
            font-size: 1.5rem;
            color: var(--accent-color);
            margin-right: 1rem;
        }

        .payment-option .option-info h6 {
            margin: 0 0 0.25rem 0;
            color: var(--primary-color);
        }

        .payment-option .option-info p {
            margin: 0;
            color: #6b7280;
            font-size: 0.9rem;
        }

        .btn-pay {
            background: var(--accent-color);
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 1.1rem;
        }

        .btn-pay:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -4px rgba(16, 185, 129, 0.4);
        }

        .btn-cancel {
            background: var(--danger-color);
            border: none;
            border-radius: 12px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-cancel:hover {
            background: #dc2626;
        }

        /* Loading States */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Status Messages */
        .status-message {
            text-align: center;
            padding: 2rem;
        }

        .status-success .status-icon {
            background: linear-gradient(135deg, var(--accent-color), #059669);
        }

        .status-error .status-icon {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
        }

        .status-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            margin: 0 auto 1.5rem;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-card">
            <!-- Payment Form -->
            <div id="paymentForm">
                <div class="payment-header">
                    <div class="payment-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h2>Pago de Consulta</h2>
                </div>

                <div class="payment-amount" id="paymentAmount">$0.00</div>
                <div class="payment-description" id="paymentDescription">Cargando información...</div>

                <div class="simulator-notice">
                    <h5><i class="fas fa-flask me-2"></i>Modo Simulador</h5>
                    <p>Este es un simulador de pago para pruebas. No se realizarán cargos reales a tarjetas.</p>
                </div>

                <div class="payment-options">
                    <div class="payment-option selected" data-result="success">
                        <i class="fas fa-check-circle"></i>
                        <div class="option-info">
                            <h6>Simular Pago Exitoso</h6>
                            <p>El pago será procesado correctamente</p>
                        </div>
                    </div>

                    <div class="payment-option" data-result="error">
                        <i class="fas fa-times-circle"></i>
                        <div class="option-info">
                            <h6>Simular Error de Pago</h6>
                            <p>El pago fallará por fondos insuficientes</p>
                        </div>
                    </div>

                    <div class="payment-option" data-result="cancel">
                        <i class="fas fa-ban"></i>
                        <div class="option-info">
                            <h6>Cancelar Pago</h6>
                            <p>Usuario cancela el proceso de pago</p>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-success btn-pay" onclick="procesarPago()">
                    <i class="fas fa-credit-card me-2"></i>
                    Procesar Pago
                </button>

                <button type="button" class="btn btn-danger btn-cancel" onclick="cancelarPago()">
                    Cancelar y Regresar
                </button>
            </div>

            <!-- Success Message -->
            <div id="successMessage" class="status-message status-success" style="display: none;">
                <div class="status-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h3>¡Pago Exitoso!</h3>
                <p>Su pago ha sido procesado correctamente. Recibirá un email de confirmación en breve.</p>
                <button type="button" class="btn btn-success btn-pay mt-3" onclick="regresarInicio()">
                    <i class="fas fa-home me-2"></i>
                    Regresar al Inicio
                </button>
            </div>

            <!-- Error Message -->
            <div id="errorMessage" class="status-message status-error" style="display: none;">
                <div class="status-icon">
                    <i class="fas fa-times"></i>
                </div>
                <h3>Error en el Pago</h3>
                <p id="errorText">Ha ocurrido un error procesando su pago.</p>
                <button type="button" class="btn btn-primary btn-pay mt-3" onclick="reintentar()">
                    <i class="fas fa-redo me-2"></i>
                    Reintentar Pago
                </button>
                <button type="button" class="btn btn-secondary mt-2" onclick="regresarInicio()">
                    Regresar al Inicio
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let paymentData = {};
        let selectedResult = 'success';

        // Inicializar página
        document.addEventListener('DOMContentLoaded', function() {
            loadPaymentInfo();
            setupEventListeners();
        });

        function setupEventListeners() {
            // Event listeners para opciones de pago
            document.querySelectorAll('.payment-option').forEach(option => {
                option.addEventListener('click', function() {
                    document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedResult = this.dataset.result;
                });
            });
        }

        async function loadPaymentInfo() {
            const urlParams = new URLSearchParams(window.location.search);
            const referencia = urlParams.get('ref');

            if (!referencia) {
                showError('Referencia de pago no válida');
                return;
            }

            try {
                // En un caso real, aquí consultaríamos los detalles del pago
                // Por ahora simulamos los datos
                paymentData = {
                    referencia: referencia,
                    monto: 2500.00,
                    descripcion: 'Consulta médica - Hospital Angeles'
                };

                document.getElementById('paymentAmount').textContent = '$' + paymentData.monto.toLocaleString();
                document.getElementById('paymentDescription').textContent = paymentData.descripcion;

            } catch (error) {
                console.error('Error cargando información de pago:', error);
                showError('Error cargando información de pago');
            }
        }

        async function procesarPago() {
            const btn = document.querySelector('.btn-pay');
            const originalText = btn.innerHTML;

            // Mostrar loading
            btn.innerHTML = '<span class="spinner"></span> Procesando...';
            btn.disabled = true;
            document.getElementById('paymentForm').classList.add('loading');

            // Simular delay de procesamiento
            await new Promise(resolve => setTimeout(resolve, 3000));

            try {
                // Simular resultado basado en selección
                if (selectedResult === 'success') {
                    // Enviar webhook simulado de éxito
                    await enviarWebhook('completado');
                    mostrarExito();
                } else if (selectedResult === 'error') {
                    // Simular error
                    await enviarWebhook('fallido');
                    mostrarError('Fondos insuficientes. Por favor verifique su tarjeta.');
                } else {
                    // Cancelación
                    await enviarWebhook('cancelado');
                    mostrarError('Pago cancelado por el usuario.');
                }

            } catch (error) {
                console.error('Error procesando pago:', error);
                mostrarError('Error de conexión. Por favor intente nuevamente.');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
                document.getElementById('paymentForm').classList.remove('loading');
            }
        }

        async function enviarWebhook(estado) {
            // Simular envío de webhook al servidor
            const webhookData = {
                referencia: paymentData.referencia,
                estado: estado,
                monto: paymentData.monto,
                timestamp: new Date().toISOString()
            };

            // En un caso real, esto sería enviado automáticamente por el proveedor
            await fetch('webhook_simulado.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(webhookData)
            });
        }

        function mostrarExito() {
            document.getElementById('paymentForm').style.display = 'none';
            document.getElementById('successMessage').style.display = 'block';
        }

        function mostrarError(mensaje) {
            document.getElementById('errorText').textContent = mensaje;
            document.getElementById('paymentForm').style.display = 'none';
            document.getElementById('errorMessage').style.display = 'block';
        }

        function showError(mensaje) {
            mostrarError(mensaje);
        }

        function reintentar() {
            document.getElementById('errorMessage').style.display = 'none';
            document.getElementById('paymentForm').style.display = 'block';
            selectedResult = 'success'; // Reset a éxito por defecto
            document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected'));
            document.querySelector('[data-result="success"]').classList.add('selected');
        }

        function cancelarPago() {
            if (confirm('¿Está seguro de que desea cancelar el pago?')) {
                window.location.href = 'cliente.php';
            }
        }

        function regresarInicio() {
            window.location.href = 'cliente.php';
        }
    </script>
</body>
</html>