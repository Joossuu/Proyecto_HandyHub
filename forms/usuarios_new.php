<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Crear Usuario - HandyHub</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'partials/nav.php'; ?>
<main class="main">
  <div class="card" style="max-width:800px;margin:20px auto;">
    <h4>Crear nuevo usuario</h4>
    <form id="createUserForm">
      <div class="mb-2"><label>Usuario (login)</label><input id="u_login" class="form-control" required></div>
      <div class="mb-2"><label>Nombre completo</label><input id="u_name" class="form-control"></div>
      <div class="mb-2"><label>Rol</label>
        <select id="u_role" class="form-control">
          <option value="1">Administrador</option>
          <option value="2">Supervisor</option>
          <option value="3">Técnico</option>
          <option value="4">Usuario</option>
        </select>
      </div>
      <div class="mb-2"><label>Departamento</label><input id="u_dept" class="form-control"></div>
      <div class="mb-2"><label>Contraseña</label><input id="u_pass" type="password" class="form-control" required></div>
      <div style="display:flex;gap:10px"><button class="btn btn-primary">Crear</button><a href="usuarios.php" class="btn btn-ghost">Volver</a></div>
    </form>
    <div id="msg" style="margin-top:10px;color:var(--muted)"></div>
  </div>
</main>

<script src="assets/js/fetch-helpers.js"></script>
<script>
document.getElementById('createUserForm').addEventListener('submit', async function(e){
  e.preventDefault();
  const payload = {
    Usuario_Login: document.getElementById('u_login').value.trim(),
    Nombre_Rol: '', // opcional
    ID_Rol: parseInt(document.getElementById('u_role').value),
    Departamento: document.getElementById('u_dept').value.trim(),
    password: document.getElementById('u_pass').value
  };
  try {
    const res = await fetch('api/usuarios_api.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(payload),
      credentials: 'include'
    });
    const j = await res.json();
    if (j.error) { document.getElementById('msg').innerText = j.error; return; }
    document.getElementById('msg').innerText = 'Usuario creado correctamente.';
    // opcional: cerrar la pestaña después de 1s
    setTimeout(()=>{ window.close(); }, 1000);
  } catch(err){ document.getElementById('msg').innerText = 'Error al crear.'; console.error(err); }
});
</script>
</body>
</html>
