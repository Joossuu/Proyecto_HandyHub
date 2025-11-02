<?php
require_once __DIR__ . '/../config/db_config.php';

$usuario = $_SESSION['usuario'] ?? 'Invitado';
$rol = 'Sin rol';

if (isset($_SESSION['id_usuario'])) {
  $stmt = $mysqli->prepare("SELECT r.Nombre_Rol FROM Usuario u
                            LEFT JOIN Rol r ON u.ID_Rol = r.ID_Rol
                            WHERE u.ID_Usuario = ?");
  $stmt->bind_param("i", $_SESSION['id_usuario']);
  $stmt->execute();
  $stmt->bind_result($nombre_rol);
  if ($stmt->fetch()) {
    $rol = $nombre_rol;
  }
  $stmt->close();
}
?>

<div class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
    <div class="logo">🔧 HandyHub</div>
  </div>

  <ul class="nav-links">
    <li><a href="/Proyecto_HandyHub/views/dashboard.php" data-label="Dashboard"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
    <li><a href="/Proyecto_HandyHub/views/usuarios.php" data-label="Usuarios"><i class="fas fa-users"></i><span>Usuarios</span></a></li>
    <li><a href="/Proyecto_HandyHub/views/roles.php" data-label="Roles"><i class="fas fa-user-tag"></i><span>Roles</span></a></li>
    <li><a href="/Proyecto_HandyHub/views/herramientas.php" data-label="Herramientas"><i class="fas fa-toolbox"></i><span>Herramientas</span></a></li>
    <li><a href="/Proyecto_HandyHub/views/mantenimientos.php" data-label="Mantenimientos"><i class="fas fa-wrench"></i><span>Mantenimientos</span></a></li>
    <li><a href="/Proyecto_HandyHub/views/prestamos.php" data-label="Préstamos"><i class="fas fa-handshake"></i><span>Préstamos</span></a></li>
    <li><a href="/Proyecto_HandyHub/views/bitacora.php" data-label="Bitácora"><i class="fas fa-clipboard-list"></i><span>Bitácora</span></a></li>
    <li><a href="/Proyecto_HandyHub/config/logout.php" title="Cerrar sesión" data-label="Salir"><i class="fas fa-sign-out-alt"></i><span>Salir</span></a></li>
  </ul>

  <div class="sidebar-user">
    <div class="user-icon">👤</div>
    <div class="user-info">
      <div class="user-name"><?= htmlspecialchars($usuario) ?></div>
      <div class="user-role"><?= htmlspecialchars($rol) ?></div>
    </div>
  </div>
</div>

<script>
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const main = document.getElementById('mainContent');
  const footer = document.querySelector('footer');
  sidebar.classList.toggle('collapsed');
  if (main) main.classList.toggle('collapsed');
  if (footer) footer.classList.toggle('collapsed');
}

document.querySelectorAll('.nav-links a').forEach(link => {
  link.addEventListener('mousemove', e => {
    document.documentElement.style.setProperty('--mouse-y', `${e.clientY}px`);
  });
});
</script>
