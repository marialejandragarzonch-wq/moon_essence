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
    die("Error de conexión a la base de datos.");
}

$carrito = $_SESSION['carrito'] ?? [];
$total = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - Moon Essence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --bg-cielo: #0b131e;
            --bg-tarjeta: #1a2332;
            --accent-luna: #f9e8d0;
            --texto-suave: #aebbc9;
        }
        body {
            background-color: var(--bg-cielo);
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar-moon {
            background-color: rgba(5, 9, 14, 0.95);
            border-bottom: 1px solid #233044;
            backdrop-filter: blur(8px);
        }
        .brand-text {
            color: var(--accent-luna) !important;
            letter-spacing: 1.5px;
            text-decoration: none;
        }
        .card-carrito {
            background-color: var(--bg-tarjeta);
            border: 1px solid #28374d;
            border-radius: 14px;
        }
        .img-carrito {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
        }
        .btn-beigecito {
            background-color: var(--accent-luna);
            color: #0b131e;
            border: none;
            font-weight: 600;
        }
        .btn-beigecito:hover {
            background-color: #ffffff;
            color: #0b131e;
        }
        .text-muted-moon {
            color: var(--texto-suave) !important;
        }
        footer {
            background-color: #05090e;
            border-top: 1px solid #233044;
            color: var(--texto-suave);
            margin-top: auto;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-moon sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3 brand-text" href="../index.php">
            <i class="bi bi-moon-stars-fill me-2"></i>Moon Essence
        </a>
        <a href="../index.php" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left me-1"></i> Seguir Comprando</a>
    </div>
</nav>

<div class="container my-5">
    <h2 class="brand-text fw-bold mb-4"><i class="bi bi-cart-fill me-2"></i> Tu Carrito de Compras</h2>

    <?php if (empty($carrito)): ?>
        <div class="card card-carrito p-5 text-center">
            <i class="bi bi-cart-x fs-1 text-muted-moon mb-3"></i>
            <h4 class="text-white">Tu carrito está vacío</h4>
            <p class="text-muted-moon">Aún no has agregado prendas o calzado.</p>
            <div class="mt-3">
                <a href="../index.php" class="btn btn-beigecito">Volver a la Tienda</a>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-carrito p-4 mb-4">
                    <div class="table-responsive">
                        <table class="table table-dark align-middle mb-0" style="background: transparent;">
                            <thead>
                                <tr class="text-muted-moon border-secondary">
                                    <th>Producto</th>
                                    <th>Talla</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($carrito as $item): 
                                    $subtotal = $item['precio'] * $item['cantidad'];
                                    $total += $subtotal;
                                ?>
                                    <tr class="border-secondary">
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <?php if (!empty($item['imagen'])): ?>
                                                    <img src="../uploads/<?= htmlspecialchars($item['imagen']) ?>" alt="Img" class="img-carrito">
                                                <?php else: ?>
                                                    <div class="img-carrito bg-secondary d-flex align-items-center justify-content-center small text-dark">Sin foto</div>
                                                <?php endif; ?>
                                                <span class="fw-bold text-white"><?= htmlspecialchars($item['nombre']) ?></span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($item['talla']) ?></span></td>
                                        <td>$<?= number_format($item['precio'], 2, ',', '.') ?></td>
                                        <td><?= $item['cantidad'] ?></td>
                                        <td class="text-success fw-bold">$<?= number_format($subtotal, 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-carrito p-4">
                    <h4 class="brand-text fw-bold mb-3">Resumen de Compra</h4>
                    <div class="d-flex justify-content-between mb-3 text-muted-moon">
                        <span>Total a Pagar:</span>
                        <span class="text-success fw-bold fs-4">$<?= number_format($total, 2, ',', '.') ?></span>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-beigecito py-2" onclick="alert('¡Compra realizada con éxito! Gracias por elegir Moon Essence.');">
                            <i class="bi bi-bag-check-fill me-2"></i> Finalizar Compra
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<footer class="py-4 mt-auto">
    <div class="container text-center">
        <p class="brand-text fw-bold mb-1 fs-5">Moon Essence</p>
        <p class="small m-0">© 2026 Todos los derechos reservados.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>