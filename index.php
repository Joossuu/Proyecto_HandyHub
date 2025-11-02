<?php
session_start();
if (isset($_SESSION['user_id'])) {
  header("Location: dashboard.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>HandyHub - Iniciar Sesión</title>
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body {
      background: radial-gradient(circle at top left, #1e293b, #0f172a);
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .login-container {
      width: 360px;
      background: rgba(30, 41, 59, 0.9);
      padding: 40px 30px;
      border-radius: 15px;
      box-shadow: 0 4px 25px rgba(0,0,0,0.4);
      color: white;
    }
    .login-container img {
      width: 80px;
      display: block;
      margin: 0 auto 15px;
      border-radius: 50%;
      background: rgba(255,255,255,0.1);
      padding: 10px;
    }
    .form-control {
      background: rgba(255,255,255,0.1);
      border: none;
      color: white;
    }
    .form-control::placeholder {
      color: rgba(255,255,255,0.5);
    }
    .btn-login {
      width: 100%;
      background: #0ea5e9;
      color: white;
      border: none;
      transition: all 0.3s;
    }
    .btn-login:hover {
      background: #0284c7;
    }
    .small-links {
      display: flex;
      justify-content: space-between;
      font-size: 0.9em;
    }
  </style>
</head>
<body>
  <div class="login-container text-center">
    <img src="assets/img/images.jpg" alt="Logo">
    <h3 class="mb-4">Iniciar Sesión</h3>
    <div id="alert" class="alert alert-danger d-none"></div>
    <form id="loginForm">
      <div class="mb-3">
        <input type="text" class="form-control" id="username" placeholder="Usuario" required>
      </div>
      <div class="mb-3">
        <input type="password" class="form-control" id="password" placeholder="Contraseña" required>
      </div>
      <button type="submit" class="btn btn-login py-2">LOGIN</button>
    </form>
  </div>

  <script src="assets/js/fetch-helpers.js"></script>
  <script>
  document.getElementById('loginForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const alertEl = document.getElementById('alert');
    alertEl.classList.add('d-none');
    const user = document.getElementById('username').value.trim();
    const pass = document.getElementById('password').value.trim();
    try {
      const res = await fetch('api/login.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        credentials: 'include',
        body: JSON.stringify({username: user, password: pass})
      });
      const text = await res.text();
      console.log(text);
      const data = JSON.parse(text);
      if (data.success) {
        window.location.href = 'dashboard.php';
      } else {
        alertEl.innerText = data.error || "Credenciales inválidas";
        alertEl.classList.remove('d-none');
      }
    } catch(err) {
      alertEl.innerText = "Error de conexión";
      alertEl.classList.remove('d-none');
      console.error(err);
    }
  });
  </script>
</body>
</html>
