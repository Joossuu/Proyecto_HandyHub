<?php
require_once __DIR__ . '/../config/session_check.php';
require_once __DIR__ . '/../config/db_config.php';

// Obtener rol del usuario actual
$rolUsuario = $_SESSION['rol'] ?? 'Usuario';

// Obtener usuarios
$usuarios = $mysqli->query("SELECT u.ID_Usuario, u.Usuario_Login, u.Fecha_Creacion, u.ID_Rol, u.ID_Departamento,
                                   d.Nombre AS Departamento, r.Nombre_Rol AS Rol
                            FROM Usuario u
                            LEFT JOIN Departamento d ON u.ID_Departamento = d.ID_Departamento
                            LEFT JOIN Rol r ON u.ID_Rol = r.ID_Rol
                            WHERE u.Estado = 1
                            ORDER BY u.Fecha_Creacion DESC");

// Cargar roles y departamentos según el rol del usuario
$rolesRaw = $mysqli->query("SELECT ID_Rol, Nombre_Rol FROM Rol");
$departamentosRaw = $mysqli->query("SELECT ID_Departamento, Nombre FROM Departamento");

$roles = [];
$departamentos = [];

while ($r = $rolesRaw->fetch_assoc()) {
  $roles[] = $r;
}
while ($d = $departamentosRaw->fetch_assoc()) {
  $departamentos[] = $d;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Usuarios - HandyHub</title>
  <link rel="stylesheet" href="../assets/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <?php include '../partials/sidebar.php'; ?>

  <div class="main-content" id="mainContent">
    <div class="usuarios-header">
      <h2>👥 Gestión de Usuarios</h2>
      <button class="crear-btn" onclick="abrirModal('modalCrear')">+ Crear usuario</button>
    </div>

  <?php if (isset($_GET["exito"])) : ?>
    <div class="alert success">✅ Usuario creado correctamente</div>
  <?php elseif (isset($_GET["actualizado"])) : ?>
    <div class="alert success">✅ Usuario actualizado correctamente</div>
  <?php elseif (isset($_GET["eliminado"])) : ?>
    <div class="alert success">✅ Usuario eliminado correctamente</div>
  <?php elseif (isset($_GET["error"])) : ?>
    <?php if ($_GET["error"] == "1") : ?>
      <div class="alert error">❌ Faltan datos o hubo un error</div>
    <?php elseif ($_GET["error"] == "duplicado") : ?>
      <div class="alert error">⚠️ Ya existe un usuario con ese nombre</div>
    <?php elseif ($_GET["error"] == "duplicado_activo") : ?>
      <div class="alert error">⚠️ Ya existe un usuario activo con ese nombre</div>
    <?php endif; ?>
  <?php endif; ?>



    <div class="usuarios-busqueda">
      <input type="text" id="buscador" placeholder="Buscar por nombre, correo o rol...">
    </div>

    <table class="usuarios-tabla" id="tablaUsuarios">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Correo electrónico</th>
          <th>Departamento</th>
          <th>Rol</th>
          <th>Creado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $usuarios->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['Usuario_Login']) ?></td>
            <td><?= htmlspecialchars($row['Usuario_Login']) ?>@<?= strtolower($row['Usuario_Login']) ?>.com</td>
            <td><?= htmlspecialchars($row['Departamento'] ?? 'Sin asignar') ?></td>
            <td><?= htmlspecialchars($row['Rol'] ?? 'Sin rol') ?></td>
            <td><?= date('d/m/Y H:i', strtotime($row['Fecha_Creacion'])) ?></td>
            <td class="acciones">
              <button title="Editar" onclick="abrirModalEditar(
                <?= $row['ID_Usuario'] ?>,
                '<?= $row['Usuario_Login'] ?>',
                '<?= $row['Usuario_Login'] ?>@<?= strtolower($row['Usuario_Login']) ?>.com',
                <?= $row['ID_Departamento'] ?? 1 ?>,
                <?= $row['ID_Rol'] ?? 2 ?>
              )"><i class="fas fa-edit"></i></button>

              <button title="Eliminar" onclick="abrirModalEliminar(<?= $row['ID_Usuario'] ?>)">
                <i class="fas fa-trash-alt"></i>
              </button>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <?php include '../partials/footer.php'; ?>

  <!-- Modal Crear Usuario -->
  <div class="modal-overlay" id="modalCrear">
    <div class="modal">
      <h3>Crear nuevo usuario</h3>
      <form action="../controllers/create_user.php" method="POST">
        <input type="text" name="nombre" placeholder="Nombre de usuario" required>
        <input type="email" name="correo" placeholder="Correo electrónico" required>

        <select name="departamento" required>
          <option value="">Seleccionar departamento</option>
          <?php foreach ($departamentos as $d): ?>
            <option value="<?= $d['ID_Departamento'] ?>"><?= htmlspecialchars($d['Nombre']) ?></option>
          <?php endforeach; ?>
        </select>

        <select name="rol" required>
          <option value="">Seleccionar rol</option>
          <?php foreach ($roles as $r): ?>
            <option value="<?= $r['ID_Rol'] ?>"><?= htmlspecialchars($r['Nombre_Rol']) ?></option>
          <?php endforeach; ?>
        </select>

        <div class="modal-actions">
          <button type="button" class="cancel-btn" onclick="cerrarModal('modalCrear')">Cancelar</button>
          <button type="submit" class="save-btn">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Editar Usuario -->
  <div class="modal-overlay" id="modalEditar">
    <div class="modal">
      <h3>Editar usuario</h3>
      <form action="../controllers/update_user.php" method="POST">
        <input type="hidden" name="id" id="editId">
        <input type="text" name="nombre" id="editNombre" required>
        <input type="email" name="correo" id="editCorreo" required>

        <select name="departamento" id="editDepartamento" required>
          <?php foreach ($departamentos as $d): ?>
            <option value="<?= $d['ID_Departamento'] ?>"><?= htmlspecialchars($d['Nombre']) ?></option>
          <?php endforeach; ?>
        </select>

        <select name="rol" id="editRol" required>
          <?php foreach ($roles as $r): ?>
            <option value="<?= $r['ID_Rol'] ?>"><?= htmlspecialchars($r['Nombre_Rol']) ?></option>
          <?php endforeach; ?>
        </select>

        <div class="modal-actions">
          <button type="button" class="cancel-btn" onclick="cerrarModal('modalEditar')">Cancelar</button>
          <button type="submit" class="save-btn">Actualizar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Eliminar Usuario -->
  <div class="modal-overlay" id="modalEliminar">
    <div class="modal">
      <form action="../controllers/delete_user.php" method="POST">
        <input type="hidden" name="id" id="deleteId">
        <h3>¿Estás seguro de eliminar este usuario?</h3>
        <div class="modal-actions">
          <button type="button" class="cancel-btn" onclick="cerrarModal('modalEliminar')">Cancelar</button>
          <button type="submit" class="delete-btn">Eliminar</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Buscador en tiempo real
    document.getElementById('buscador').addEventListener('input', function () {
      const filtro = this.value.toLowerCase();
      const filas = document.querySelectorAll('#tablaUsuarios tbody tr');
      filas.forEach(fila => {
        const texto = fila.textContent.toLowerCase();
        fila.style.display = texto.includes(filtro) ? '' : 'none';
      });
    });

    // Modales
    function abrirModal(id) {
      document.getElementById(id).style.display = 'flex';
    }

    function cerrarModal(id) {
      document.getElementById(id).style.display = 'none';
    }

    function abrirModalEditar(id, nombre, correo, departamento, rol) {
      document.getElementById('editId').value = id;
      document.getElementById('editNombre').value = nombre;
      document.getElementById('editCorreo').value = correo;
      document.getElementById('editDepartamento').value = departamento;
      document.getElementById('editRol').value
      document.getElementById('editRol').value = rol;
      abrirModal('modalEditar');
    }

    function abrirModalEliminar(id) {
      document.getElementById('deleteId').value = id;
      abrirModal('modalEliminar');
    }
  </script>
<script>
  setTimeout(() => {
    const alerta = document.querySelector('.alert');
    if (alerta) {
      alerta.classList.add('ocultar');
      setTimeout(() => alerta.style.display = 'none', 500); // espera que termine la transición
    }
  }, 4000);
</script>

  
</body>
</html>
