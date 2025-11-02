<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: /HandyHub/index.php");
  exit;
}
?>
