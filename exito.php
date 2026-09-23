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

// Verificar si hay un pedido exitoso en sesión
$id_pedido = isset($_SESSION['pedido_exitoso']) ? $_SESSION['pedido_exitoso'] : 0;
$pedido_info = null;

if ($id_pedido > 0) {
    $stmt = $pdo->prepare("SELECT * FROM pedidos WHERE id_pedido = ?");
    $stmt->execute([$id_pedido]);
    $pedido_info = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Si entran sin un pedido reciente, los redirigimos al inicio
if (!$pedido_info) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moon Essence - ¡Pedido Exitoso!</title>
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
        .card-exito {
            background-color: var(--bg-tarjeta);
            border: 1px solid #28374d;
            border-radius: 16px;
            max-width: 650px;
            margin: 0 auto;
        }
        .icono-luna {
            font-size: 4rem;
            color: var(--accent-luna);
            text-shadow: 0 0 20px rgba(249, 232, 208, 0.4);
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
    <div class="container justify-content-center">
        <a class="navbar-brand fw-bold fs-3 brand-text" href="index.php">
            <i class="bi bi-moon-stars-fill me-2"></i>Moon Essence
        </a>
    </div>
</nav>

<div class="container my-5 flex-grow-1 d-flex align-items-center justify-content-center">
    <div class="card card-exito p-4 p-md-5 text-center w-100 shadow-lg">
        <div class="mb-3">
            <i class="bi bi-moon-stars icono-luna"></i>
        </div>
        
        <h1 class="brand-text fw-bold mb-3">¡Gracias por tu compra, <?= htmlspecialchars($pedido_info['nombre_cliente']) ?>!</h1>
        <p class="text-muted-moon fs-5 mb-4">
            Tu pedido ha quedado registrado bajo la luna de Moon Essence y ya estamos preparando tus prendas con mucho cuidado.
        </p>

        <div class="card bg-dark border-secondary p-3 text-start mb-4 rounded-3">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted-moon">Número de Orden:</span>
                <span class="text-white fw-bold">#ME-<?= htmlspecialchars($pedido_info['id_pedido']) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted-moon">Dirección de Entrega:</span>
                <span class="text-white"><?= htmlspecialchars($pedido_info['direccion']) ?> (<?= htmlspecialchars($pedido_info['ciudad']) ?>)</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted-moon">Teléfono de contacto:</span>
                <span class="text-white"><?= htmlspecialchars($pedido_info['telefono']) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted-moon">Método de Pago:</span>
                <span class="text-white"><?= htmlspecialchars($pedido_info['metodo_pago']) ?></span>
            </div>
            <hr class="border-secondary my-2">
            <div class="d-flex justify-content-between">
                <span class="text-muted-moon fw-bold">Total Pagado:</span>
                <span class="text-success fw-bold fs-5">$<?= number_format($pedido_info['total'], 0, ',', '.') ?></span>
            </div>
        </div>

        <p class="small text-muted-moon mb-4">
            Pronto nos pondremos en contacto al número provisto para coordinar los detalles del envío.
        </p>

        <div>
            <a href="index.php" class="btn btn-moon px-5 py-2 fw-bold">
                <i class="bi bi-arrow-left me-2"></i> Volver al Inicio
            </a>
        </div>
    </div>
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