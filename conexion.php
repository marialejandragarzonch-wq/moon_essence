<?php
// Conexión tradicional MySQLi basada en los datos de PDO
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'moon_essence';

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Error al conectar con la base de datos: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");