# 🔴 PROBLEMA ACTUAL: Servidor Linux con Restricción open_basedir

## Diagnóstico

### ❌ Error Principal
```
file_exists(): open_basedir restriction in effect. 
File(/dev/ttyS0) is not within the allowed path(s)
```

### 🔍 Causa Raíz
- **Servidor de producción**: Linux (Ferozo Hosting)
- **Desarrollo local**: Windows 7
- **Problema**: PHP está configurado con `open_basedir` que NO incluye `/dev`
- **Consecuencia**: No se puede acceder a los puertos seriales `/dev/ttyUSB*` o `/dev/ttyS*`

### 📊 Información del Hosting
- **Proveedor**: Ferozo
- **Cuenta**: c2701336
- **Dominio**: balanza.barloventosrl.website
- **PHP**: 8.2
- **Rutas permitidas**:
  - `/home/c2701336/storagedir`
  - `/home/c2701336`
  - `/tmp`
  - `/home/c2701336/tmpsite`
  - `/opt/php8-2/lib/php`
  - `/opt/ferozo/etc/suspen`
  - `/opt/ferozo/suspended.page`
  - ❌ **FALTA: `/dev`**

---

## ✅ Soluciones Implementadas

### 1. Código Actualizado
- ✅ `BalanzaService.php`: Elimina `file_exists()` y maneja errores de `fopen()`
- ✅ `test-directo.php`: Detecta SO y muestra warnings de open_basedir
- ✅ Mensajes de error más descriptivos con sugerencias

### 2. Documentación Creada
- ✅ `SOLICITUD_HOSTING_FEROZO.md`: Modelo de ticket para soporte

---

## 📋 PASOS A SEGUIR (EN ORDEN)

### Paso 1: Solicitar Modificación a Ferozo ⭐ URGENTE
1. Abrir ticket en: https://panel.ferozo.com
2. Copiar el contenido de `SOLICITUD_HOSTING_FEROZO.md`
3. Enviar solicitud para agregar `/dev` a `open_basedir`

**Tiempo estimado de respuesta**: 24-48 horas hábiles

### Paso 2: Identificar Puerto USB (después de aprobación)
Una vez que Ferozo habilite el acceso:

1. **Conectar el adaptador USB-Serial** al servidor
2. **Pedir a Ferozo que ejecute**: `ls -la /dev/tty*`
3. **Identificar el puerto creado**: Generalmente `/dev/ttyUSB0`

### Paso 3: Configurar Permisos
Pedir a Ferozo que ejecute:
```bash
# Opción 1: Permisos directos
sudo chmod 666 /dev/ttyUSB0

# Opción 2: Agregar usuario al grupo dialout (más seguro)
sudo usermod -a -G dialout c2701336
```

### Paso 4: Configurar .env en Producción
```env
# Linux - Puerto USB
BALANZA_PUERTO=/dev/ttyUSB0
BALANZA_BAUD_RATE=9600
BALANZA_DIVISOR=100
```

Ejecutar:
```bash
php artisan config:cache
```

### Paso 5: Probar Conexión
1. **Test directo**: https://balanza.barloventosrl.website/test-directo.php?puerto=/dev/ttyUSB0
2. **API**: https://balanza.barloventosrl.website/api/balanza/probar-conexion

---

## 🔄 Desarrollo Local (Windows 7)

Mientras esperas respuesta de Ferozo, puedes seguir probando en local:

### Configuración Windows (.env.local)
```env
BALANZA_PUERTO=COM1
BALANZA_BAUD_RATE=9600
BALANZA_DIVISOR=100
```

### Probar en Local
1. Conectar balanza a un puerto COM (verificar en Administrador de Dispositivos)
2. Abrir: http://localhost/balanceControl/public/test-directo.php?puerto=COM1
3. Probar diferentes puertos: COM1, COM2, COM3, COM4

---

## ⚡ Alternativa Rápida: VPS

Si Ferozo rechaza la solicitud o tarda mucho, considera:

### Opción A: VPS de Ferozo
- **Costo**: ~$500-800 ARS/mes
- **Ventaja**: Control total del servidor
- **Link**: https://ferozo.com/vps

### Opción B: Otros proveedores VPS
- **DigitalOcean**: Desde USD $4/mes
- **Vultr**: Desde USD $2.50/mes  
- **Contabo**: Desde EUR €3.99/mes

Con VPS tendrías:
- ✅ Sin restricciones open_basedir
- ✅ Acceso root completo
- ✅ Configuración libre de PHP
- ✅ Instalación de software adicional

---

## 🧪 Testing Checklist

### En Producción (Linux)
- [ ] Ticket enviado a Ferozo
- [ ] Ferozo habilitó `/dev` en open_basedir
- [ ] Puerto USB identificado (`/dev/ttyUSBX`)
- [ ] Permisos configurados
- [ ] `.env` actualizado
- [ ] Caché limpiada (`php artisan config:cache`)
- [ ] Test directo exitoso
- [ ] API funcionando

### En Desarrollo (Windows)
- [ ] Puerto COM identificado
- [ ] Balanza conectada
- [ ] Test directo exitoso
- [ ] Integración con Filament funcionando

---

## 📞 Contactos de Soporte

### Ferozo
- **Panel**: https://panel.ferozo.com
- **Email**: soporte@ferozo.com
- **Teléfono**: 0810-333-7696
- **Chat**: Disponible en panel

### Fabricante Balanza (si es necesario)
- Consultar manual EL05B
- Verificar configuración de transmisión automática
- Confirmar baud rate y formato e105

---

## 📝 Notas Importantes

1. **No elimines archivos anteriores**: Toda la documentación previa (DOCUMENTACION_BALANZA.md, etc.) sigue siendo válida
2. **El código funciona**: El problema es solo de permisos del servidor
3. **Es solucionable**: Ferozo probablemente aprobará la solicitud (es común en apps industriales)
4. **Paciencia**: El soporte puede tardar 1-2 días hábiles

---

## 🎯 Estado Actual

```
┌─────────────────────────────────────┐
│  BLOQUEADO POR HOSTING              │
│  ⏳ Esperando respuesta de Ferozo   │
│  📧 Enviar ticket AHORA             │
└─────────────────────────────────────┘
```

**Próxima acción**: Copiar SOLICITUD_HOSTING_FEROZO.md y abrir ticket en Ferozo.
