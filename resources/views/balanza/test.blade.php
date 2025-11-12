<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Prueba de Balanza - Barlovento SRL</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            width: 100%;
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            color: #333;
            font-size: 2em;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 1.1em;
        }

        .controls {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .control-group {
            margin-bottom: 20px;
        }

        .control-group:last-child {
            margin-bottom: 0;
        }

        .control-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }

        .control-group input,
        .control-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s;
        }

        .control-group input:focus,
        .control-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            margin-top: 10px;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .result {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            margin-top: 30px;
            display: none;
        }

        .result.show {
            display: block;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .result-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .status-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 2em;
        }

        .status-icon.success {
            background: #d4edda;
            color: #28a745;
        }

        .status-icon.error {
            background: #f8d7da;
            color: #dc3545;
        }

        .status-icon.loading {
            background: #fff3cd;
            color: #ffc107;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .result-title {
            font-size: 1.5em;
            color: #333;
            font-weight: 600;
        }

        .weight-display {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 10px;
            margin: 20px 0;
        }

        .weight-value {
            font-size: 4em;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 10px;
        }

        .weight-unit {
            font-size: 1.5em;
            color: #666;
        }

        .result-details {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #666;
            font-weight: 600;
        }

        .detail-value {
            color: #333;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 600;
        }

        .badge-success {
            background: #d4edda;
            color: #28a745;
        }

        .badge-danger {
            background: #f8d7da;
            color: #dc3545;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error-message {
            color: #dc3545;
            padding: 15px;
            background: #f8d7da;
            border-radius: 8px;
            margin-top: 15px;
        }

        .info-message {
            color: #856404;
            padding: 15px;
            background: #fff3cd;
            border-radius: 8px;
            margin-top: 15px;
        }

        .json-viewer {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔬 Prueba de Balanza Digital</h1>
            <p>Sistema de lectura remota EL05B - Barlovento SRL</p>
        </div>

        <div class="controls">
            <div class="control-group">
                <label for="puerto">Puerto COM</label>
                <select id="puerto">
                    <option value="COM1">COM1</option>
                    <option value="COM2">COM2</option>
                    <option value="COM3">COM3</option>
                    <option value="COM4">COM4</option>
                </select>
            </div>

            <div class="control-group">
                <label for="baudrate">Velocidad (Baudrate)</label>
                <select id="baudrate">
                    <option value="1200" selected>1200</option>
                    <option value="2400">2400</option>
                    <option value="4800">4800</option>
                    <option value="9600">9600</option>
                </select>
            </div>

            <button id="btnObtenerPeso" class="btn btn-primary">
                ⚖️ Obtener Peso
            </button>

            <button id="btnUltimaLectura" class="btn btn-secondary">
                📊 Ver Última Lectura
            </button>
        </div>

        <div id="resultado" class="result">
            <!-- El resultado se mostrará aquí dinámicamente -->
        </div>
    </div>

    <script>
        // Configuración
        const API_BASE = '{{ url("/balanza") }}';
        const MAX_POLL_ATTEMPTS = 60; // 60 intentos x 1.5s = 90s máximo
        const POLL_INTERVAL = 1500; // 1.5 segundos

        // Elementos del DOM
        const btnObtenerPeso = document.getElementById('btnObtenerPeso');
        const btnUltimaLectura = document.getElementById('btnUltimaLectura');
        const resultado = document.getElementById('resultado');
        const puerto = document.getElementById('puerto');
        const baudrate = document.getElementById('baudrate');

        // CSRF Token para peticiones POST
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        /**
         * Muestra un mensaje de estado en el área de resultados
         */
        function showStatus(icon, title, message, type = 'loading') {
            resultado.className = `result show`;
            resultado.innerHTML = `
                <div class="result-header">
                    <div class="status-icon ${type}">
                        ${icon}
                    </div>
                    <div>
                        <div class="result-title">${title}</div>
                    </div>
                </div>
                ${message ? `<div class="info-message">${message}</div>` : ''}
                ${type === 'loading' ? '<div class="spinner"></div>' : ''}
            `;
        }

        /**
         * Muestra el resultado de la lectura
         */
        function showWeight(data) {
            const payload = data.payload || data;
            const lecturas = payload.lecturas || [];
            
            // Buscar primera lectura válida y estable
            let lectura = lecturas.find(l => l.valido && l.estable);
            if (!lectura && lecturas.length > 0) {
                lectura = lecturas[0];
            }

            if (!lectura) {
                showError('No se obtuvieron lecturas de la balanza');
                return;
            }

            const peso = lectura.peso_kg || 0;
            const estable = lectura.estable ? 'Sí' : 'No';
            const tipo = lectura.tipo || 'bruto';
            const timestamp = payload.timestamp || data.received_at || new Date().toISOString();

            resultado.className = `result show`;
            resultado.innerHTML = `
                <div class="result-header">
                    <div class="status-icon success">
                        ✓
                    </div>
                    <div>
                        <div class="result-title">Lectura Exitosa</div>
                    </div>
                </div>

                <div class="weight-display">
                    <div class="weight-value">${peso.toFixed(2)}</div>
                    <div class="weight-unit">kilogramos</div>
                </div>

                <div class="result-details">
                    <div class="detail-row">
                        <span class="detail-label">Estado:</span>
                        <span class="detail-value">
                            <span class="badge ${lectura.estable ? 'badge-success' : 'badge-warning'}">
                                ${estable}
                            </span>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Tipo de peso:</span>
                        <span class="detail-value">${tipo}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Válido:</span>
                        <span class="detail-value">
                            <span class="badge ${lectura.valido ? 'badge-success' : 'badge-danger'}">
                                ${lectura.valido ? 'Sí' : 'No'}
                            </span>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Timestamp:</span>
                        <span class="detail-value">${new Date(timestamp).toLocaleString('es-AR')}</span>
                    </div>
                </div>

                <details style="margin-top: 20px;">
                    <summary style="cursor: pointer; color: #667eea; font-weight: 600;">
                        Ver datos completos (JSON)
                    </summary>
                    <div class="json-viewer">
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                    </div>
                </details>
            `;
        }

        /**
         * Muestra un error
         */
        function showError(message, details = null) {
            resultado.className = `result show`;
            resultado.innerHTML = `
                <div class="result-header">
                    <div class="status-icon error">
                        ✗
                    </div>
                    <div>
                        <div class="result-title">Error</div>
                    </div>
                </div>
                <div class="error-message">
                    ${message}
                </div>
                ${details ? `
                    <details style="margin-top: 15px;">
                        <summary style="cursor: pointer;">Ver detalles</summary>
                        <div class="json-viewer">
                            <pre>${JSON.stringify(details, null, 2)}</pre>
                        </div>
                    </details>
                ` : ''}
            `;
        }

        /**
         * Consulta el estado de un job
         */
        async function pollJobStatus(jobId, attempt = 1) {
            try {
                const response = await fetch(`${API_BASE}/job-status?job_id=${encodeURIComponent(jobId)}`);
                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.error || 'Error consultando job');
                }

                const job = data.job;

                if (job.status === 'done') {
                    // Job completado
                    if (job.result && job.result.success !== false) {
                        showWeight(job.result);
                    } else {
                        showError(
                            job.result?.error || 'Error desconocido en la lectura',
                            job.result
                        );
                    }
                    return;
                }

                if (job.status === 'pending' || job.status === 'running') {
                    // Seguir esperando
                    if (attempt >= MAX_POLL_ATTEMPTS) {
                        showError(
                            'Timeout: La lectura está tardando demasiado. Verifica que el agente local esté ejecutándose.',
                            { job_id: jobId, attempts: attempt }
                        );
                        return;
                    }

                    const statusText = job.status === 'pending' 
                        ? 'Esperando que el agente procese la solicitud...'
                        : 'El agente está leyendo la balanza...';

                    showStatus(
                        '⏳',
                        `Procesando (${attempt}/${MAX_POLL_ATTEMPTS})`,
                        statusText,
                        'loading'
                    );

                    setTimeout(() => pollJobStatus(jobId, attempt + 1), POLL_INTERVAL);
                    return;
                }

                // Estado desconocido
                showError('Estado de job desconocido: ' + job.status, job);

            } catch (error) {
                showError('Error al consultar estado: ' + error.message);
            }
        }

        /**
         * Solicita una nueva lectura
         */
        async function obtenerPeso() {
            const puertoVal = puerto.value;
            const baudrateVal = parseInt(baudrate.value);

            btnObtenerPeso.disabled = true;
            btnUltimaLectura.disabled = true;

            showStatus(
                '🔄',
                'Enviando solicitud',
                'Creando job de lectura en el servidor...',
                'loading'
            );

            try {
                const response = await fetch(`${API_BASE}/request`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        puerto: puertoVal,
                        baudrate: baudrateVal
                    })
                });

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.error || 'Error al crear solicitud');
                }

                const jobId = data.job.id;

                showStatus(
                    '⏳',
                    'Esperando lectura',
                    `Job creado: ${jobId}. Esperando que el agente local procese la solicitud...`,
                    'loading'
                );

                // Iniciar polling del estado
                setTimeout(() => pollJobStatus(jobId), POLL_INTERVAL);

            } catch (error) {
                showError('Error al solicitar lectura: ' + error.message);
                btnObtenerPeso.disabled = false;
                btnUltimaLectura.disabled = false;
            }
        }

        /**
         * Obtiene la última lectura guardada
         */
        async function obtenerUltimaLectura() {
            btnObtenerPeso.disabled = true;
            btnUltimaLectura.disabled = true;

            showStatus(
                '📊',
                'Consultando última lectura',
                'Obteniendo datos del servidor...',
                'loading'
            );

            try {
                const response = await fetch(`${API_BASE}/latest`);
                
                if (response.status === 404) {
                    showError('No hay lecturas guardadas todavía');
                    btnObtenerPeso.disabled = false;
                    btnUltimaLectura.disabled = false;
                    return;
                }

                const data = await response.json();

                if (!data.success && data.success !== undefined) {
                    throw new Error(data.error || 'Error al obtener última lectura');
                }

                showWeight(data);
                btnObtenerPeso.disabled = false;
                btnUltimaLectura.disabled = false;

            } catch (error) {
                showError('Error al obtener última lectura: ' + error.message);
                btnObtenerPeso.disabled = false;
                btnUltimaLectura.disabled = false;
            }
        }

        // Event listeners
        btnObtenerPeso.addEventListener('click', obtenerPeso);
        btnUltimaLectura.addEventListener('click', obtenerUltimaLectura);

        // Permitir Enter en los campos
        puerto.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') obtenerPeso();
        });
        baudrate.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') obtenerPeso();
        });
    </script>
</body>
</html>
