<?php
require_once __DIR__ . '/../config/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Recibir datos del formulario
  $nombre = trim($_POST['nombre']);
  $correo = trim($_POST['correo']);
  $departamento = intval($_POST['departamento']);
  $rol = intval($_POST['rol']);

  if ($nombre && $correo && $departamento && $rol) {
    // Verificar si ya existe el usuario
    $check = $mysqli->prepare("SELECT ID_Usuario, Estado FROM Usuario WHERE Usuario_Login = ?");
    $check->bind_param("s", $nombre);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
      $check->bind_result($idExistente, $estado);
      $check->fetch();
      $check->close();

      if ($estado == 1) {
        // Usuario activo → error de duplicado
        header("Location: ../views/usuarios.php?error=duplicado");
        exit;
      } else {
        // Usuario inactivo → reactivar y actualizar
        $stmt = $mysqli->prepare("UPDATE Usuario SET ID_Rol = ?, ID_Departamento = ?, Estado = 1, Fecha_Creacion = NOW() WHERE ID_Usuario = ?");
        $stmt->bind_param("iii", $rol, $departamento, $idExistente);
        $stmt->execute();
        $stmt->close();

        header("Location: ../views/usuarios.php?exito=1");
        exit;
      }
    } else {
      $check->close();

      // Insertar nuevo usuario
      $stmt = $mysqli->prepare("INSERT INTO Usuario (Usuario_Login, ID_Rol, ID_Departamento, Estado, Fecha_Creacion) VALUES (?, ?, ?, 1, NOW())");
      $stmt->bind_param("sii", $nombre, $rol, $departamento);
      $stmt->execute();
      $stmt->close();

      header("Location: ../views/usuarios.php?exito=1");
      exit;
    }
  } else {
    header("Location: ../views/usuarios.php?error=1");
    exit;
  }
}
?>
