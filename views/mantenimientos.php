<?php
require_once __DIR__ . '/../config/session_check.php';
require_once __DIR__ . '/../config/db_config.php';

$mantenimientos = $mysqli->query("
  SELECT M.ID_Mantenimiento, H.Nombre AS Herramienta, U.Usuario_Login AS Tecnico,
         M.Tipo_Mantenimiento, M.Fecha_Inicio, M.Fecha_Finalizacion, M.Observaciones
  FROM Mantenimiento M
  LEFT JOIN Herramienta H ON M.ID_Herramienta = H.ID_Herramienta
  LEFT JOIN Usuario U ON M.ID_Tecnico = U.ID_Usuario
  ORDER BY M.ID_Mantenimiento DESC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Mantenimientos</title>
  <link rel="stylesheet" href="../assets/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <?php include '../partials/sidebar.php'; ?>

  <div class="main-content">
    <div class="usuarios-header">
      <h2>🧰 Gestión de Mantenimientos</h2>
      <button class="crear-btn" onclick="abrirModal('modalCrear')">+ Registrar mantenimiento</button>
    </div>

    <?php if (isset($_GET['exito'])): ?>
      <div class="alert success">✅ Mantenimiento registrado correctamente</div>
    <?php elseif (isset($_GET['actualizado'])): ?>
      <div class="alert success">✅ Mantenimiento actualizado</div>
    <?php elseif (isset($_GET['eliminado'])): ?>
      <div class="alert success">🗑️ Mantenimiento eliminado</div>
    <?php elseif (isset($_GET['error'])): ?>
      <div class="alert error">❌ Faltan datos o hubo un error</div>
    <?php endif; ?>

    <div class="usuarios-busqueda">
      <input type="text" id="buscador" placeholder="Buscar por herramienta, técnico o tipo...">
    </div>

    <table class="usuarios-tabla" id="tablaMantenimientos">
      <thead>
        <tr>
          <th>Herramienta</th>
          <th>Técnico</th>
          <th>Tipo</th>
          <th>Inicio</th>
          <th>Finalización</th>
          <th>Observaciones</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $mantenimientos->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['Herramienta']) ?></td>
            <td><?= htmlspecialchars($row['Tecnico']) ?></td>
            <td><?= htmlspecialchars($row['Tipo_Mantenimiento']) ?></td>
            <td><?= htmlspecialchars($row['Fecha_Inicio']) ?></td>
            <td><?= htmlspecialchars($row['Fecha_Finalizacion'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['Observaciones']) ?></td>
            <td class="acciones">
              <button title="Editar" onclick="abrirModalEditar(
                <?= $row['ID_Mantenimiento'] ?>,
                '<?= htmlspecialchars($row['Tipo_Mantenimiento'], ENT_QUOTES) ?>',
                '<?= htmlspecialchars($row['Fecha_Inicio'], ENT_QUOTES) ?>',
                '<?= htmlspecialchars($row['Fecha_Finalizacion'], ENT_QUOTES) ?>',
                '<?= htmlspecialchars($row['Observaciones'], ENT_QUOTES) ?>'
              )"><i class="fas fa-edit"></i></button>

              <button title="Eliminar" onclick="abrirModalEliminar(<?= $row['ID_Mantenimiento'] ?>)">
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
      <h3>Registrar mantenimiento</h3>
      <form action="../controllers/create_mantenimiento.php" method="POST">
        <select name="id_herramienta" required>
          <option value="">Seleccionar herramienta</option>
          <?php
          $herramientas = $mysqli->query("SELECT ID_Herramienta, Nombre FROM Herramienta");
          while ($h = $herramientas->fetch_assoc()) {
            echo "<option value='{$h['ID_Herramienta']}'>{$h['Nombre']}</option>";
          }
          ?>
        </select>

        <select name="id_tecnico">
          <option value="">Seleccionar técnico</option>
          <?php
          $tecnicos = $mysqli->query("SELECT ID_Usuario, Usuario_Login FROM Usuario WHERE ID_Rol = 3");
          while ($t = $tecnicos->fetch_assoc()) {
            echo "<option value='{$t['ID_Usuario']}'>{$t['Usuario_Login']}</option>";
          }
          ?>
        </select>

        <input type="text" name="tipo" placeholder="Tipo de mantenimiento" required>
        <input type="datetime-local" name="inicio" required>
        <textarea name="observaciones" placeholder="Observaciones"></textarea>

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
      <h3>Editar mantenimiento</h3>
      <form action="../controllers/update_mantenimiento.php" method="POST">
        <input type="hidden" name="id" id="editId">
        <input type="text" name="tipo" id="editTipo" required>
        <input type="datetime-local" name="inicio" id="editInicio" required>
        <input type="datetime-local" name="final" id="editFinal">
        <textarea name="observaciones" id="editObs"></textarea>

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
      <form action="../controllers/delete_mantenimiento.php" method="POST">
        <input type="hidden" name="id" id="deleteId">
        <h3>¿Eliminar este mantenimiento?</h3>
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
      const filas = document.querySelectorAll('#tablaMantenimientos tbody tr');
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

    function abrirModalEditar(id, tipo, inicio, final, obs) {
      document.getElementById('editId').value = id;
      document.getElementById('editTipo').value = tipo;
      document.getElementById('editInicio').value = inicio.replace(' ', 'T');
      document.getElementById('editFinal').value = final ? final.replace(' ', 'T') : '';
      document.getElementById('editObs').value = obs;
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
