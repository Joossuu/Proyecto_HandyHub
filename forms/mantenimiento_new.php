<?php
session_start(); if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Registrar mantenimiento</title><link rel="stylesheet" href="assets/css/bootstrap.min.css"><link rel="stylesheet" href="assets/css/style.css"></head><body>
<?php include 'partials/nav.php'; ?>
<main class="main">
  <div class="card" style="max-width:900px;margin:20px auto;">
    <h4>Registrar mantenimiento</h4>
    <form id="mForm">
      <div class="row">
        <div class="col-md-6 mb-2"><label>Herramienta</label><select id="mTool" class="form-control"></select></div>
        <div class="col-md-6 mb-2"><label>Técnico</label><select id="mTec" class="form-control"></select></div>
      </div>
      <div class="mb-2"><label>Tipo</label><select id="mTipo" class="form-control"><option>Preventivo</option><option>Correctivo</option></select></div>
      <div class="mb-2"><label>Observaciones</label><textarea id="mObs" class="form-control"></textarea></div>
      <div style="display:flex;gap:10px"><button class="btn btn-primary">Registrar</button><a href="mantenimiento.php" class="btn btn-ghost">Volver</a></div>
    </form>
    <div id="msg" style="margin-top:10px;color:var(--muted)"></div>
  </div>
</main>
<script src="assets/js/fetch-helpers.js"></script>
<script>
async function init(){
  const users = await (await fetch('api/usuarios_api.php', {credentials:'include'})).json();
  document.getElementById('mTec').innerHTML = users.map(u=>`<option value="${u.ID_Usuario}">${u.Usuario_Login}</option>`).join('');
  const tools = await (await fetch('api/inventario_api.php', {credentials:'include'})).json();
  document.getElementById('mTool').innerHTML = tools.map(t=>`<option value="${t.ID_Herramienta}">${t.Nombre}</option>`).join('');
}
document.getElementById('mForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const payload = { id_herramienta: parseInt(document.getElementById('mTool').value), id_tecnico: parseInt(document.getElementById('mTec').value), tipo: document.getElementById('mTipo').value, observaciones: document.getElementById('mObs').value, fecha_inicio: new Date().toISOString().slice(0,19).replace('T',' ') };
  try {
    const r = await fetch('api/mantenimiento_api.php', {method:'POST', headers:{'Content-Type':'application/json'}, credentials:'include', body: JSON.stringify(payload)});
    const j = await r.json();
    if (j.error) { document.getElementById('msg').innerText = j.error; return; }
    document.getElementById('msg').innerText = 'Mantenimiento registrado.';
    setTimeout(()=>{ window.close(); },900);
  } catch(e){ document.getElementById('msg').innerText='Error'; console.error(e); }
});
init();
</script>
</body></html>
