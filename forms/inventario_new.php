<?php
session_start(); if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Nueva herramienta</title><link rel="stylesheet" href="assets/css/bootstrap.min.css"><link rel="stylesheet" href="assets/css/style.css"></head><body>
<?php include 'partials/nav.php'; ?>
<main class="main">
  <div class="card" style="max-width:800px;margin:20px auto;">
    <h4>Registrar herramienta</h4>
    <form id="toolCreateForm">
      <div class="mb-2"><label>Nombre</label><input id="t_nombre" class="form-control" required></div>
      <div class="mb-2"><label>Descripción</label><textarea id="t_desc" class="form-control"></textarea></div>
      <div class="mb-2"><label>Estado</label><select id="t_estado" class="form-control"><option>Disponible</option><option>Prestada</option><option>En reparación</option><option>Dañada</option></select></div>
      <div class="mb-2"><label>Ubicación</label><input id="t_ubic" class="form-control"></div>
      <div style="display:flex;gap:10px"><button class="btn btn-primary">Guardar</button><a href="inventario.php" class="btn btn-ghost">Volver</a></div>
    </form>
    <div id="msg" style="margin-top:10px;color:var(--muted)"></div>
  </div>
</main>
<script src="assets/js/fetch-helpers.js"></script>
<script>
document.getElementById('toolCreateForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const payload = {
    nombre: document.getElementById('t_nombre').value.trim(),
    descripcion: document.getElementById('t_desc').value.trim(),
    estado: document.getElementById('t_estado').value,
    ubicacion: document.getElementById('t_ubic').value.trim()
  };
  try {
    const res = await fetch('api/inventario_api.php', {method:'POST', headers:{'Content-Type':'application/json'}, credentials:'include', body: JSON.stringify(payload)});
    const j = await res.json();
    if (j.error) { document.getElementById('msg').innerText = j.error; return; }
    document.getElementById('msg').innerText = 'Herramienta registrada.';
    setTimeout(()=>{ window.close(); }, 900);
  } catch(err){ document.getElementById('msg').innerText = 'Error'; console.error(err); }
});
</script>
</body></html>
