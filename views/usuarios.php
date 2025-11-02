<?php
session_start();
if (!isset($_SESSION['user_id'])) header("Location:index.php");
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Usuarios - HandyHub</title><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="assets/css/bootstrap.min.css"><link rel="stylesheet" href="assets/css/style.css"></head><body>
<?php include 'partials/nav.php'; ?>
<main class="container-fluid p-4">
  <h3>Usuarios</h3>
  
  <div class="panel" id="panel-usuarios">
    <div class="panel-header"><h4>Crear / editar usuario</h4><div class="panel-toggle" id="panel-usuarios-toggle">Abrir ▾</div></div>
    <div class="panel-body">
      <form id="form-usuario">
        <div class="form-grid">
          <div class="form-row"><label>Usuario (login)</label><input id="u_login" required></div>
          <div class="form-row"><label>Nombre</label><input id="u_nombre"></div>
          <div class="form-row"><label>Rol</label><select id="u_rol"></select></div>
          <div class="form-row"><label>Departamento</label><input id="u_depto"></div>
          <div class="form-row"><label>Contraseña</label><input id="u_pass" type="password" placeholder="Dejar vacío para no cambiar"></div>
        </div>
        <div class="form-actions"><button class="btn btn-primary" type="submit">Guardar</button><button type="button" class="btn btn-ghost" onclick="document.getElementById('panel-usuarios').classList.remove('open')">Cerrar</button></div>
      </form>
    </div>
  </div>

  <div class="table-responsive" style="margin-top:18px">
    <table class="table table-dark table-striped" id="usersTbl"><thead><tr><th>ID</th><th>Usuario</th><th>Nombre</th><th>Rol</th><th>Departamento</th><th>Fecha</th><th>Acción</th></tr></thead><tbody></tbody></table>
  </div>
</main>
  </div>
</div>

<script src="assets/js/fetch-helpers.js"></script>
<script>
function loadRoles(){
  const roles = [{ID_Rol:1,Nombre_Rol:'Administrador'},{ID_Rol:2,Nombre_Rol:'Supervisor'},{ID_Rol:3,Nombre_Rol:'Técnico'},{ID_Rol:4,Nombre_Rol:'Usuario'}];
  document.getElementById('u_rol').innerHTML = roles.map(r=>`<option value="${r.ID_Rol}">${r.Nombre_Rol}</option>`).join('');
}
async function loadUsers(){
  const res = await fetch('api/usuarios_api.php',{credentials:'include'});
  if (!res.ok){ if (res.status===401) location='index.php'; return; }
  const data = await res.json();
  const tbody = document.querySelector('#usersTbl tbody'); tbody.innerHTML='';
  data.forEach(u=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${u.ID_Usuario}</td><td>${u.Usuario_Login}</td><td>${u.Nombre_Usuario||''}</td><td>${u.Nombre_Rol||''}</td><td>${u.Departamento||''}</td><td>${u.Fecha_Creacion}</td>
      <td><button class="btn btn-sm btn-secondary edit" data-id="${u.ID_Usuario}">Editar</button> <button class="btn btn-sm btn-danger del" data-id="${u.ID_Usuario}">Eliminar</button></td>`;
    tbody.appendChild(tr);
  });
  document.querySelectorAll('.edit').forEach(b=>b.onclick=()=>editUser(b.dataset.id));
  document.querySelectorAll('.del').forEach(b=>b.onclick=()=>deleteUser(b.dataset.id));
}

async function editUser(id){
  const res = await fetch('api/usuarios_api.php?id='+id,{credentials:'include'});
  if (!res.ok) return alert('Error al obtener usuario');
  const u = await res.json();
  document.getElementById('u_login').value = u.Usuario_Login || '';
  document.getElementById('u_nombre').value = u.Nombre_Usuario || '';
  document.getElementById('u_depto').value = u.Departamento || '';
  document.getElementById('u_rol').value = u.ID_Rol || '';
  document.getElementById('form-usuario').dataset.editId = id;
  document.getElementById('panel-usuarios').classList.add('open');
}
async function deleteUser(id){
  if (!confirm('Eliminar usuario?')) return;
  const res = await fetch('api/usuarios_api.php?id='+id,{method:'DELETE',credentials:'include'});
  if (res.ok) loadUsers(); else alert('Error');
}

document.getElementById('form-usuario').addEventListener('submit', async function(e){
  e.preventDefault();
  const editId = this.dataset.editId;
  const payload = { usuario: document.getElementById('u_login').value.trim(), nombre: document.getElementById('u_nombre').value.trim(), id_rol: parseInt(document.getElementById('u_rol').value), departamento: document.getElementById('u_depto').value.trim(), password: document.getElementById('u_pass').value || undefined };
  if (editId){
    payload.id = parseInt(editId);
    await fetch('api/usuarios_api.php', {method:'PUT', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload), credentials:'include'});
    delete this.dataset.editId;
  } else {
    await fetch('api/usuarios_api.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload), credentials:'include'});
  }
  document.getElementById('panel-usuarios').classList.remove('open');
  loadUsers();
});

loadRoles();
loadUsers();
</script>

<!-- Bootstrap + panel toggle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>document.querySelectorAll('.panel-header').forEach(h=>h.addEventListener('click', ()=>h.closest('.panel').classList.toggle('open')));</script>

  </div> <!-- /.content-area -->
</div>   <!-- /.app-shell -->


</body>
</html>
