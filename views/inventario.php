<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Inventario - HandyHub</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* pequeñas ayudas visuales locales */
    .panel { border-radius:8px; background:transparent; margin-bottom:14px; }
    .panel .panel-header { display:flex; justify-content:space-between; align-items:center; padding:12px 16px; cursor:pointer; border:1px solid rgba(255,255,255,0.02); border-radius:8px; }
    .panel .panel-body { display:none; padding:14px; border-left:1px solid rgba(255,255,255,0.02); border-right:1px solid rgba(255,255,255,0.02); border-bottom:1px solid rgba(255,255,255,0.02); border-radius:0 0 8px 8px; }
    .panel.open .panel-body { display:block; }
    .form-grid { display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
    .form-row { display:flex; flex-direction:column; }
    .form-actions { margin-top:12px; display:flex; gap:8px; justify-content:flex-end; }
    @media (max-width:900px){ .form-grid{ grid-template-columns: 1fr; } }
  </style>
</head>
<body>
<?php include 'partials/nav.php'; ?>

<main class="container-fluid p-4">
  <h3>Inventario</h3>

  <div class="panel" id="panel-inventario">
    <div class="panel-header">
      <h4 style="margin:0;">Registrar herramienta</h4>
      <div class="panel-toggle" id="panel-inventario-toggle">Abrir ▾</div>
    </div>
    <div class="panel-body">
      <form id="form-inventario">
        <div class="form-grid">
          <div class="form-row">
            <label for="f_nombre">Nombre</label>
            <input id="f_nombre" name="nombre" class="form-control" required />
          </div>
          <div class="form-row">
            <label for="f_estado">Estado</label>
            <select id="f_estado" name="estado" class="form-control">
              <option>Disponible</option><option>Prestada</option><option>En reparación</option><option>Dañada</option>
            </select>
          </div>
          <div class="form-row">
            <label for="f_ubic">Ubicación</label>
            <input id="f_ubic" name="ubicacion" class="form-control" />
          </div>
          <div class="form-row">
            <label for="f_categoria">Categoría</label>
            <input id="f_categoria" name="categoria" class="form-control" />
          </div>
          <div class="form-row" style="grid-column:1 / -1;">
            <label for="f_desc">Descripción</label>
            <textarea id="f_desc" name="descripcion" rows="3" class="form-control"></textarea>
          </div>
        </div>
        <div class="form-actions">
          <button class="btn btn-primary" type="submit">Guardar</button>
          <button type="button" class="btn btn-secondary" onclick="closePanel('panel-inventario')">Cerrar</button>
        </div>
      </form>
    </div>
  </div>

  <div class="table-responsive" style="margin-top:18px">
    <table class="table table-dark table-striped" id="toolsTable">
      <thead>
        <tr><th>ID</th><th>Nombre</th><th>Estado</th><th>Ubicación</th><th>Categoría</th><th>Acciones</th></tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>



</main>

  </div> <!-- /.content-area -->
</div>   <!-- /.app-shell -->

<script src="assets/js/fetch-helpers.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>
/* Helpers */
function closePanel(id){
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.remove('open');
  const toggle = document.getElementById(id+'-toggle');
  if (toggle) toggle.innerText = 'Abrir ▾';
}

/* Panel header toggles */
document.addEventListener('click', function(evt){
  const ph = evt.target.closest('.panel-header');
  if (ph){
    const panel = ph.closest('.panel');
    if (!panel) return;
    panel.classList.toggle('open');
    const t = panel.querySelector('.panel-toggle');
    if (t) t.innerText = panel.classList.contains('open') ? 'Cerrar ▴' : 'Abrir ▾';
  }
});

/* Inventory / table logic */
let bsToolModal;
document.addEventListener('DOMContentLoaded', ()=> {
  const modalEl = document.getElementById('toolModal');
  if (modalEl) bsToolModal = new bootstrap.Modal(modalEl);
  loadTools();
});

/* Cargar tabla */
async function loadTools(){
  try {
    const res = await fetch('api/inventario_api.php', { credentials: 'include' });
    if (!res.ok) {
      if (res.status === 401) location.href = 'index.php';
      return;
    }
    const data = await res.json();
    const tbody = document.querySelector('#toolsTable tbody');
    tbody.innerHTML = '';
    data.forEach(t => {
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${t.ID_Herramienta}</td>
        <td>${t.Nombre || ''}</td>
        <td>${t.Estado || ''}</td>
        <td>${t.Ubicacion || ''}</td>
        <td>${t.categoria || ''}</td>
        <td>
          <button class="btn btn-sm btn-secondary edit" data-id="${t.ID_Herramienta}">Editar</button>
          <button class="btn btn-sm btn-danger del" data-id="${t.ID_Herramienta}">Eliminar</button>
        </td>`;
      tbody.appendChild(tr);
    });
    document.querySelectorAll('.edit').forEach(b => b.onclick = ()=> editTool(b.dataset.id));
    document.querySelectorAll('.del').forEach(b => b.onclick = ()=> deleteTool(b.dataset.id));
  } catch (err) {
    console.error('Error loadTools:', err);
  }
}

/* Abrir modal desde botón Nueva herramienta */
document.getElementById('btnNew').addEventListener('click', ()=> showModal(null));

/* Mostrar modal con datos (o vacío) */
function showModal(data = null){
  document.getElementById('toolId').value = data ? data.ID_Herramienta : '';
  document.getElementById('toolNombre').value = data ? data.Nombre : '';
  document.getElementById('toolDesc').value = data ? data.Descripcion : '';
  document.getElementById('toolEstado').value = data ? data.Estado : 'Disponible';
  document.getElementById('toolUbic').value = data ? data.Ubicacion : '';
  document.getElementById('toolCat').value = data ? data.categoria : '';
  if (bsToolModal) bsToolModal.show();
}

/* Edit: buscar item y abrir modal */
async function editTool(id){
  try {
    const res = await fetch('api/inventario_api.php', { credentials: 'include' });
    if (!res.ok) return alert('Error cargando datos');
    const list = await res.json();
    const item = list.find(x => String(x.ID_Herramienta) === String(id));
    if (!item) return alert('Elemento no encontrado');
    showModal(item);
  } catch (err) {
    console.error('editTool error', err);
  }
}

/* Delete */
async function deleteTool(id){
  if (!confirm('Eliminar herramienta?')) return;
  try {
    const res = await fetch('api/inventario_api.php?id='+encodeURIComponent(id), { method: 'DELETE', credentials: 'include' });
    if (res.ok) loadTools(); else alert('Error al eliminar');
  } catch (err) { console.error(err); alert('Error de red'); }
}

/* Guardar modal (crear/editar) */
document.getElementById('toolForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const id = document.getElementById('toolId').value;
  const payload = {
    id: id || undefined,
    nombre: document.getElementById('toolNombre').value.trim(),
    descripcion: document.getElementById('toolDesc').value.trim(),
    estado: document.getElementById('toolEstado').value,
    ubicacion: document.getElementById('toolUbic').value.trim(),
    categoria: document.getElementById('toolCat').value.trim()
  };
  try {
    const method = id ? 'PUT' : 'POST';
    const res = await fetch('api/inventario_api.php', { method, headers: {'Content-Type':'application/json'}, body: JSON.stringify(payload), credentials:'include' });
    if (res.ok) {
      if (bsToolModal) bsToolModal.hide();
      loadTools();
    } else {
      const text = await res.text();
      alert('Error al guardar: ' + (text || res.status));
    }
  } catch (err) { console.error(err); alert('Error de conexión'); }
});

/* Form del panel (alternativa: crear sin modal) */
document.getElementById('form-inventario').addEventListener('submit', async function(e){
  e.preventDefault();
  const payload = {
    nombre: document.getElementById('f_nombre').value.trim(),
    descripcion: document.getElementById('f_desc').value.trim(),
    estado: document.getElementById('f_estado').value,
    ubicacion: document.getElementById('f_ubic').value.trim(),
    categoria: document.getElementById('f_categoria').value.trim()
  };
  try {
    const res = await fetch('api/inventario_api.php', { method: 'POST', headers:{'Content-Type':'application/json'}, credentials:'include', body: JSON.stringify(payload) });
    if (res.ok) { alert('Guardado'); loadTools(); closePanel('panel-inventario'); } else { alert('Error al guardar'); }
  } catch (err) { console.error(err); alert('Error de conexión'); }
});
</script>

</body>
</html>
