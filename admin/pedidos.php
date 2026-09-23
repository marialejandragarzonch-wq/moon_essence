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

// Consultar todos los pedidos ordenados del más reciente al más antiguo
$stmt = $pdo->query("SELECT * FROM pedidos ORDER BY fecha_pedido DESC");
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moon Essence - Panel de Administración | Pedidos</title>
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
        .card-admin {
            background-color: var(--bg-tarjeta);
            border: 1px solid #28374d;
            border-radius: 14px;
        }
        .table-moon {
            color: #ffffff;
            vertical-align: middle;
        }
        .table-moon th {
            background-color: #121a24 !important;
            color: var(--accent-luna);
            border-color: #28374d;
        }
        .table-moon td {
            background-color: var(--bg-tarjeta) !important;
            color: #ffffff !important;
            border-color: #28374d;
        }
        .badge-pendiente {
            background-color: #ffc107;
            color: #000;
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
            <i class="bi bi-moon-stars-fill me-2"></i>Moon Essence <small class="fs-6 text-muted-moon">| Admin</small>
        </a>
        <a href="../index.php" class="btn btn-outline-light btn-sm"><i class="bi bi-shop me-1"></i> Ver Tienda</a>
    </div>
</nav>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="brand-text mb-1"><i class="bi bi-clipboard2-data me-2"></i>Gestión de Pedidos</h2>
            <p class="text-muted-moon m-0">Aquí puedes ver los pedidos realizados por los clientes en la tienda.</p>
        </div>
        <span class="badge bg-secondary fs-6 p-2">Total Pedidos: <?= count($pedidos) ?></span>
    </div>

    <div class="card card-admin p-4 shadow-lg">
        <?php if (empty($pedidos)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted-moon"></i>
                <p class="text-muted-moon mt-3 fs-5">Aún no se ha registrado ningún pedido bajo la luna.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-moon align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Contacto / Ciudad</th>
                            <th>Dirección</th>
                            <th>Método Pago</th>
                            <th>Total</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $p): ?>
                            <tr>
                                <td class="fw-bold text-warning">#ME-<?= $p['id_pedido'] ?></td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($p['nombre_cliente']) ?></div>
                                </td>
                                <td>
                                    <div><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($p['telefono']) ?></div>
                                    <small class="text-muted-moon"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($p['ciudad']) ?></small>
                                </td>
                                <td>
                                    <div><?= htmlspecialchars($p['direccion']) ?></div>
                                    <?php if (!empty($p['notas'])): ?>
                                        <small class="text-info">Nota: <?= htmlspecialchars($p['notas']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-dark border border-secondary"><?= htmlspecialchars($p['metodo_pago'] ?? 'Contra Entrega') ?></span></td>
                                <td class="text-success fw-bold">$<?= number_format($p['total'], 0, ',', '.') ?></td>
                                <td class="small text-muted-moon"><?= $p['fecha_pedido'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer class="py-4 mt-auto">
    <div class="container text-center">
        <p class="brand-text fw-bold mb-1 fs-5">Moon Essence</p>
        <p class="small m-0">Panel de Control - © 2026</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>