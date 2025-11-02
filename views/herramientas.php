<?php
require_once __DIR__ . '/../config/session_check.php';
require_once __DIR__ . '/../config/db_config.php';

$herramientas = $mysqli->query("SELECT ID_Herramienta, Nombre, Descripcion, Estado, Ubicacion FROM Herramienta ORDER BY ID_Herramienta ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Herramientas</title>
  <link rel="stylesheet" href="../assets/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <?php include '../partials/sidebar.php'; ?>

  <div class="main-content">
    <div class="usuarios-header">
      <h2>🛠️ Gestión de Herramientas</h2>
      <button class="crear-btn" onclick="abrirModal('modalCrear')">+ Agregar herramienta</button>
    </div>

    <?php if (isset($_GET['exito'])): ?>
      <div class="alert success">✅ Herramienta creada correctamente</div>
    <?php elseif (isset($_GET['actualizado'])): ?>
      <div class="alert success">✅ Herramienta actualizada correctamente</div>
    <?php elseif (isset($_GET['eliminado'])): ?>
      <div class="alert success">🗑️ Herramienta eliminada correctamente</div>
    <?php elseif (isset($_GET['error'])): ?>
      <div class="alert error">❌ Faltan datos o hubo un error</div>
    <?php elseif (isset($_GET['error1'])): ?>
      <div class="alert error">⚠️ Ya existe una herramienta con ese nombre</div>
    <?php endif; ?>

    <div class="usuarios-busqueda">
      <input type="text" id="buscador" placeholder="Buscar por nombre o ubicación...">
    </div>

    <table class="usuarios-tabla" id="tablaHerramientas">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Estado</th>
          <th>Ubicación</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $herramientas->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['Nombre']) ?></td>
            <td><?= htmlspecialchars($row['Descripcion']) ?></td>
            <td><?= htmlspecialchars($row['Estado']) ?></td>
            <td><?= htmlspecialchars($row['Ubicacion']) ?></td>
            <td class="acciones">
              <button title="Editar" onclick="abrirModalEditar(
                <?= $row['ID_Herramienta'] ?>,
                '<?= htmlspecialchars($row['Nombre'], ENT_QUOTES) ?>',
                '<?= htmlspecialchars($row['Descripcion'], ENT_QUOTES) ?>',
                '<?= htmlspecialchars($row['Estado'], ENT_QUOTES) ?>',
                '<?= htmlspecialchars($row['Ubicacion'], ENT_QUOTES) ?>'
              )"><i class="fas fa-edit"></i></button>

              <button title="Eliminar" onclick="abrirModalEliminar(<?= $row['ID_Herramienta'] ?>)">
                <i class="fas fa-trash-alt"></i>
              </button>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <?php include '../partials/footer.php'; ?>

  <!-- Modal Crear -->
  <div class="modal-overlay" id="modalCrear">
    <div class="modal">
      <h3>Crear nueva herramienta</h3>
      <form action="../controllers/create_herramienta.php" method="POST">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="text" name="descripcion" placeholder="Descripción">
        <input type="text" name="estado" placeholder="Estado" value="Disponible" required>
        <input type="text" name="ubicacion" placeholder="Ubicación">
        <div class="modal-actions">
          <button type="button" class="cancel-btn" onclick="cerrarModal('modalCrear')">Cancelar</button>
          <button type="submit" class="save-btn">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Editar -->
  <div class="modal-overlay" id="modalEditar">
    <div class="modal">
      <h3>Editar herramienta</h3>
      <form action="../controllers/update_herramienta.php" method="POST">
        <input type="hidden" name="id" id="editId">
        <input type="text" name="nombre" id="editNombre" required>
        <input type="text" name="descripcion" id="editDescripcion">
        <input type="text" name="estado" id="editEstado" required>
        <input type="text" name="ubicacion" id="editUbicacion">
        <div class="modal-actions">
          <button type="button" class="cancel-btn" onclick="cerrarModal('modalEditar')">Cancelar</button>
          <button type="submit" class="save-btn">Actualizar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Eliminar -->
  <div class="modal-overlay" id="modalEliminar">
    <div class="modal">
      <form action="../controllers/delete_herramienta.php" method="POST">
        <input type="hidden" name="id" id="deleteId">
        <h3>¿Estás seguro de eliminar esta herramienta?</h3>
        <div class="modal-actions">
          <button type="button" class="cancel-btn" onclick="cerrarModal('modalEliminar')">Cancelar</button>
          <button type="submit" class="delete-btn">Eliminar</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    document.getElementById('buscador').addEventListener('input', function () {
      const filtro = this.value.toLowerCase();
      const filas = document.querySelectorAll('#tablaHerramientas tbody tr');
      filas.forEach(fila => {
        const texto = fila.textContent.toLowerCase();
        fila.style.display = texto.includes(filtro) ? '' : 'none';
      });
    });

    function abrirModal(id) {
      document.getElementById(id).style.display = 'flex';
    }

    function cerrarModal(id) {
      document.getElementById(id).style.display = 'none';
    }

    function abrirModalEditar(id, nombre, descripcion, estado, ubicacion) {
      document.getElementById('editId').value = id;
      document.getElementById('editNombre').value = nombre;
      document.getElementById('editDescripcion').value = descripcion;
      document.getElementById('editEstado').value = estado;
      document.getElementById('editUbicacion').value = ubicacion;
      abrirModal('modalEditar');
    }

    function abrirModalEliminar(id) {
      document.getElementById('deleteId').value = id;
      abrirModal('modalEliminar');
    }

    setTimeout(() => {
      const alerta = document.querySelector('.alert');
      if (alerta) {
        alerta.classList.add('ocultar');
        setTimeout(() => alerta.style.display = 'none', 500);
      }
    }, 4000);
  </script>
</body>
</html>
