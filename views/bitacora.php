<?php
session_start();
if (!isset($_SESSION['user_id'])) header("Location:index.php");
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Bitácora - HandyHub</title>
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* estilos locales mínimos para la bitácora */
    .log-card { margin:20px; padding:16px; border-radius:8px; background: rgba(255,255,255,0.01); border:1px solid rgba(255,255,255,0.02); }
    .log-item { padding:10px 8px; border-bottom:1px solid rgba(255,255,255,0.02); }
    .log-item strong { display:block; color:#e6eef6; }
    .log-item .meta { font-size:12px; color:rgba(255,255,255,0.45); margin-top:6px; }
    .log-empty { color:rgba(255,255,255,0.5); padding:12px; }
    .log-loading { color:rgba(255,255,255,0.6); padding:12px; }
  </style>
</head>
<body>
<?php include 'partials/nav.php'; ?>

<main class="main">
  <div class="log-card">
    <h4>Bitácora</h4>
    <div id="logControls" style="margin:8px 0;">
      <button id="refreshLogs" class="btn btn-sm btn-secondary">Refrescar</button>
    </div>
    <div id="logList" style="max-height:520px;overflow:auto;"></div>
  </div>
</main>

<script>
/**
 * Carga la bitácora de forma robusta:
 * - maneja responses no JSON
 * - acepta [] o { data: [...] }
 * - muestra mensajes de carga / error / sin registros
 */
async function loadLogs(){
  const listEl = document.getElementById('logList');
  listEl.innerHTML = '<div class="log-loading">Cargando registros...</div>';

  let res;
  try {
    res = await fetch('api/bitacora_api.php', { credentials: 'include' });
  } catch (err) {
    console.error('Fetch error (bitacora):', err);
    listEl.innerHTML = '<div class="log-empty">Error de conexión. Revisa la consola (F12).</div>';
    return;
  }

  if (!res.ok) {
    if (res.status === 401) {
      // no autenticado -> redirigir al login
      location.href = 'index.php';
      return;
    }
    // mostrar texto de error devuelto por el servidor (si hay)
    let txt = '';
    try { txt = await res.text(); } catch(e) { txt = ''; }
    listEl.innerHTML = '<div class="log-empty">Error al cargar registros. HTTP ' + res.status + '</div>';
    console.warn('Bitacora: respuesta no OK:', res.status, txt);
    return;
  }

  // intentar parsear JSON, si no se puede lo dejamos en array vacío
  let payload;
  try {
    payload = await res.json();
  } catch (err) {
    // la respuesta no era JSON (p. ej. HTML de error). Mostrar en consola y mensaje.
    console.error('Bitacora: la respuesta no es JSON:', err);
    const text = await res.text().catch(()=>'');
    console.warn('Respuesta cruda:', text);
    listEl.innerHTML = '<div class="log-empty">La API devolvió una respuesta inesperada. Revisa la consola.</div>';
    return;
  }

  // normalizar: aceptamos [] o { data: [...] } u objects con propiedades comunes
  let list = [];
  if (Array.isArray(payload)) {
    list = payload;
  } else if (payload && Array.isArray(payload.data)) {
    list = payload.data;
  } else if (payload && payload.rows && Array.isArray(payload.rows)) {
    list = payload.rows;
  } else {
    // si API devolvió un objeto simple, intentar inferir un array en alguna propiedad
    const maybeArray = Object.values(payload).find(v => Array.isArray(v));
    if (Array.isArray(maybeArray)) list = maybeArray;
  }

  if (!Array.isArray(list) || list.length === 0) {
    listEl.innerHTML = '<div class="log-empty">Sin registros.</div>';
    return;
  }

  // construir HTML de los elementos
  const html = list.map(r => {
    // intentar mapear varias formas de campos
    const usuario = r.Usuario_Login || r.usuario || r.user || r.nombre || 'Usuario';
    const accion = r.Accion || r.accion || r.Descripcion || r.descripcion || r.evento || 'Evento';
    const fecha = r.Fecha || r.fecha || r.created_at || r.Timestamp || '';
    const detalle = r.Detalle || r.detalle || '';
    return `
      <div class="log-item">
        <strong>${escapeHtml(usuario)} — ${escapeHtml(accion)}</strong>
        ${detalle ? `<div style="color:rgba(255,255,255,0.8);margin-top:6px">${escapeHtml(detalle)}</div>` : ''}
        <div class="meta">${escapeHtml(fecha)}</div>
      </div>
    `;
  }).join('');
  listEl.innerHTML = html;
}

// pequeño helper para escapar HTML por seguridad
function escapeHtml(s){
  if (s === null || s === undefined) return '';
  return String(s).replace(/[&<>"'`=\/]/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#x2F;','`':'&#x60;','=':'&#x3D;'}[c]; });
}

document.getElementById('refreshLogs').addEventListener('click', loadLogs);
loadLogs();
</script>
</body>
</html>
