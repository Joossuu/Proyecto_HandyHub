<?php
require_once __DIR__ . '/../config/session_check.php';
require_once __DIR__ . '/../config/db_config.php';

// Métricas reales
$usuarios = $mysqli->query("SELECT COUNT(*) AS total FROM Usuario WHERE Estado = 1")->fetch_assoc()['total'];
$herramientas = $mysqli->query("SELECT COUNT(*) AS total FROM Herramienta WHERE Estado = 'Disponible'")->fetch_assoc()['total'];
$mantenimientos = $mysqli->query("SELECT COUNT(*) AS total FROM Mantenimiento WHERE Fecha_Finalizacion IS NULL")->fetch_assoc()['total'];
$prestamos = $mysqli->query("SELECT COUNT(*) AS total FROM Prestamo WHERE Estado = 'Activo'")->fetch_assoc()['total'];

// Acciones recientes
$acciones = $mysqli->query("SELECT u.Usuario_Login, b.Accion, b.Fecha
                            FROM Bitacora b
                            LEFT JOIN Usuario u ON b.ID_Usuario = u.ID_Usuario
                            ORDER BY b.Fecha DESC
                            LIMIT 5");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - HandyHub</title>
  <link rel="stylesheet" href="../assets/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <?php include '../partials/sidebar.php'; ?>

  <div class="main-content" id="mainContent">
    <h2>¡Bienvenido, <?= $_SESSION['usuario'] ?> 👋</h2>
    <p>Este es tu panel principal. Aquí puedes ver el estado general del sistema HandyHub.</p>

    <div class="dashboard-cards">
      <div class="card">
        <i class="fas fa-users"></i>
        <h3>Usuarios activos</h3>
        <p><?= $usuarios ?></p>
      </div>
      <div class="card">
        <i class="fas fa-toolbox"></i>
        <h3>Herramientas disponibles</h3>
        <p><?= $herramientas ?></p>
      </div>
      <div class="card">
        <i class="fas fa-wrench"></i>
        <h3>Mantenimientos en curso</h3>
        <p><?= $mantenimientos ?></p>
      </div>
      <div class="card">
        <i class="fas fa-handshake"></i>
        <h3>Préstamos activos</h3>
        <p><?= $prestamos ?></p>
      </div>
    </div>

    <div class="acciones">
      <h3>Últimas acciones registradas</h3>
      <ul>
        <?php while ($row = $acciones->fetch_assoc()): ?>
          <li>
            <span><?= htmlspecialchars($row['Usuario_Login']) ?></span> — <?= htmlspecialchars($row['Accion']) ?> <br>
            <small><?= date('d/m/Y H:i', strtotime($row['Fecha'])) ?></small>
          </li>
        <?php endwhile; ?>
      </ul>
    </div>
  </div>

  <?php include '../partials/footer.php'; ?>
</body>
</html>
