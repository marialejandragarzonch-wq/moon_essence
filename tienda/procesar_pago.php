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

if (empty($_SESSION['carrito'])) {
    header('Location: index.php');
    exit;
}

// Calcular el total
$ids = array_keys($_SESSION['carrito']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$stmt = $pdo->prepare("SELECT id_producto, precio FROM productos WHERE id_producto IN ($placeholders)");
$stmt->execute($ids);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($productos as $producto) {
    $id = $producto['id_producto'];
    $total += $producto['precio'] * $_SESSION['carrito'][$id];
}

// Insertar en la tabla pedidos sin la columna fecha_creacion
$stmt = $pdo->prepare("INSERT INTO pedidos (id_usuario, total) VALUES (1, ?)");
$stmt->execute([$total]);
$pedido_id = $pdo->lastInsertId();

// Guardar detalles del pedido
foreach ($productos as $producto) {
    $id = $producto['id_producto'];
    $cantidad = $_SESSION['carrito'][$id];
    $precio = $producto['precio'];

    $stmt_detalle = $pdo->prepare("INSERT INTO detalle_pedidos (id_pedido, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
    $stmt_detalle->execute([$pedido_id, $id, $cantidad, $precio]);
}

// Vaciar el carrito tras completarse el registro
unset($_SESSION['carrito']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Pedido Confirmado!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<div class="card text-center p-4 shadow" style="max-width: 450px;">
    <div class="card-body">
        <h1 class="text-success mb-3">¡Compra Exitosa!</h1>
        <p class="fs-5">Tu número de pedido es: <strong>#<?= $pedido_id ?></strong></p>
        <p class="text-muted">Hemos procesado tu solicitud correctamente.</p>
        <a href="index.php" class="btn btn-danger btn-lg mt-3">Volver al Catálogo</a>
    </div>
</div>

</body>
</html>