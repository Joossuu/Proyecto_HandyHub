<?php
session_start();
require_once __DIR__ . '/config/db_config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $usuario = $_POST['Usuario_Login'];
  $clave = $_POST['Contrasena'];

  $stmt = $mysqli->prepare("SELECT u.ID_Usuario, u.Usuario_Login, r.Nombre_Rol, c.Password_Hash
                            FROM Usuario u
                            JOIN Credencial c ON u.ID_Usuario = c.ID_Usuario
                            LEFT JOIN Rol r ON u.ID_Rol = r.ID_Rol
                            WHERE u.Usuario_Login = ?");
  $stmt->bind_param("s", $usuario);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $stmt->bind_result($id_usuario, $login, $rol, $hash);
    $stmt->fetch();

    if (password_verify($clave, $hash)) {
      $_SESSION['usuario'] = $login;
      $_SESSION['id_usuario'] = $id_usuario;
      $_SESSION['rol'] = $rol;

      // Registrar en bitácora
      $accion = "Inicio de sesión";
      $fecha = date('Y-m-d H:i:s');
      $bitacora = $mysqli->prepare("INSERT INTO Bitacora (ID_Usuario, Accion, Fecha) VALUES (?, ?, ?)");
      $bitacora->bind_param("iss", $id_usuario, $accion, $fecha);
      $bitacora->execute();
      $bitacora->close();

      header("Location: views/dashboard.php");
      exit;
    } else {
      $error = "Credenciales inválidas";
    }
  } else {
    $error = "Credenciales inválidas";
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login - HandyHub</title>
  <link rel="stylesheet" href="/Proyecto_HandyHub/assets/login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-page">

<body>
  <div class="container">
    <div class="login-box">
      <img src="images.jpg" alt="Logo">
      <h2>Login</h2>

      <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="input-group">
          <i class="fas fa-user"></i>
          <input type="text" name="Usuario_Login" placeholder="Usuario" required>
        </div>

        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="Contrasena" placeholder="Contraseña" required>
        </div>

        <div class="checkbox-group">
          <input type="checkbox" id="remember">
          <label for="remember">Recordarme</label>
        </div>

        <button type="submit">LOGIN</button>

        <div class="links">
          <p><a href="#">¿Olvidaste tu contraseña?</a></p>
          <p>¿No tienes una cuenta? <a href="#">¡Regístrate!</a></p>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
