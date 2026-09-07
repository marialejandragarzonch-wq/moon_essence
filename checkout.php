<?php
session_start();

// Configuración de conexión a la base de datos
$host = 'localhost';
$db   = 'moon_essence';
$user = 'root';
$pass = ''; // Deja vacío si usas XAMPP por defecto

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Verificar si hay productos en el carrito
if (empty($_SESSION['carrito'])) {
    header('Location: index.php');
    exit;
}

// Obtener los IDs de los productos en el carrito
$ids = array_keys($_SESSION['carrito']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));

// Consulta corregida usando id_producto e id_tienda
$stmt = $pdo->prepare("
    SELECT p.id_producto, p.nombre_producto, p.precio, p.imagen, t.nombre_tienda 
    FROM productos p 
    JOIN tiendas t ON p.id_tienda = t.id_tienda 
    WHERE p.id_producto IN ($placeholders)
");
$stmt->execute($ids);
$productos_carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Moon Essence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold text-danger" href="index.php">Moon Essence</a>
    </div>
</nav>

<div class="container">
    <h2 class="mb-4">Resumen de tu Compra</h2>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos_carrito as $producto): 
                                $id = $producto['id_producto'];
                                $cantidad = $_SESSION['carrito'][$id];
                                $subtotal = $producto['precio'] * $cantidad;
                                $total += $subtotal;
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="uploads/<?= htmlspecialchars($producto['imagen']) ?>" alt="" style="width: 50px; height: 50px; object-fit: cover;" class="me-3 rounded">
                                        <div>
                                            <strong><?= htmlspecialchars($producto['nombre_producto']) ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($producto['nombre_tienda']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= $cantidad ?></td>
                                <td>$<?= number_format($producto['precio'], 2) ?></td>
                                <td>$<?= number_format($subtotal, 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total a Pagar</h5>
                    <h3 class="text-danger fw-bold">$<?= number_format($total, 2) ?></h3>
                    <hr>
                    <form action="procesar_pago.php" method="POST">
                        <button type="submit" class="btn btn-danger w-100 btn-lg">Confirmar Pedido</button>
                    </form>
                    <a href="index.php" class="btn btn-outline-secondary w-100 mt-2">Seguir comprando</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>