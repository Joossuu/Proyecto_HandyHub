<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'handyhubdb';

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
  die('Error de conexión: ' . $mysqli->connect_error);
}
?>
