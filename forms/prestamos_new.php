<?php
session_start(); if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Registrar préstamo</title><link rel="stylesheet" href="assets/css/bootstrap.min.css"><link rel="stylesheet" href="assets/css/style.css"></head><body>
<?php include 'partials/nav.php'; ?>
<main class="main">
  <div class="card" style="max-width:900px;margin:20px auto;">
    <h4>Registrar préstamo</h4>
    <form id="loanCreateForm">
      <div class="row">
        <div class="col-md-6 mb-2"><label>Usuario</label><select id="loanUser" class="form-control"></select></div>
        <div class="col-md-6 mb-2"><label>Herramienta</label><select id="loanTool" class="form-control"></select></div>
      </div>
      <div class="mb-2"><label>Fecha entrega prevista</label><input id="loanDue" type="date" class="form-control"></div>
      <div style="display:flex;gap:10px"><button class="btn btn-primary">Registrar</button><a href="prestamos.php" class="btn btn-ghost">Volver</a></div>
    </form>
    <div id="msg" style="margin-top:10px;color:var(--muted)"></div>
  </div>
</main>

<script src="assets/js/fetch-helpers.js"></script>
<script>
async function fill() {
  const users = await (await fetch('api/usuarios_api.php', {credentials:'include'})).json();
  document.getElementById('loanUser').innerHTML = users.map(u=>`<option value="${u.ID_Usuario}">${u.Usuario_Login}</option>`).join('');
  const tools = await (await fetch('api/inventario_api.php', {credentials:'include'})).json();
  document.getElementById('loanTool').innerHTML = tools.filter(t=>t.Estado==='Disponible').map(t=>`<option value="${t.ID_Herramienta}">${t.Nombre}</option>`).join('');
}
document.getElementById('loanCreateForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const payload = {
    id_usuario: parseInt(document.getElementById('loanUser').value),
    id_herramienta: parseInt(document.getElementById('loanTool').value),
    fecha_prestamo: new Date().toISOString().slice(0,19).replace('T',' '),
    fecha_entrega: document.getElementById('loanDue').value ? (document.getElementById('loanDue').value+' 00:00:00') : null
  };
  try {
    const r = await fetch('api/prestamos_api.php', {method:'POST', headers:{'Content-Type':'application/json'}, credentials:'include', body: JSON.stringify(payload)});
    const j = await r.json();
    if (j.error) { document.getElementById('msg').innerText = j.error; return; }
    document.getElementById('msg').innerText = 'Préstamo registrado.';
    setTimeout(()=>{ window.close(); },900);
  } catch(e){ document.getElementById('msg').innerText='Error'; console.error(e); }
});
fill();
</script>
</body></html>
