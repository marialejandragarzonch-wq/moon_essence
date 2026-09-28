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

// Eliminar producto del carrito
if (isset($_GET['eliminar'])) {
    $index_eliminar = $_GET['eliminar'];
    if (isset($_SESSION['carrito'][$index_eliminar])) {
        unset($_SESSION['carrito'][$index_eliminar]);
        $_SESSION['carrito'] = array_values($_SESSION['carrito']);
    }
    header("Location: checkout.php");
    exit;
}

$carrito = $_SESSION['carrito'] ?? [];
$subtotal_general = 0;
$total_articulos = 0;

foreach ($carrito as $item) {
    $subtotal_general += ($item['precio'] ?? 0) * ($item['cantidad'] ?? 1);
    $total_articulos += ($item['cantidad'] ?? 1);
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

        html, body, .moon-bg {
            background-color: var(--bg-cielo) !important;
            color: #ffffff !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .navbar-moon {
            background-color: #05090e !important;
            border-bottom: 1px solid #233044;
        }

        .brand-text {
            color: var(--accent-luna) !important;
            letter-spacing: 1px;
            text-decoration: none;
        }

        .card-product {
            background-color: var(--bg-tarjeta) !important;
            border: 1px solid #28374d !important;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-moon {
            background-color: var(--accent-luna) !important;
            color: #0b131e !important;
            font-weight: 600;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-moon:hover {
            background-color: #ffffff !important;
            color: #0b131e !important;
            box-shadow: 0 0 12px rgba(249, 232, 208, 0.4);
        }

        .btn-outline-moon {
            border: 1px solid var(--accent-luna) !important;
            color: var(--accent-luna) !important;
            background: transparent;
        }

        .btn-outline-moon:hover {
            background-color: var(--accent-luna) !important;
            color: #0b131e !important;
        }

        .text-muted-moon {
            color: var(--texto-suave) !important;
        }

        /* Estilos ajustados para la tabla del Carrito */
        .table-moon {
            color: #ffffff !important;
            vertical-align: middle;
            font-size: 1.05rem;
        }

        .table-moon th {
            background-color: transparent !important;
            color: #ffffff !important;
            border-bottom: 2px solid #384a66;
            font-weight: 600;
            padding: 16px 12px;
        }

        .table-moon td {
            background-color: transparent !important;
            border-bottom: 1px solid #28374d;
            padding: 18px 12px;
            color: #ffffff !important;
        }

        .img-carrito {
            width: 80px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #384a66;
        }

        .badge-talla {
            font-size: 0.95rem;
            padding: 5px 12px;
            background-color: #2a3a52 !important;
            color: #ffffff;
            border: 1px solid #485c7b;
            text-transform: uppercase;
        }

        .precio-texto {
            color: #ffffff !important;
            font-weight: 600;
        }

        .subtotal-texto {
            color: #2ecc71 !important;
            font-weight: 700;
        }

        .agradecimiento-box {
            border-top: 1px dashed #28374d;
            margin-top: 40px;
            padding-top: 25px;
            text-align: center;
        }

        .agradecimiento-texto {
            color: var(--accent-luna);
            font-style: italic;
            font-size: 1.15rem;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-moon sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3 brand-text" href="index.php">
            <i class="bi bi-moon-stars-fill me-2"></i>Moon Essence
        </a>
        <div class="d-flex align-items-center gap-3">
            <a href="index.php" class="btn btn-outline-moon"><i class="bi bi-house me-1"></i> Inicio</a>
            <span class="btn btn-moon disabled"><i class="bi bi-cart me-1"></i> Carrito (<?= $total_articulos ?>)</span>
        </div>
    </div>
</nav>

<div class="container my-5">
    <h2 class="brand-text mb-4 fw-bold"><i class="bi bi-bag-check me-2"></i>Tu Carrito de Compras</h2>

    <?php if (empty($carrito)): ?>
        <div class="card card-product p-5 text-center">
            <i class="bi bi-cart-x fs-1 text-muted-moon mb-3"></i>
            <h4 class="text-white">Tu carrito está vacío</h4>
            <p class="text-muted-moon fs-5">Agrega algunas prendas a tu colección.</p>
            <div class="mt-3">
                <a href="index.php" class="btn btn-moon btn-lg">Ir a Comprar</a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Tabla de productos -->
            <div class="col-lg-8">
                <div class="card card-product p-4 shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-moon mb-0">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 40%;">Prenda</th>
                                    <th scope="col" class="text-center">Talla</th>
                                    <th scope="col" class="text-center">Precio</th>
                                    <th scope="col" class="text-center">Cantidad</th>
                                    <th scope="col" class="text-center">Subtotal</th>
                                    <th scope="col" class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($carrito as $idx => $item): 
                                    $nombre_prod = $item['nombre_producto'] ?? $item['nombre'] ?? 'Prenda Moon Essence';
                                    $img_db = trim($item['imagen'] ?? '');
                                    $ruta_img = "";
                                    preg_match('/^(\d+)/', $img_db, $matches);
                                    if (!empty($matches[1])) {
                                        $prefijo = $matches[1];
                                        if (is_dir('uploads')) {
                                            $archivos = scandir('uploads');
                                            foreach ($archivos as $archivo) {
                                                if ($archivo === '.' || $archivo === '..') continue;
                                                if (strpos($archivo, $prefijo) === 0) {
                                                    if (strpos($archivo, '_1') !== false || empty($ruta_img)) {
                                                        $ruta_img = "uploads/" . $archivo;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if (empty($ruta_img) && !empty($img_db) && file_exists("uploads/" . $img_db)) {
                                        $ruta_img = "uploads/" . $img_db;
                                    }

                                    $subtotal = ($item['precio'] ?? 0) * ($item['cantidad'] ?? 1);
                                ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <?php if (!empty($ruta_img)): ?>
                                                    <img src="<?= htmlspecialchars($ruta_img) ?>" alt="Imagen" class="img-carrito">
                                                <?php else: ?>
                                                    <div class="img-carrito bg-dark d-flex align-items-center justify-content-center text-muted small">Sin img</div>
                                                <?php endif; ?>
                                                <div>
                                                    <div class="fw-bold text-white fs-5"><?= htmlspecialchars($nombre_prod) ?></div>
                                                    <span class="text-muted-moon fs-6">Ref: <?= htmlspecialchars($item['id_producto'] ?? $item['id'] ?? '12') ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center"><span class="badge badge-talla"><?= htmlspecialchars($item['talla'] ?? 'M') ?></span></td>
                                        <td class="text-center precio-texto">$<?= number_format($item['precio'] ?? 0, 0, ',', '.') ?></td>
                                        <td class="text-center fw-bold fs-5"><?= $item['cantidad'] ?? 1 ?></td>
                                        <td class="text-center subtotal-texto">$<?= number_format($subtotal, 0, ',', '.') ?></td>
                                        <td class="text-center">
                                            <a href="checkout.php?eliminar=<?= $idx ?>" class="btn btn-outline-danger btn-sm p-2 px-3" title="Eliminar del carrito">
                                                <i class="bi bi-trash fs-5"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Resumen del Pedido -->
            <div class="col-lg-4">
                <div class="card card-product p-4 shadow-sm">
                    <h4 class="brand-text fw-bold mb-4">Resumen del Pedido</h4>
                    
                    <div class="d-flex justify-content-between mb-3 fs-5">
                        <span class="text-muted-moon">Artículos totales</span>
                        <span class="fw-bold text-white"><?= $total_articulos ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 fs-5">
                        <span class="text-muted-moon">Subtotal</span>
                        <span class="fw-bold text-white">$<?= number_format($subtotal_general, 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 fs-5">
                        <span class="text-muted-moon">Envío</span>
                        <span class="text-success fw-bold">Gratis</span>
                    </div>
                    
                    <hr class="border-secondary my-4">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold fs-4 text-white">Total</span>
                        <span class="fs-3 fw-bold text-success">$<?= number_format($subtotal_general, 0, ',', '.') ?></span>
                    </div>
                    
                    <a href="procesar_pago.php" class="btn btn-moon w-100 py-3 fs-5 fw-bold shadow">
                        Proceder al Pago <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Frase de Agradecimiento -->
        <div class="agradecimiento-box">
            <p class="agradecimiento-texto mb-1">
                <i class="bi bi-stars me-2"></i>¡Gracias por elegir Moon Essence! Cada prenda está diseñada para resaltar tu estilo único.<i class="bi bi-stars ms-2"></i>
            </p>
            <small class="text-muted-moon">Tus compras son procesadas de forma segura.</small>
        </div>

    <?php endif; ?>
</div>

</body>
</html>