import serial
import time
import json
from datetime import datetime

class BalanzaEL05B:
    def __init__(self, port='COM1', baud_rate=9600):
        self.port = port
        self.baud_rate = baud_rate
        self.ser = None
        
    def connect(self):
        """Conecta con la balanza a través del puerto serie"""
        try:
            self.ser = serial.Serial(
                port=self.port,
                baudrate=self.baud_rate,
                bytesize=serial.EIGHTBITS,
                parity=serial.PARITY_NONE,
                stopbits=serial.STOPBITS_ONE,
                timeout=2,
                xonxoff=True,  # Habilitar control de flujo por software
                rtscts=False,  # Deshabilitar control de flujo por hardware
                dsrdtr=False   # Deshabilitar control de flujo por hardwaref
            )
            
            # Limpiar cualquier dato residual
            self.ser.reset_input_buffer()
            self.ser.reset_output_buffer()
            
            # Configurar líneas de control
            self.ser.setDTR(True)  # Data Terminal Ready
            self.ser.setRTS(True)  # Request To Send
            
            # print(f"Puerto {self.port} abierto con éxito")
            # print(f"Configuración: {self.baud_rate} baud, 8N1")
            
            return True
        except Exception as e:
            print(f"Error al conectar: {e}")
            return False
            
    def disconnect(self):
        """Cierra la conexión con la balanza"""
        if self.ser and self.ser.is_open:
            self.ser.close()
            
    def parse_status(self, status_byte):
        """Interpreta el byte de estado de la balanza"""
        status = int.from_bytes(status_byte, byteorder='big')
        return {
            'tipo_peso': 'neto' if status & 0x01 else 'bruto',
            'centro_cero': bool(status & 0x02),
            'equilibrio': bool(status & 0x04),
            'negativo': bool(status & 0x08),
            'fuera_rango': bool(status & 0x10)
        }
        
    def read_weight(self):
        """Lee el peso actual de la balanza"""
        if not self.ser or not self.ser.is_open:
            raise Exception("La balanza no está conectada")
            
        try:
            # Limpiar buffers
            self.ser.reset_input_buffer()
            self.ser.reset_output_buffer()
            
            # Intentar enviar un comando para solicitar peso (puede variar según el modelo)
            # print("Enviando solicitud de peso...")
            self.ser.write(b'\x05')  # ENQ - Solicitud
            
            # Esperar a que haya datos disponibles
            timeout = time.time() + 2  # 2 segundos de timeout
            while time.time() < timeout:
                if self.ser.in_waiting > 0:
                    break
                time.sleep(0.1)
            
            # print(f"Bytes disponibles para leer: {self.ser.in_waiting}")
            
            # Leer datos disponibles
            data = self.ser.read(8)  # Leer 8 bytes (1 estado + 6 peso + 1 CR)
            
            # Imprimir los bytes recibidos para diagnóstico
            # print("\nBytes recibidos:")
            # print(f"Datos completos (hex): {data.hex()}")
            # print(f"Datos completos (bytes): {[b for b in data]}")
            
            if len(data) < 8:
                raise Exception(f"No se recibieron datos suficientes. Bytes recibidos: {len(data)}")
                
            # Separar los datos
            status_byte = data[0:1]
            weight_bytes = data[1:7]
            
            # Mostrar bytes del peso para diagnóstico
            # print(f"Bytes de peso (hex): {weight_bytes.hex()}")
            # print(f"Bytes de peso (bytes): {[b for b in weight_bytes]}")
            
            # Interpretar el estado
            status = self.parse_status(status_byte)
            
            # Convertir el peso (6 bytes) a número
            weight_str = ''
            for byte in weight_bytes:
                # Verificar si es un dígito ASCII (0-9) o un espacio
                if 0x30 <= byte <= 0x39:  # Dígitos 0-9
                    weight_str += str(byte - 0x30)
                elif byte == 0x20:  # Espacio
                    weight_str += '0'  # Convertir espacios en ceros
                else:
                    print(f"Byte no esperado en el peso: 0x{byte:02x}")
            
            if not weight_str:
                raise Exception("No se pudo interpretar el peso recibido")
            
            # print(f"Peso interpretado: {weight_str}")
                
            weight = float(weight_str) / 100  # Dividir por 100 para obtener los decimales
            
            return {
                'peso': weight,
                'estatus': status,
                'peso_formateado': f"{weight:.2f}",
                'en_equilibrio': status['equilibrio'],
                'valido': not status['fuera_rango']
            }
            
        except Exception as e:
            raise Exception(f"Error al leer el peso: {e}")

