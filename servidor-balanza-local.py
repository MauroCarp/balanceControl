from flask import Flask, jsonify, request
from flask_cors import CORS
import logging
import sys
import json
import os
from datetime import datetime

# Configurar logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler('balanza_server.log'),
        logging.StreamHandler(sys.stdout)
    ]
)
logger = logging.getLogger('balanza-server')

# Función para verificar la instalación de Python y las dependencias
def check_python_env():
    logger.info(f"Python ejecutándose desde: {sys.executable}")
    logger.info(f"Versión de Python: {sys.version}")
    logger.info(f"PATH de Python: {os.environ.get('PYTHONPATH', 'No definido')}")
    
    # Listar todos los paths donde Python busca módulos
    logger.info("Python sys.path:")
    for path in sys.path:
        logger.info(f"  - {path}")

# Verificar dependencias necesarias con más información
required_packages = ['flask', 'flask-cors', 'pyserial']
missing_packages = []
installed_packages = {}

for package in required_packages:
    try:
        module_name = package.replace('-', '_')
        if module_name == 'pyserial':
            # Intentar múltiples nombres de importación para pyserial
            try:
                import serial
                installed_packages[package] = getattr(serial, 'VERSION', 'Versión desconocida')
                logger.info(f"PySerial encontrado en: {serial.__file__}")
                continue
            except ImportError:
                pass
        
        module = __import__(module_name)
        installed_packages[package] = getattr(module, '__version__', 'Versión desconocida')
        logger.info(f"Paquete {package} encontrado en: {module.__file__}")
    except ImportError as e:
        missing_packages.append(package)
        logger.error(f"Error al importar {package}: {str(e)}")

# Mostrar información del entorno antes de decidir si salir
check_python_env()

if missing_packages:
    logger.error(f"Faltan las siguientes dependencias: {', '.join(missing_packages)}")
    logger.error("Instala las dependencias con: pip install " + ' '.join(missing_packages))
    
    # Mostrar paquetes instalados
    logger.info("Paquetes instalados correctamente:")
    for package, version in installed_packages.items():
        logger.info(f"  - {package}: {version}")
    
    # Intentar ejecutar pip list para ver todos los paquetes instalados
    try:
        import subprocess
        result = subprocess.run([sys.executable, "-m", "pip", "list"], 
                              capture_output=True, text=True)
        logger.info("Lista completa de paquetes instalados:")
        logger.info(result.stdout)
    except Exception as e:
        logger.error(f"Error al listar paquetes: {e}")
    
    sys.exit(1)

# Importar la función de lectura de balanza
try:
    from test_balanza import get_weight_json, BalanzaEL05B
    logger.info("Módulo de balanza importado correctamente")
except ImportError as e:
    logger.error(f"Error al importar el módulo de balanza: {e}")
    sys.exit(1)

app = Flask(__name__)
CORS(app)

# Manejador global de errores
@app.errorhandler(Exception)
def handle_exception(e):
    logger.error(f"Error no manejado: {str(e)}", exc_info=True)
    return jsonify({
        'status': 'error',
        'error': str(e),
        'tipo': type(e).__name__,
        'timestamp': datetime.now().isoformat()
    }), 500

# Variables globales para tracking
last_reading = None
error_count = 0
start_time = datetime.now()

@app.route('/peso', methods=['GET'])
def get_weight():
    global last_reading, error_count
    
    try:
        # Obtener puerto de la URL o usar valor por defecto
        port = request.args.get('puerto', 'COM1')
        baud_rate = int(request.args.get('baudrate', '1200'))
        
        logger.info(f"Solicitando peso - Puerto: {port}, Baudrate: {baud_rate}")
        
        # Usar directamente get_weight_json como en test_balanza.py
        try:
            json_str = get_weight_json(port=port, baud_rate=baud_rate)
            logger.info(f"Respuesta raw de get_weight_json: {json_str}")
            
            data = json.loads(json_str)
            
            # Guardar última lectura exitosa
            last_reading = {
                'timestamp': datetime.now().isoformat(),
                'data': data
            }
            
            return jsonify(data)
            
        except Exception as e:
            logger.error(f"Error en get_weight_json: {str(e)}")
            return jsonify({
                'success': False,
                'error': str(e),
                'configuracion': {
                    'puerto': port,
                    'velocidad': baud_rate
                },
                'timestamp': datetime.now().isoformat(),
                'lecturas': []
            })
        
    except Exception as e:
        error_count += 1
        logger.error(f"Error al leer balanza: {str(e)}")
        return jsonify({
            'success': False,
            'error': str(e),
            'detalles': {
                'error_count': error_count,
                'tipo_error': type(e).__name__
            }
        }), 500

