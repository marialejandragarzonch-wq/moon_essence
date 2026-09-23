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

// Procesar actualización de cantidades si envían el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_cantidades'])) {
    if (isset($_POST['cantidades']) && is_array($_POST['cantidades'])) {
        foreach ($_POST['cantidades'] as $clave => $nueva_cant) {
            $nueva_cant = intval($nueva_cant);
            if ($nueva_cant > 0 && isset($_SESSION['carrito'][$clave])) {
                $_SESSION['carrito'][$clave]['cantidad'] = $nueva_cant;
            } else if ($nueva_cant <= 0 && isset($_SESSION['carrito'][$clave])) {
                unset($_SESSION['carrito'][$clave]);
            }
        }
    }
    header("Location: checkout.php");
    exit();
}

try {
    $stmt_cat = $pdo->query("SELECT * FROM categorias ORDER BY id_categoria ASC");
    $categorias_menu = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categorias_menu = [];
}

$carrito_productos = [];
$total_general = 0;
$cantidad_total_articulos = 0;

if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $clave => $item) {
        $id_prod = $item['id_producto'];
        $talla = $item['talla'];
        $cantidad = $item['cantidad'];

        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id_producto = ?");
        $stmt->execute([$id_prod]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($prod) {
            $prod['talla'] = $talla;
            $prod['cantidad'] = $cantidad;
            $prod['subtotal'] = $prod['precio'] * $cantidad;
            $prod['clave_carrito'] = $clave;
            $total_general += $prod['subtotal'];
            $cantidad_total_articulos += $cantidad;
            $carrito_productos[] = $prod;
        }
    }
}

