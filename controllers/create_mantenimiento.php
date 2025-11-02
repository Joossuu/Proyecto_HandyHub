<?php
require_once __DIR__ . '/../config/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_herramienta = intval($_POST['id_herramienta']);
  $id_tecnico = $_POST['id_tecnico'] !== '' ? intval($_POST['id_tecnico']) : null;
  $tipo = trim($_POST['tipo']);
  $inicio = $_POST['inicio'];
  $observaciones = trim($_POST['observaciones']);

  if ($id_herramienta && $tipo && $inicio) {
    $stmt = $mysqli->prepare("
      INSERT INTO Mantenimiento (ID_Herramienta, ID_Tecnico, Tipo_Mantenimiento, Fecha_Inicio, Observaciones)
      VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issss", $id_herramienta, $id_tecnico, $tipo, $inicio, $observaciones);
    $stmt->execute();
    $stmt->close();

    header("Location: ../views/mantenimientos.php?exito=1");
    exit;
  } else {
    header("Location: ../views/mantenimientos.php?error=1");
    exit;
  }
}
?>
