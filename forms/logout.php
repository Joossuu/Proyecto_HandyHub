<?php
// logout.php (UI)
session_start();
require 'api/db_config.php';
if (isset($_SESSION['user_id'])) {
  $uid = intval($_SESSION['user_id']);
  $ip = $_SERVER['REMOTE_ADDR'];
  $mysqli->query("INSERT INTO Bitacora (ID_Usuario, Accion, IP_Origen) VALUES ($uid, 'Cierre de sesión (manual)', '".$mysqli->real_escape_string($ip)."')");
}
session_destroy();
header("Location: index.php");
exit;
