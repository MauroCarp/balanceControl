<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lectura de Peso</title>
  <style>
    body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; margin: 2rem; }
    .row { display: flex; gap: .75rem; align-items: center; margin-bottom: 1rem; }
    button { padding: .6rem 1rem; font-size: 1rem; cursor: pointer; }
    input { padding: .5rem; font-size: 1rem; width: 10rem; }
    pre { background: #0f172a; color: #e2e8f0; padding: 1rem; border-radius: .5rem; overflow: auto; }
    .muted { color: #6b7280; }
  </style>
</head>
<body>
  <h1>Lectura de Peso</h1>

  <div class="row">
    <label for="puerto">Puerto:</label>
    <input id="puerto" value="COM1" />
    <button id="btnLeer">Leer peso</button>
    <span id="status" class="muted"></span>
  </div>

  <pre id="salida" aria-live="polite">Presioná "Leer peso" para consultar…</pre>

  <script>
    const btn = document.getElementById('btnLeer');
    const out = document.getElementById('salida');
    const statusEl = document.getElementById('status');
    const puertoEl = document.getElementById('puerto');

    async function leer() {
      const puerto = encodeURIComponent(puertoEl.value || 'COM1');
      const url = `/peso/leer?puerto=${puerto}`;
      btn.disabled = true;
      statusEl.textContent = 'Consultando…';
      out.textContent = '';

      try {
        const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
        const text = await resp.text();
        let data;
        try { data = JSON.parse(text); } catch { data = { raw: text }; }
        out.textContent = JSON.stringify(data, null, 2);
        statusEl.textContent = resp.ok ? 'OK' : `Error HTTP ${resp.status}`;
      } catch (e) {
        statusEl.textContent = 'Error de red';
        out.textContent = JSON.stringify({ ok: false, error: e?.message || String(e) }, null, 2);
      } finally {
        btn.disabled = false;
      }
    }

    btn.addEventListener('click', leer);
  </script>
</body>
</html>