@app.route('/status', methods=['GET'])
def get_status():
    """Endpoint para verificar el estado del servidor y la balanza"""
    try:
        logger.info("Iniciando verificación de estado")
        
        # Crear el objeto de estado paso a paso
        status_data = {'status': 'online'}
        logger.info("Paso 1: Estado básico creado")
        
        # Información del servicio
        status_data.update({
            'service': 'balanza-local',
            'timestamp': datetime.now().isoformat()
        })
        logger.info("Paso 2: Información de servicio agregada")
        
        # Cálculo de uptime
        try:
            uptime = datetime.now() - start_time
            status_data['uptime'] = str(uptime)
        except Exception as e:
            logger.error(f"Error calculando uptime: {str(e)}")
            status_data['uptime'] = "Error calculando uptime"
        logger.info("Paso 3: Uptime calculado")
        
        # Información de versiones
        try:
            status_data['version'] = {
                'python': sys.version.split()[0],
                'flask': getattr(Flask, '__version__', 'unknown'),
                'server': '1.1.0'
            }
        except Exception as e:
            logger.error(f"Error obteniendo versiones: {str(e)}")
            status_data['version'] = {'error': str(e)}
        logger.info("Paso 4: Versiones agregadas")
        
        # Estado de la última lectura
        try:
            status_data['last_reading'] = last_reading if last_reading else None
        except Exception as e:
            logger.error(f"Error con última lectura: {str(e)}")
            status_data['last_reading'] = None
        logger.info("Paso 5: Última lectura verificada")
        
        # Verificación de puertos
        try:
            import serial.tools.list_ports
            ports = [p.device for p in serial.tools.list_ports.comports()]
            status_data['puertos_disponibles'] = ports
        except Exception as e:
            logger.error(f"Error verificando puertos: {str(e)}")
            status_data['puertos_disponibles'] = []
            status_data['error_puertos'] = str(e)
        logger.info("Paso 6: Puertos verificados")
        
        # Información de errores
        status_data['error_count'] = error_count
        logger.info("Paso 7: Conteo de errores agregado")
        
        logger.info("Estado completo generado exitosamente")
        return jsonify(status_data)
        
    except Exception as e:
        logger.error(f"Error crítico en status: {str(e)}", exc_info=True)
        return jsonify({
            'status': 'error',
            'error': str(e),
            'timestamp': datetime.now().isoformat()
        }), 500

@app.route('/diagnostico', methods=['GET'])
def run_diagnostics():
    """Endpoint para ejecutar diagnóstico completo"""
    try:
        results = {
            'servidor': {
                'status': 'ok',
                'uptime': str(datetime.now() - start_time),
                'error_count': error_count
            },
            'sistema': {
                'python_version': sys.version,
                'platform': sys.platform,
                'cwd': os.getcwd(),
                'python_path': sys.executable,
                'sys_path': sys.path
            },
            'modulos': {
                'flask': Flask.__version__,
                'dependencias': {}
            },
            'balanza': {
                'ultima_lectura': last_reading,
                'puertos': []
            }
        }
        
        # Verificar módulos con más detalle
        for package in required_packages:
            try:
                if package == 'pyserial':
                    import serial
                    results['modulos']['dependencias']['pyserial'] = {
                        'version': getattr(serial, 'VERSION', 'Desconocida'),
                        'path': serial.__file__,
                        'status': 'instalado'
                    }
                else:
                    mod = __import__(package.replace('-', '_'))
                    results['modulos']['dependencias'][package] = {
                        'version': getattr(mod, '__version__', 'Desconocida'),
                        'path': mod.__file__,
                        'status': 'instalado'
                    }
            except Exception as e:
                results['modulos']['dependencias'][package] = {
                    'status': 'error',
                    'error': str(e)
                }
        
        # Verificar puertos serie con más detalle
        try:
            import serial.tools.list_ports
            puertos = list(serial.tools.list_ports.comports())
            results['balanza']['puertos'] = []
            
            for p in puertos:
                puerto_info = {
                    'device': p.device,
                    'description': p.description,
                    'manufacturer': p.manufacturer,
                    'hwid': p.hwid,
                    'vid': hex(p.vid) if p.vid else None,
                    'pid': hex(p.pid) if p.pid else None,
                    'serial_number': p.serial_number,
                    'location': p.location
                }
                
                # Intentar abrir el puerto para verificar acceso
                try:
                    test_serial = serial.Serial(p.device, timeout=1)
                    test_serial.close()
                    puerto_info['accesible'] = True
                except Exception as e:
                    puerto_info['accesible'] = False
                    puerto_info['error_acceso'] = str(e)
                
                results['balanza']['puertos'].append(puerto_info)
                
        except Exception as e:
            results['balanza']['error_puertos'] = str(e)
        
        return jsonify(results)
        
    except Exception as e:
        logger.error(f"Error en diagnóstico: {str(e)}")
        return jsonify({
            'status': 'error',
            'error': str(e),
            'traceback': str(sys.exc_info())
        }), 500

@app.route('/')
def home():
    """Página de inicio con información básica"""
    return """
    <html>
        <head>
            <title>Servidor Balanza Local</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                code { background: #f0f0f0; padding: 2px 5px; }
            </style>
        </head>
        <body>
            <h1>Servidor Balanza Local</h1>
            <p>Servidor funcionando correctamente.</p>
            <h2>Endpoints disponibles:</h2>
            <ul>
                <li><code>/peso?puerto=COM1</code> - Obtener peso de la balanza</li>
                <li><code>/status</code> - Estado del servidor</li>
                <li><code>/diagnostico</code> - Diagnóstico completo</li>
            </ul>
            <p><small>Versión 1.1.0</small></p>
        </body>
    </html>
    """

if __name__ == '__main__':
    # Verificar que podamos importar serial
    try:
        import serial
        logger.info("Módulo PySerial importado correctamente")
    except ImportError:
        logger.error("PySerial no está instalado. Instálalo con: pip install pyserial")
        sys.exit(1)
    
    # Iniciar servidor
    logger.info("Iniciando servidor en http://127.0.0.1:5000")
    app.run(host='127.0.0.1', port=5000, debug=False)