function limpiarRutaCheckout($img) {
    $img = trim($img ?? '');
    if (empty($img)) return '';
    if (strpos($img, 'uploads/') === 0 || strpos($img, 'http') === 0) {
        return $img;
    }
    return 'uploads/' . $img;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moon Essence - Carrito de Compras</title>
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
        }
        .nav-link-moon {
            color: var(--texto-suave) !important;
        }
        .nav-link-moon:hover {
            color: var(--accent-luna) !important;
        }
        .dropdown-menu-moon {
            background-color: var(--bg-tarjeta);
            border: 1px solid #28374d;
            border-radius: 8px;
        }
        .dropdown-item-moon {
            color: var(--texto-suave);
            padding: 8px 16px;
        }
        .dropdown-item-moon:hover {
            background-color: rgba(249, 232, 208, 0.1);
            color: var(--accent-luna);
        }
        .card-cart {
            background-color: var(--bg-tarjeta);
            border: 1px solid #28374d;
            border-radius: 14px;
        }
        .table-moon {
            color: #ffffff;
            vertical-align: middle;
            background-color: var(--bg-tarjeta) !important;
        }
        .table-moon th {
            border-color: #28374d !important;
            color: var(--texto-suave) !important;
            background-color: #121a24 !important;
        }
        .table-moon td {
            border-color: #28374d !important;
            background-color: var(--bg-tarjeta) !important;
            color: #ffffff !important;
        }
        .table-moon tr {
            background-color: var(--bg-tarjeta) !important;
        }
        .img-carrito {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #28374d;
        }
        .form-control-cantidad {
            background-color: #121a24 !important;
            border: 1px solid #28374d !important;
            color: #ffffff !important;
            width: 70px;
            text-align: center;
            border-radius: 6px;
        }
        .btn-moon {
            background-color: var(--accent-luna);
            color: #0b131e;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .btn-moon:hover {
            background-color: #ffffff;
            box-shadow: 0 0 12px rgba(249, 232, 208, 0.4);
        }
        .btn-outline-moon {
            border: 1px solid var(--accent-luna);
            color: var(--accent-luna);
            border-radius: 8px;
        }
        .btn-outline-moon:hover {
            background-color: var(--accent-luna);
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
        <a class="navbar-brand fw-bold fs-3 brand-text" href="index.php">
            <i class="bi bi-moon-stars-fill me-2"></i>Moon Essence
        </a>
        
        <button class="navbar-toggler border-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMoonContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMoonContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                <li class="nav-item">
                    <a class="nav-link nav-link-moon fw-semibold" href="index.php"><i class="bi bi-house me-1"></i> Inicio</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <a href="checkout.php" class="btn btn-moon px-3 ms-1"><i class="bi bi-cart me-1"></i> Carrito (<?= $cantidad_total_articulos ?>)</a>
            </div>
        </div>
    </div>
</nav>

<div class="container my-5">
    <h2 class="brand-text mb-4"><i class="bi bi-bag-check me-2"></i>Tu Carrito de Compras</h2>
    
    <?php if (empty($carrito_productos)): ?>
        <div class="row">
            <div class="col-12">
                <div class="card card-cart p-4 text-center py-5">
                    <i class="bi bi-cart-x display-3 text-muted-moon mb-3"></i>
                    <h4 class="text-white">Tu carrito está vacío</h4>
                    <p class="text-muted-moon">Aún no has agregado ninguna prenda de Moon Essence.</p>
                    <a href="index.php" class="btn btn-moon mt-3 w-25 mx-auto">Explorar Catálogo</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <form action="checkout.php" method="POST">
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card card-cart p-4">
                        <div class="table-responsive">
                            <table class="table table-moon align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Prenda</th>
                                        <th>Talla</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($carrito_productos as $item): ?>
                                        <?php $foto_item = limpiarRutaCheckout($item['imagen'] ?? ''); ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <?php if (!empty($foto_item)): ?>
                                                        <img src="<?= htmlspecialchars($foto_item) ?>" class="img-carrito" alt="<?= htmlspecialchars($item['nombre_producto']) ?>">
                                                    <?php else: ?>
                                                        <div class="img-carrito d-flex align-items-center justify-content-center bg-dark text-muted">
                                                            <i class="bi bi-image"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <h6 class="text-white fw-bold mb-0"><?= htmlspecialchars($item['nombre_producto']) ?></h6>
                                                        <small class="text-muted-moon">Ref: <?= $item['id_producto'] ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary px-2 py-1"><?= htmlspecialchars($item['talla']) ?></span>
                                            </td>
                                            <td>$<?= number_format($item['precio'], 0, ',', '.') ?></td>
                                            <td>
                                                <input type="number" name="cantidades[<?= $item['clave_carrito'] ?>]" value="<?= $item['cantidad'] ?>" min="1" max="99" class="form-control form-control-cantidad">
                                            </td>
                                            <td class="fw-bold text-success">$<?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                            <td>
                                                <a href="eliminar_carrito.php?clave=<?= urlencode($item['clave_carrito']) ?>" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 text-end">
                            <button type="submit" name="actualizar_cantidades" class="btn btn-outline-moon btn-sm">
                                <i class="bi bi-arrow-clockwise me-1"></i> Actualizar Cantidades
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card card-cart p-4">
                        <h5 class="text-white mb-3">Resumen del Pedido</h5>
                        <div class="d-flex justify-content-between text-muted-moon mb-2">
                            <span>Artículos totales</span>
                            <span><?= $cantidad_total_articulos ?></span>
                        </div>
                        <div class="d-flex justify-content-between text-muted-moon mb-2">
                            <span>Subtotal</span>
                            <span>$<?= number_format($total_general, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between text-muted-moon mb-3">
                            <span>Envío</span>
                            <span>Gratis</span>
                        </div>
                        <hr class="border-secondary">
                        <div class="d-flex justify-content-between text-white fw-bold fs-5 mb-4">
                            <span>Total</span>
                            <span class="text-success">$<?= number_format($total_general, 0, ',', '.') ?></span>
                        </div>
                        <a href="finalizar_compra.php" class="btn btn-moon w-100 py-2">Proceder al Pago</a>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<footer class="py-4">
    <div class="container text-center">
        <p class="brand-text fw-bold mb-1 fs-5">Moon Essence</p>
        <p class="small m-0">© 2026 Todos los derechos reservados.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>