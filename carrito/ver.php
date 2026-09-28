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

// Actualizar cantidad desde el carrito
if (isset($_POST['actualizar_cantidad'])) {
    $index = $_POST['index_producto'];
    $nueva_cant = (int)$_POST['cantidad'];
    
    if (isset($_SESSION['carrito'][$index])) {
        if ($nueva_cant > 0) {
            $_SESSION['carrito'][$index]['cantidad'] = $nueva_cant;
        } else {
            unset($_SESSION['carrito'][$index]);
            $_SESSION['carrito'] = array_values($_SESSION['carrito']);
        }
    }
    header("Location: ver.php");
    exit;
}

// Eliminar producto del carrito
if (isset($_GET['eliminar'])) {
    $index_eliminar = $_GET['eliminar'];
    if (isset($_SESSION['carrito'][$index_eliminar])) {
        unset($_SESSION['carrito'][$index_eliminar]);
        $_SESSION['carrito'] = array_values($_SESSION['carrito']);
    }
    header("Location: ver.php");
    exit;
}

// Vaciar carrito
if (isset($_GET['vaciar'])) {
    unset($_SESSION['carrito']);
    header("Location: ver.php");
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
            width: 85px;
            height: 105px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #384a66;
        }

        .badge-talla {
            font-size: 0.9rem;
            padding: 5px 10px;
            background-color: #2a3a52 !important;
            color: #ffffff;
            border: 1px solid #485c7b;
            text-transform: uppercase;
            border-radius: 6px;
        }

        .precio-texto {
            color: #ffffff !important;
            font-weight: 600;
        }

        .subtotal-texto {
            color: #2ecc71 !important;
            font-weight: 700;
        }

        .cant-control {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #111a28;
            border: 1px solid #384a66;
            border-radius: 8px;
            overflow: hidden;
            width: 110px;
            margin: 0 auto;
        }

        .cant-btn {
            background: transparent;
            border: none;
            color: var(--accent-luna);
            font-weight: bold;
            width: 32px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
        }

        .cant-btn:hover {
            background-color: #233044;
        }

        .cant-input {
            width: 44px;
            background: transparent;
            border: none;
            color: #ffffff;
            text-align: center;
            font-weight: bold;
            font-size: 1rem;
            -moz-appearance: textfield;
        }

        .cant-input::-webkit-outer-spin-button,
        .cant-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
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
        <a class="navbar-brand fw-bold fs-3 brand-text" href="../index.php">
            <i class="bi bi-moon-stars-fill me-2"></i>Moon Essence
        </a>
        <div class="d-flex align-items-center gap-3">
            <a href="../index.php" class="btn btn-outline-moon"><i class="bi bi-house me-1"></i> Inicio</a>
            <span class="btn btn-moon disabled"><i class="bi bi-cart me-1"></i> Carrito (<?= $total_articulos ?>)</span>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="brand-text fw-bold mb-0"><i class="bi bi-bag-check me-2"></i>Tu Carrito de Compras</h2>
        <?php if (!empty($carrito)): ?>
            <a href="ver.php?vaciar=1" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash me-1"></i> Limpiar Carrito
            </a>
        <?php endif; ?>
    </div>

    <?php if (empty($carrito)): ?>
        <div class="card card-product p-5 text-center">
            <i class="bi bi-cart-x fs-1 text-muted-moon mb-3"></i>
            <h4 class="text-white">Tu carrito está vacío</h4>
            <p class="text-muted-moon fs-5">Agrega algunas prendas a tu colección.</p>
            <div class="mt-3">
                <a href="../index.php" class="btn btn-moon btn-lg">Ir a Comprar</a>
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
                                    <th scope="col" style="width: 38%;">Prenda</th>
                                    <th scope="col" class="text-center">Talla</th>
                                    <th scope="col" class="text-center">Precio</th>
                                    <th scope="col" class="text-center">Cantidad</th>
                                    <th scope="col" class="text-center">Subtotal</th>
                                    <th scope="col" class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($carrito as $idx => $item): 
                                    $nombre_prod = $item['nombre_producto'] ?? $item['nombre'] ?? '';
                                    
                                    // Identificar referencia exacta desde la sesión
                                    $ref_real = $item['id_producto'] ?? $item['id'] ?? $item['referencia'] ?? $item['ref'] ?? null;
                                    $desc_real = $item['descripcion'] ?? '';

                                    // Consultar la base de datos para obtener los datos específicos del producto
                                    try {
                                        if ($ref_real) {
                                            $stmt = $pdo->prepare("SELECT id, descripcion FROM productos WHERE id = ?");
                                            $stmt->execute([$ref_real]);
                                        } else {
                                            $stmt = $pdo->prepare("SELECT id, descripcion FROM productos WHERE nombre_producto = ? OR nombre = ? LIMIT 1");
                                            $stmt->execute([$nombre_prod, $nombre_prod]);
                                        }
                                        
                                        $p_db = $stmt->fetch(PDO::FETCH_ASSOC);
                                        if ($p_db) {
                                            $ref_real = $p_db['id'];
                                            if (!empty($p_db['descripcion'])) {
                                                $desc_real = trim($p_db['descripcion']);
                                            }
                                        }
                                    } catch (Exception $e) {}

                                    // Lógica de Imagen
                                    $img_db = trim($item['imagen'] ?? '');
                                    $ruta_img = "";
                                    preg_match('/^(\d+)/', $img_db, $matches);
                                    if (!empty($matches[1])) {
                                        $prefijo = $matches[1];
                                        if (is_dir('../uploads')) {
                                            $archivos = scandir('../uploads');
                                            foreach ($archivos as $archivo) {
                                                if ($archivo === '.' || $archivo === '..') continue;
                                                if (strpos($archivo, $prefijo) === 0) {
                                                    if (strpos($archivo, '_1') !== false || empty($ruta_img)) {
                                                        $ruta_img = "../uploads/" . $archivo;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if (empty($ruta_img) && !empty($img_db) && file_exists("../uploads/" . $img_db)) {
                                        $ruta_img = "../uploads/" . $img_db;
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
                                                    
                                                    <!-- REFERENCIA REAL -->
                                                    <?php if ($ref_real): ?>
                                                        <div class="text-muted-moon small mb-1">
                                                            Ref: <?= htmlspecialchars($ref_real) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <!-- DESCRIPCIÓN REAL DEL PRODUCTO (Solo si existe en la BD) -->
                                                    <?php if (!empty($desc_real)): ?>
                                                        <div class="text-muted-moon small" style="font-size: 0.85rem; max-width: 220px; line-height: 1.2;">
                                                            <?= htmlspecialchars($desc_real) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center"><span class="badge badge-talla"><?= htmlspecialchars($item['talla'] ?? 'M') ?></span></td>
                                        <td class="text-center precio-texto">$<?= number_format($item['precio'] ?? 0, 0, ',', '.') ?></td>
                                        
                                        <td class="text-center">
                                            <form method="POST" action="ver.php" class="d-inline">
                                                <input type="hidden" name="actualizar_cantidad" value="1">
                                                <input type="hidden" name="index_producto" value="<?= $idx ?>">
                                                
                                                <div class="cant-control">
                                                    <button type="button" class="cant-btn" onclick="cambiarCant(this, -1)">-</button>
                                                    <input type="number" name="cantidad" value="<?= $item['cantidad'] ?? 1 ?>" min="1" max="99" class="cant-input" onchange="this.form.submit()">
                                                    <button type="button" class="cant-btn" onclick="cambiarCant(this, 1)">+</button>
                                                </div>
                                            </form>
                                        </td>

                                        <td class="text-center subtotal-texto">$<?= number_format($subtotal, 0, ',', '.') ?></td>
                                        <td class="text-center">
                                            <a href="ver.php?eliminar=<?= $idx ?>" class="btn btn-outline-danger btn-sm p-2 px-3" title="Eliminar del carrito">
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
                    
                    <a href="../finalizar_compra.php" class="btn btn-moon w-100 py-3 fs-5 fw-bold shadow">
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

<script>
function cambiarCant(btn, delta) {
    const form = btn.closest('form');
    const input = form.querySelector('.cant-input');
    let val = parseInt(input.value) || 1;
    val += delta;
    if (val < 1) val = 1;
    input.value = val;
    form.submit();
}
</script>

</body>
</html>