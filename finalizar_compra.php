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

// Si el carrito está vacío, regresar al inicio
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header("Location: index.php");
    exit();
}

$error = "";

// Procesar el formulario cuando el usuario confirma la compra
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_cliente = trim($_POST['nombre_cliente'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $notas = trim($_POST['notas'] ?? '');
    $metodo_pago = $_POST['metodo_pago'] ?? 'Contra Entrega';

    if (empty($nombre_cliente) || empty($telefono) || empty($direccion) || empty($ciudad)) {
        $error = "Por favor, completa todos los campos obligatorios de envío.";
    } else {
        // Calcular total general
        $total_general = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $stmt = $pdo->prepare("SELECT precio FROM productos WHERE id_producto = ?");
            $stmt->execute([$item['id_producto']]);
            $prod = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($prod) {
                $total_general += $prod['precio'] * $item['cantidad'];
            }
        }

        try {
            $pdo->beginTransaction();

            // 1. Insertar el pedido principal
            $sql_pedido = "INSERT INTO pedidos (nombre_cliente, telefono, direccion, ciudad, notas, total, metodo_pago, estado_pedido, fecha_pedido) VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente', NOW())";
            $stmt_ped = $pdo->prepare($sql_pedido);
            $stmt_ped->execute([$nombre_cliente, $telefono, $direccion, $ciudad, $notas, $total_general, $metodo_pago]);
            $id_pedido_nuevo = $pdo->lastInsertId();

            // 2. Insertar los detalles de los productos en detalle_pedidos (si existe la tabla)
            foreach ($_SESSION['carrito'] as $item) {
                $stmt_p = $pdo->prepare("SELECT precio FROM productos WHERE id_producto = ?");
                $stmt_p->execute([$item['id_producto']]);
                $p_info = $stmt_p->fetch(PDO::FETCH_ASSOC);
                $precio_unitario = $p_info ? $p_info['precio'] : 0;

                try {
                    $sql_det = "INSERT INTO detalle_pedidos (id_pedido, id_producto, cantidad, precio_unitario, talla) VALUES (?, ?, ?, ?, ?)";
                    $stmt_det = $pdo->prepare($sql_det);
                    $stmt_det->execute([$id_pedido_nuevo, $item['id_producto'], $item['cantidad'], $precio_unitario, $item['talla']]);
                } catch (Exception $exDetalle) {
                    // Evita romper la compra si hay diferencias menores en columnas
                }
            }

            $pdo->commit();

            // Vaciar el carrito y guardar ID para la página de éxito
            unset($_SESSION['carrito']);
            $_SESSION['pedido_exitoso'] = $id_pedido_nuevo;

            header("Location: exito.php");
            exit();

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Hubo un error al procesar tu pedido: " . $e->getMessage();
        }
    }
}

// Cargar datos para el resumen visual
$carrito_productos = [];
$total_general = 0;
$cantidad_total_articulos = 0;

