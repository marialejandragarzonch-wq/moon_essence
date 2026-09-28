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

// Si el carrito está vacío, regresar al carrito
if (empty($carrito)) {
    header("Location: carrito/ver.php");
    exit;
}

$subtotal_general = 0;
foreach ($carrito as $item) {
    $subtotal_general += ($item['precio'] ?? 0) * ($item['cantidad'] ?? 1);
}

// Procesar pedido al presionar el botón
$mensaje_exito = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vaciar el carrito tras finalizar la compra
    $_SESSION['carrito'] = [];
    $mensaje_exito = "¡Tu pedido ha sido realizado con éxito!";
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

        .form-control, .form-select {
            background-color: #111a28 !important;
            border: 1px solid #28374d !important;
            color: #ffffff !important;
            padding: 12px 15px;
            border-radius: 8px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent-luna) !important;
            box-shadow: 0 0 8px rgba(249, 232, 208, 0.2);
        }

        .form-control::placeholder {
            color: #5d708a !important;
        }

        .btn-moon {
            background-color: var(--accent-luna) !important;
            color: #0b131e !important;
            font-weight: 700;
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

        .badge-talla {
            font-size: 0.8rem;
            padding: 3px 8px;
            background-color: #2a3a52 !important;
            color: #ffffff;
            border: 1px solid #485c7b;
            text-transform: uppercase;
            border-radius: 4px;
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
        <div class="d-flex align-items-center">
            <a href="carrito/ver.php" class="btn btn-outline-moon btn-sm px-3">
                <i class="bi bi-arrow-left me-1"></i> Volver al Carrito
            </a>
        </div>
    </div>
</nav>

<div class="container my-5" style="max-width: 1100px;">

    <?php if (!empty($mensaje_exito)): ?>
        <div class="card card-product p-5 text-center my-4">
            <i class="bi bi-check-circle-fill fs-1 text-success mb-3"></i>
            <h2 class="brand-text fw-bold mb-3"><?= $mensaje_exito ?></h2>
            <p class="text-muted-moon fs-5">Hemos procesado tu pedido correctamente.</p>
            <div class="mt-4">
                <a href="index.php" class="btn btn-moon btn-lg px-4">Volver al Inicio</a>
            </div>
        </div>
    <?php else: ?>

        <div class="text-center mb-5">
            <h2 class="brand-text fw-bold fs-1">Finalizar Compra</h2>
            <p class="text-muted-moon fs-5">Completa tus datos para el envío de tus prendas</p>
        </div>

        <form method="POST" action="">
            <div class="row g-4">
                <!-- Datos de Envío -->
                <div class="col-lg-7">
                    <div class="card card-product p-4 shadow-sm">
                        <h4 class="text-white fw-bold mb-4">
                            <i class="bi bi-geo-alt me-2 brand-text"></i>Datos de Envío
                        </h4>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-white fw-medium">Nombre completo</label>
                                <input type="text" class="form-control" name="nombre" placeholder="Ej. Ana María" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white fw-medium">Correo Electrónico</label>
                                <input type="email" class="form-control" name="email" placeholder="tu@email.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white fw-medium">Teléfono</label>
                                <input type="text" class="form-control" name="telefono" placeholder="300 000 0000" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white fw-medium">Ciudad</label>
                                <input type="text" class="form-control" name="ciudad" placeholder="Bogotá, Medellín, etc." required>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-white fw-medium">Dirección exacta de entrega</label>
                                <input type="text" class="form-control" name="direccion" placeholder="Carrera 15 # 45 - 20 Apto 301" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-white fw-medium">Método de Pago</label>
                                <select class="form-select" name="metodo_pago" required>
                                    <option value="contraentrega">Pago Contra Entrega (Efectivo)</option>
                                    <option value="transferencia">Transferencia Bancaria / Nequi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumen Final -->
                <div class="col-lg-5">
                    <div class="card card-product p-4 shadow-sm">
                        <h4 class="text-white fw-bold mb-4">
                            <i class="bi bi-receipt me-2 brand-text"></i>Resumen Final
                        </h4>

                        <div class="pe-1 mb-3" style="max-height: 280px; overflow-y: auto;">
                            <?php foreach ($carrito as $item): 
                                $nombre_p = $item['nombre_producto'] ?? $item['nombre'] ?? 'Prenda';
                                $sub = ($item['precio'] ?? 0) * ($item['cantidad'] ?? 1);
                            ?>
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-secondary">
                                    <div>
                                        <div class="fw-bold text-white fs-6"><?= htmlspecialchars($nombre_p) ?></div>
                                        <small class="text-muted-moon">
                                            Talla: <span class="badge badge-talla"><?= htmlspecialchars($item['talla'] ?? 'S') ?></span>
                                            | Cant: <?= $item['cantidad'] ?? 1 ?>
                                        </small>
                                    </div>
                                    <div class="text-success fw-bold fs-6">
                                        $<?= number_format($sub, 0, ',', '.') ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr class="border-secondary my-3">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold fs-4 text-white">Total a Pagar:</span>
                            <span class="fs-3 fw-bold text-success">$<?= number_format($subtotal_general, 0, ',', '.') ?></span>
                        </div>

                        <button type="submit" class="btn btn-moon w-100 py-3 fs-5 fw-bold shadow">
                            <i class="bi bi-check-circle-fill me-2"></i>Confirmar y Realizar Pedido
                        </button>
                    </div>
                </div>
            </div>
        </form>

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