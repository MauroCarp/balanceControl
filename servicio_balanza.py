# servicio_balanza.py
from fastapi import FastAPI, HTTPException
import serial, datetime

PORT = "COM3"
BAUD = 9600

app = FastAPI()

def leer():
    with serial.Serial(PORT, BAUD, timeout=1) as ser:
        linea = ser.readline().decode(errors='ignore').strip()
        peso = None
        for part in linea.split():
            try:
                peso = float(part.replace(',', '.'))
                break
            except:
                pass
        if peso is None:
            raise ValueError("No se pudo obtener peso")
        return {
            "peso": peso,
            "unidad": "kg",
            "raw": linea,
            "timestamp": datetime.datetime.utcnow().isoformat() + "Z"
        }

@app.get("/read")
def read():
    try:
        return leer()
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))