def get_weight_json(port='COM1', baud_rate=9600):
    """Función para obtener las lecturas en formato JSON"""
    balanza = BalanzaEL05B(port, baud_rate)
    
    resultados = {
        'timestamp': datetime.now().isoformat(),
        'configuracion': {
            'puerto': port,
            'velocidad': baud_rate
        },
        'lecturas': [],
        'error': None
    }
    
    if not balanza.connect():
        resultados['error'] = "No se pudo conectar con la balanza"
        return json.dumps(resultados, indent=2)
    
    try:
        for i in range(5):  # máximo 5 intentos
            try:
                result = balanza.read_weight()
                peso_x10 = float(result['peso_formateado']) * 10
                
                lectura = {
                    'intento': i + 1,
                    'peso_kg': float(result['peso_formateado']),
                    'peso_x10_kg': peso_x10,
                    'tipo': result['estatus']['tipo_peso'],
                    'estable': result['en_equilibrio'],
                    'valido': result['valido'],
                    'estatus': result['estatus']
                }
                resultados['lecturas'].append(lectura)
                
                # Si la lectura es válida y estable, terminar
                if result['valido'] and result['en_equilibrio']:
                    # print("\n✅ Lectura válida y estable obtenida, finalizando...")
                    break
                
            except Exception as e:
                resultados['lecturas'].append({
                    'intento': i + 1,
                    'error': str(e)
                })
                
            if i < 4:  # No esperar después del último intento
                time.sleep(1)
    
    except Exception as e:
        resultados['error'] = str(e)
    
    finally:
        balanza.disconnect()
    
    return json.dumps(resultados, indent=2)

def test_balanza(port='COM1', baud_rate=9600):
    """Función de prueba principal"""
    balanza = BalanzaEL05B(port, baud_rate)
    
    # print("🔌 Conectando con la balanza...")
    if not balanza.connect():
        # print("❌ No se pudo conectar con la balanza")
        return
        
    # print("✅ Conexión establecida")
    
    try:
        # print("\n📊 Leyendo peso...")
        for i in range(5):  # Leer 5 veces
            result = balanza.read_weight()
            # print(f"\nLectura #{i+1}:")
            # Multiplicar el peso formateado por 10
            peso_x10 = float(result['peso_formateado']) * 10
            # print(f"Peso: {result['peso_formateado']} kg")
            # print(f"Peso x10: {peso_x10:.2f} kg")
            # print(f"Tipo: {result['estatus']['tipo_peso']}")
            # print(f"Estable: {'✅' if result['en_equilibrio'] else '❌'}")
            # print(f"Válido: {'✅' if result['valido'] else '❌'}")
            time.sleep(1)  # Esperar 1 segundo entre lecturas
            
    except Exception as e:
        print(f"❌ Error: {e}")
        
    finally:
        # print("\n🔌 Desconectando...")
        balanza.disconnect()
        # print("✅ Desconectado")

if __name__ == "__main__":
    # Configuración
    PORT = 'COM1'
    BAUD_RATE = 1200  # Solo usar 1200 baudios
    
    # print("📊 Obteniendo lecturas de la balanza en formato JSON...")
    # print(f"Puerto: {PORT}")
    # print(f"Velocidad: {BAUD_RATE} baudios\n")
    
    # Obtener y mostrar el JSON
    json_resultado = get_weight_json(PORT, BAUD_RATE)
    print(json_resultado)