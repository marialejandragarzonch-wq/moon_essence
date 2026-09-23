<?php
session_start();
$host = 'localhost';
$db   = 'moon_essence';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$id_producto = isset($_POST['id_producto']) ? intval($_POST['id_producto']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);
$talla = isset($_POST['talla']) ? $_POST['talla'] : 'Única';
$cantidad_agregada = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;
if ($cantidad_agregada < 1) $cantidad_agregada = 1;

if ($id_producto > 0) {
    $stmt = $pdo->prepare("SELECT id_producto, nombre_producto FROM productos WHERE id_producto = ?");
    $stmt->execute([$id_producto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $clave_carrito = $id_producto . '_' . $talla;

        if (isset($_SESSION['carrito'][$clave_carrito])) {
            $_SESSION['carrito'][$clave_carrito]['cantidad'] += $cantidad_agregada;
        } else {
            $_SESSION['carrito'][$clave_carrito] = [
                'id_producto' => $id_producto,
                'talla' => $talla,
                'cantidad' => $cantidad_agregada
            ];
        }

        $_SESSION['mensaje_exito'] = "¡Prenda añadida al carrito con éxito! (Talla: $talla, Cantidad: $cantidad_agregada)";
    }
}

header("Location: index.php?agregado=1");
exit();
?>