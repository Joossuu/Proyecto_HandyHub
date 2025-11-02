<?php
require_once __DIR__ . '/../config/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = intval($_POST['id']);
  $nombre = trim($_POST['nombre']);
  $correo = trim($_POST['correo']);
  $departamento = intval($_POST['departamento']);
  $rol = intval($_POST['rol']);

  if ($id && $nombre && $correo && $departamento && $rol) {
    $stmt = $mysqli->prepare("UPDATE Usuario SET Usuario_Login = ?, ID_Rol = ?, ID_Departamento = ? WHERE ID_Usuario = ?");
    $stmt->bind_param("siii", $nombre, $rol, $departamento, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: ../views/usuarios.php?actualizado=1");
    exit;
  } else {
    header("Location: ../views/usuarios.php?error=1");
    exit;
  }
}
?>
