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

// Variables por defecto
$ingresos_totales = 0;
$pedidos_completados = 0;
$ticket_promedio = 0;
$transacciones = [];

// Intentar consultar la tabla de ventas/pedidos (puedes cambiar 'pedidos' si tu tabla se llama diferente, ej: 'ordenes' o 'ventas')
try {
    $stmt_totales = $pdo->query("SELECT SUM(total) as ingresos, COUNT(*) as total_pedidos FROM pedidos");
    $res = $stmt_totales->fetch(PDO::FETCH_ASSOC);
    if ($res) {
        $ingresos_totales = $res['ingresos'] ?? 0;
        $pedidos_completados = $res['total_pedidos'] ?? 0;
        if ($pedidos_completados > 0) {
            $ticket_promedio = $ingresos_totales / $pedidos_completados;
        }
    }

    $stmt_trans = $pdo->query("SELECT * FROM pedidos ORDER BY id_pedido DESC LIMIT 10");
    $transacciones = $stmt_trans->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Si la tabla no existe o tiene otro nombre, evitamos que rompa la página
    $transacciones = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moon Essence - Reportes Analíticos</title>
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
        .kpi-card {
            background-color: var(--bg-tarjeta);
            border: 1px solid #28374d;
            border-radius: 12px;
            transition: transform 0.2s;
        }
        .kpi-card:hover {
            border-color: var(--accent-luna);
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
        <a class="navbar-brand fw-bold fs-3 brand-text" href="admin_dashboard.php">
            <i class="bi bi-moon-stars-fill me-2"></i>Moon Essence
        </a>
        <div class="d-flex align-items-center gap-3">
            <a href="admin_dashboard.php" class="btn btn-sm btn-beigecito"><i class="bi bi-arrow-left me-1"></i> Volver al Panel</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="mb-4">
        <h2 class="brand-text fw-bold"><i class="bi bi-bar-chart-line-fill me-2"></i>Reportes Analíticos</h2>
        <p class="text-muted-moon">Métricas de rendimiento financiero y registro de ventas.</p>
    </div>

    <!-- Tarjetas de KPIs -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="kpi-card p-4 text-center shadow">
                <span class="text-muted-moon text-uppercase small fw-bold tracking-wider">Ingresos Totales</span>
                <h2 class="text-success fw-bold mt-2 mb-0">$<?= number_format($ingresos_totales, 2, ',', '.') ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card p-4 text-center shadow">
                <span class="text-muted-moon text-uppercase small fw-bold tracking-wider">Pedidos Completados</span>
                <h2 class="brand-text fw-bold mt-2 mb-0"><?= $pedidos_completados ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card p-4 text-center shadow">
                <span class="text-muted-moon text-uppercase small fw-bold tracking-wider">Ticket Promedio</span>
                <h2 class="text-warning fw-bold mt-2 mb-0">$<?= number_format($ticket_promedio, 2, ',', '.') ?></h2>
            </div>
        </div>
    </div>

    <!-- Tabla de Transacciones -->
    <div class="card card-admin p-4 shadow-lg">
        <h4 class="brand-text mb-3"><i class="bi bi-clock-history me-2"></i>Últimas Transacciones</h4>

        <?php if (empty($transacciones)): ?>
            <div class="text-center py-5">
                <i class="bi bi-receipt fs-1 text-muted-moon"></i>
                <p class="text-muted-moon mt-3 fs-5">No se registraron ventas en el sistema por el momento.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-moon align-middle">
                    <thead>
                        <tr>
                            <th>N° Pedido</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transacciones as $t): ?>
                            <tr>
                                <td class="fw-bold text-warning">#<?= $t['id_pedido'] ?? $t['id'] ?></td>
                                <td><?= htmlspecialchars($t['fecha'] ?? 'N/D') ?></td>
                                <td><?= htmlspecialchars($t['cliente'] ?? $t['nombre_cliente'] ?? 'Cliente General') ?></td>
                                <td class="text-success fw-bold">$<?= number_format($t['total'], 2, ',', '.') ?></td>
                                <td><span class="badge bg-success"><?= htmlspecialchars($t['estado'] ?? 'Completado') ?></span></td>
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
        <p class="small m-0">© 2026 Todos los derechos reservados.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>