foreach ($_SESSION['carrito'] as $clave => $item) {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id_producto = ?");
    $stmt->execute([$item['id_producto']]);
    $prod = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($prod) {
        $prod['talla'] = $item['talla'];
        $prod['cantidad'] = $item['cantidad'];
        $prod['subtotal'] = $prod['precio'] * $item['cantidad'];
        $total_general += $prod['subtotal'];
        $cantidad_total_articulos += $item['cantidad'];
        $carrito_productos[] = $prod;
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
    <title>Moon Essence - Finalizar Compra</title>
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
        .card-cart {
            background-color: var(--bg-tarjeta);
            border: 1px solid #28374d;
            border-radius: 14px;
        }
        .form-control-moon, .form-select-moon {
            background-color: #121a24 !important;
            border: 1px solid #28374d !important;
            color: #ffffff !important;
            border-radius: 8px;
            padding: 10px 14px;
        }
        .form-control-moon:focus, .form-select-moon:focus {
            border-color: var(--accent-luna) !important;
            box-shadow: 0 0 8px rgba(249, 232, 208, 0.2);
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
        .img-miniatura {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #28374d;
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
        <div class="d-flex align-items-center gap-2">
            <a href="checkout.php" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left me-1"></i> Volver al Carrito</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <h2 class="brand-text mb-4"><i class="bi bi-truck me-2"></i>Detalles de Envío y Pago</h2>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger bg-danger text-white border-0 mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
        </div>
    <?php endif; ?>

    <form action="finalizar_compra.php" method="POST">
        <div class="row g-4">
            <!-- Columna Izquierda: Formulario de Dirección -->
            <div class="col-lg-7">
                <div class="card card-cart p-4">
                    <h4 class="text-white mb-3">Información de Destino</h4>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted-moon">Nombre Completo *</label>
                        <input type="text" name="nombre_cliente" class="form-control form-control-moon" required placeholder="Ej. Valentina Gómez">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted-moon">Teléfono / Celular *</label>
                            <input type="text" name="telefono" class="form-control form-control-moon" required placeholder="Ej. 3001234567">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted-moon">Ciudad *</label>
                            <input type="text" name="ciudad" class="form-control form-control-moon" required placeholder="Ej. Bogotá">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted-moon">Dirección Exacta *</label>
                        <input type="text" name="direccion" class="form-control form-control-moon" required placeholder="Ej. Calle 100 # 15-20, Apto 402">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted-moon">Notas o Indicaciones (Opcional)</label>
                        <textarea name="notas" class="form-control form-control-moon" rows="2" placeholder="Ej. Tocar el timbre o dejar en portería"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted-moon">Método de Pago</label>
                        <select name="metodo_pago" class="form-select form-select-moon">
                            <option value="Contra Entrega">Pago Contra Entrega</option>
                            <option value="Nequi / Daviplata">Transferencia (Nequi / Daviplata)</option>
                            <option value="Tarjeta de Crédito">Tarjeta de Crédito / Débito</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Resumen Rápido -->
            <div class="col-lg-5">
                <div class="card card-cart p-4">
                    <h4 class="text-white mb-3">Resumen del Pedido (<?= $cantidad_total_articulos ?> prendas)</h4>
                    
                    <div class="d-flex flex-column gap-3 mb-3" style="max-height: 250px; overflow-y: auto;">
                        <?php foreach ($carrito_productos as $item): ?>
                            <?php $img_fin = limpiarRutaCheckout($item['imagen'] ?? ''); ?>
                            <div class="d-flex align-items-center justify-content-between border-bottom border-secondary pb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <?php if (!empty($img_fin)): ?>
                                        <img src="<?= htmlspecialchars($img_fin) ?>" class="img-miniatura" alt="">
                                    <?php else: ?>
                                        <div class="img-miniatura bg-dark d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image"></i></div>
                                    <?php endif; ?>
                                    <div>
                                        <h6 class="text-white mb-0 fs-6"><?= htmlspecialchars($item['nombre_producto']) ?></h6>
                                        <small class="text-muted-moon">Talla: <?= $item['talla'] ?> | Cant: <?= $item['cantidad'] ?></small>
                                    </div>
                                </div>
                                <span class="text-success fw-bold">$<?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-flex justify-content-between text-muted-moon mb-2">
                        <span>Subtotal</span>
                        <span>$<?= number_format($total_general, 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between text-muted-moon mb-3">
                        <span>Envío Estelar</span>
                        <span class="text-success fw-semibold">Gratis</span>
                    </div>
                    <hr class="border-secondary">
                    <div class="d-flex justify-content-between text-white fw-bold fs-5 mb-4">
                        <span>Total a Pagar</span>
                        <span class="text-success">$<?= number_format($total_general, 0, ',', '.') ?></span>
                    </div>

                    <button type="submit" class="btn btn-moon w-100 py-3 fw-bold fs-5">
                        <i class="bi bi-check-circle-fill me-2"></i> Confirmar y Realizar Pedido
                    </button>
                </div>
            </div>
        </div>
    </form>
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