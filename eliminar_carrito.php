<?php
session_start();
$clave = $_GET['clave'] ?? '';

if (!empty($clave) && isset($_SESSION['carrito'][$clave])) {
    unset($_SESSION['carrito'][$clave]);
}

header("Location: checkout.php");
exit();
?>