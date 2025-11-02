<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Préstamos - HandyHub</title><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="assets/css/bootstrap.min.css"><link rel="stylesheet" href="assets/css/style.css"></head><body>
<?php include 'partials/nav.php'; ?>
<main class="container-fluid p-4">
  <h3>Préstamos</h3>
  

  <div class="panel" id="panel-prestamo">
    <div class="panel-header"><h4>Registrar préstamo</h4><div class="panel-toggle" id="panel-prestamo-toggle">Abrir ▾</div></div>
    <div class="panel-body">
      <form id="form-prestamo">
        <div class="form-grid">
          <div class="form-row"><label>Usuario</label><select id="f_usuario" class="form-control"></select></div>
          <div class="form-row"><label>Herramienta</label><select id="f_herramienta" class="form-control"></select></div>
          <div class="form-row"><label>Fecha entrega prevista</label><input type="date" id="f_fecha_entrega" class="form-control"></div>
        </div>
        <div class="form-actions"><button class="btn btn-primary" type="submit">Registrar</button><button type="button" class="btn btn-ghost" onclick="document.getElementById('panel-prestamo').classList.remove('open')">Cerrar</button></div>
      </form>
    </div>
  </div>

  <div class="card p-3 mt-3">
    <h5>Préstamos activos</h5>
    <table class="table table-dark" id="loansTable"><thead><tr><th>ID</th><th>Herramienta</th><th>Usuario</th><th>Fecha</th><th>Entrega</th><th>Estado</th><th>Acción</th></tr></thead><tbody></tbody></table>
  </div>
</main>
  </div>
</div>

<script src="assets/js/fetch-helpers.js"></script>
<script>
async function loadUsersAndTools(){
  let res = await fetch('api/usuarios_api.php', {credentials:'include'});
  const users = await res.json();
  document.getElementById('f_usuario').innerHTML = users.map(u => `<option value="${u.ID_Usuario}">${u.Usuario_Login} (${u.Nombre_Rol||'--'})</option>`).join('');
  res = await fetch('api/inventario_api.php', {credentials:'include'});
  const tools = await res.json();
  document.getElementById('f_herramienta').innerHTML = tools.filter(t=>t.Estado==='Disponible').map(t=>`<option value="${t.ID_Herramienta}">${t.Nombre}</option>`).join('');
}

async function loadLoans(){
  const res = await fetch('api/prestamos_api.php', {credentials:'include'});
  const data = await res.json();
  const tbody = document.querySelector('#loansTable tbody'); tbody.innerHTML='';
  data.forEach(l=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${l.ID_Prestamo}</td><td>${l.herramienta}</td><td>${l.usuario}</td><td>${l.Fecha_Prestamo}</td><td>${l.Fecha_Entrega||''}</td><td>${l.Estado}</td><td>${l.Estado==='Activo'?'<button class=\"btn btn-sm btn-success return\" data-id=\"'+l.ID_Prestamo+'\">Devolver</button>':''}</td>`;
    tbody.appendChild(tr);
  });
  document.querySelectorAll('.return').forEach(b=>b.onclick=async function(){
    const id = this.dataset.id;
    const condicion = prompt('Condición de la herramienta al devolver (Disponible / Dañada):', 'Disponible');
    await fetch('api/prestamos_api.php', {method:'PUT', headers:{'Content-Type':'application/json'}, credentials:'include', body: JSON.stringify({id_prestamo: id, condicion: condicion, observaciones: ''})});
    loadLoans(); loadUsersAndTools();
  });
}

document.getElementById('form-prestamo').addEventListener('submit', async function(e){
  e.preventDefault();
  const payload = {
    id_usuario: parseInt(document.getElementById('f_usuario').value),
    id_herramienta: parseInt(document.getElementById('f_herramienta').value),
    fecha_prestamo: new Date().toISOString().slice(0,19).replace('T',' '),
    fecha_entrega: document.getElementById('f_fecha_entrega').value ? (document.getElementById('f_fecha_entrega').value+' 00:00:00') : null
  };
  await fetch('api/prestamos_api.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload), credentials:'include'});
  document.getElementById('panel-prestamo').classList.remove('open');
  loadLoans(); loadUsersAndTools();
});

loadUsersAndTools();
loadLoans();
</script>

<!-- Bootstrap + panel toggle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>document.querySelectorAll('.panel-header').forEach(h=>h.addEventListener('click', ()=>h.closest('.panel').classList.toggle('open')));</script>

  </div> <!-- /.content-area -->
</div>   <!-- /.app-shell -->


</body>
</html>
