<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Mantenimiento - HandyHub</title><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="assets/css/bootstrap.min.css"><link rel="stylesheet" href="assets/css/style.css"></head><body>
<?php include 'partials/nav.php'; ?>
<main class="container-fluid p-4">
  <h3>Mantenimientos</h3>

  <div class="panel" id="panel-mantenimiento">
    <div class="panel-header"><h4>Registrar mantenimiento</h4><div class="panel-toggle" id="panel-mantenimiento-toggle">Abrir ▾</div></div>
    <div class="panel-body">
      <form id="form-mantenimiento">
        <div class="form-grid">
          <div class="form-row"><label>Herramienta</label><select id="mTool" name="id_herramienta"></select></div>
          <div class="form-row"><label>Técnico</label><select id="mTec" name="id_tecnico"></select></div>
          <div class="form-row"><label>Tipo</label><select id="mTipo" name="tipo"><option>Preventivo</option><option>Correctivo</option></select></div>
          <div class="form-row"><label>Observaciones</label><textarea id="mObs" name="observaciones" rows="3"></textarea></div>
        </div>
        <div class="form-actions"><button class="btn btn-primary">Registrar</button><button type="button" class="btn btn-ghost" onclick="document.getElementById('panel-mantenimiento').classList.remove('open')">Cerrar</button></div>
      </form>
    </div>
  </div>

  <div id="mList" style="margin-top:18px"></div>
</main>
  </div>
</div>

<script src="assets/js/fetch-helpers.js"></script>
<script>
async function loadForM(){
  const users = await (await fetch('api/usuarios_api.php', {credentials:'include'})).json();
  document.getElementById('mTec').innerHTML = users.map(u=>`<option value="${u.ID_Usuario}">${u.Usuario_Login}</option>`).join('');
  const tools = await (await fetch('api/inventario_api.php', {credentials:'include'})).json();
  document.getElementById('mTool').innerHTML = tools.map(t=>`<option value="${t.ID_Herramienta}">${t.Nombre}</option>`).join('');
  loadMList();
}
async function loadMList(){
  const list = await (await fetch('api/mantenimiento_api.php', {credentials:'include'})).json();
  let html = '<table class="table table-dark table-sm"><thead><tr><th>ID</th><th>Herramienta</th><th>Técnico</th><th>Tipo</th><th>Fecha</th></tr></thead><tbody>';
  list.forEach(l=>{ html += `<tr><td>${l.ID_Mantenimiento}</td><td>${l.herramienta||''}</td><td>${l.tecnico||''}</td><td>${l.Tipo_Mantenimiento}</td><td>${l.Fecha_Inicio}</td></tr>`; });
  html += '</tbody></table>';
  document.getElementById('mList').innerHTML = html;
}
document.getElementById('form-mantenimiento').addEventListener('submit', async function(e){
  e.preventDefault();
  const payload = { id_herramienta: parseInt(document.getElementById('mTool').value), id_tecnico: parseInt(document.getElementById('mTec').value), tipo: document.getElementById('mTipo').value, observaciones: document.getElementById('mObs').value, fecha_inicio: new Date().toISOString().slice(0,19).replace('T',' ') };
  await fetch('api/mantenimiento_api.php', {method:'POST', headers:{'Content-Type':'application/json'}, credentials:'include', body: JSON.stringify(payload)});
  document.getElementById('panel-mantenimiento').classList.remove('open');
  loadMList();
});
loadForM();
</script>

<!-- Bootstrap + panel toggle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>document.querySelectorAll('.panel-header').forEach(h=>h.addEventListener('click', ()=>h.closest('.panel').classList.toggle('open')));</script>

  </div> <!-- /.content-area -->
</div>   <!-- /.app-shell -->


</body>
</html>
