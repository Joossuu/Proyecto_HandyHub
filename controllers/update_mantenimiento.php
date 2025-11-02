<?php
require_once __DIR__ . '/../config/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = intval($_POST['id']);
  $tipo = trim($_POST['tipo']);
  $inicio = $_POST['inicio'];
  $final = isset($_POST['final']) && $_POST['final'] !== '' ? $_POST['final'] : null;
  $observaciones = trim($_POST['observaciones']);

  if ($id && $tipo && $inicio) {
    $stmt = $mysqli->prepare("
      UPDATE Mantenimiento
      SET Tipo_Mantenimiento = ?, Fecha_Inicio = ?, Fecha_Finalizacion = ?, Observaciones = ?
      WHERE ID_Mantenimiento = ?
    ");
    $stmt->bind_param("ssssi", $tipo, $inicio, $final, $observaciones, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: ../views/mantenimientos.php?actualizado=1");
    exit;
  } else {
    header("Location: ../views/mantenimientos.php?error=1");
    exit;
  }
}
?>
