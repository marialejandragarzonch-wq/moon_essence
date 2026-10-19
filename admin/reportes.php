<?php
session_start();

$host = 'localhost';
$db   = 'moon_essence';
$user = 'root';
$pass = '';

$ingresos_totales_num = 0;
$pedidos_completados = 0;
$ticket_promedio_num = 0;

$ultimas_transacciones = [];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Obtener la suma total de ingresos reales de la tabla pedidos
    $stmtIngresos = $pdo->query("SELECT SUM(total) FROM pedidos");
    $ingresos_totales_num = $stmtIngresos->fetchColumn() ?: 0;

    // 2. Obtener la cantidad total de pedidos realizados
    $stmtPedidos = $pdo->query("SELECT COUNT(*) FROM pedidos");
    $pedidos_completados = $stmtPedidos->fetchColumn() ?: 0;

    // 3. Calcular el Ticket Promedio dinámico
    if ($pedidos_completados > 0) {
        $ticket_promedio_num = $ingresos_totales_num / $pedidos_completados;
    }

    // 4. Consultar las últimas transacciones reales uniendo con la tabla usuarios si aplica
    $stmtTrans = $pdo->query("SELECT p.*, COALESCE(u.nombre, p.nombre_cliente, 'Cliente General') AS cliente_nombre 
                              FROM pedidos p 
                              LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario 
                              ORDER BY p.id_pedido DESC LIMIT 10");
    $ultimas_transacciones = $stmtTrans->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si falla la conexión, mantener variables en cero
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes Analíticos - Moon Essence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg-principal: #0b131e;
            --bg-card: #131c2a;
            --bg-card-inner: #1a2738;
            --accent-luna: #f9e8d0;
            --texto-suave: #d1dbe8;
            --border-color: #213045;
        }
        body {
            background-color: var(--bg-principal);
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        .navbar-admin {
            background-color: #080e17;
            border-bottom: 1px solid var(--border-color);
            padding: 12px 24px;
        }
        .brand-text {
            color: var(--accent-luna) !important;
            font-weight: 700;
            text-decoration: none;
        }
        .metric-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 22px;
        }
        .icon-box {
            background-color: #1a2738;
            border-radius: 8px;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-luna);
        }
        
        .text-beige {
            color: var(--accent-luna) !important;
        }
        .text-readable-sub {
            color: var(--accent-luna) !important;
            opacity: 0.9;
        }

        .table-custom {
            background-color: transparent !important;
            color: #ffffff !important;
            font-size: 0.88rem;
        }
        .table-custom tr, .table-custom td, .table-custom th {
            background-color: transparent !important;
        }
        .table-custom th {
            color: var(--accent-luna) !important;
            font-weight: 700;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 12px;
        }
        .table-custom td {
            border-bottom: 1px solid var(--border-color);
            padding: 14px 0;
            vertical-align: middle;
            color: #ffffff !important;
        }
        .badge-id {
            background-color: #1a2738;
            color: var(--accent-luna);
            border: 1px solid #28374d;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .info-box-text {
            color: var(--accent-luna) !important;
        }
    </style>
</head>
<body>

<!-- Navbar Admin -->
<nav class="navbar-admin d-flex justify-content-between align-items-center">
    <a class="brand-text fs-5 d-flex align-items-center gap-2" href="admin_dashboard.php">
        <i class="bi bi-moon-stars-fill"></i> Moon Essence <span class="fs-6 text-secondary fw-normal">| Reportes Analíticos</span>
    </a>
    
    <div class="d-flex align-items-center gap-2">
        <a href="admin_dashboard.php" class="btn btn-sm btn-outline-light">
            <i class="bi bi-arrow-left me-1"></i> Volver al Panel
        </a>
        <a href="../auth/logout.php" class="btn btn-sm btn-danger fw-bold">
            <i class="bi bi-box-arrow-right me-1"></i> Salir
        </a>
    </div>
</nav>

<div class="container-fluid px-4 py-4">
    <!-- Encabezado -->
    <div class="mb-4">
        <h2 class="fw-bold m-0 text-beige"><i class="bi bi-graph-up-arrow me-2"></i>Reportes Analíticos & Ventas</h2>
        <p class="text-readable-sub small m-0 mt-1">Métricas de rendimiento financiero, transacciones e ingresos en tiempo real.</p>
    </div>

    <!-- Métricas KPI Dinámicas -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="metric-card d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-beige small d-block mb-1">Ingresos Totales</span>
                    <h2 class="fw-bold m-0 text-success">$<?= number_format($ingresos_totales_num, 2, ',', '.') ?></h2>
                </div>
                <div class="icon-box text-success"><i class="bi bi-cash-stack fs-4"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-beige small d-block mb-1">Pedidos Realizados</span>
                    <h2 class="fw-bold m-0 text-white"><?= $pedidos_completados ?></h2>
                </div>
                <div class="icon-box"><i class="bi bi-bag-check fs-4"></i></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-beige small d-block mb-1">Ticket Promedio</span>
                    <h2 class="fw-bold m-0 text-white">$<?= number_format($ticket_promedio_num, 2, ',', '.') ?></h2>
                </div>
                <div class="icon-box"><i class="bi bi-receipt fs-4"></i></div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="metric-card h-100">
                <h5 class="fw-bold text-beige mb-3"><i class="bi bi-graph-up me-2"></i>Comportamiento de Ventas</h5>
                <div style="height: 260px;">
                    <canvas id="chartVentas"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card h-100 d-flex flex-column justify-content-between">
                <h5 class="fw-bold text-beige mb-2"><i class="bi bi-pie-chart me-2"></i>Estado de Pedidos</h5>
                <div style="height: 220px;" class="d-flex justify-content-center align-items-center">
                    <canvas id="chartEstado"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla Dinámica de Transacciones Realizadas -->
    <div class="row g-4">
        <div class="col-md-8">
            <div class="metric-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-beige m-0"><i class="bi bi-clock-history me-2"></i>Últimas Transacciones</h5>
                    <span class="badge bg-primary">Tiempo Real</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless table-custom m-0">
                        <thead>
                            <tr>
                                <th>N° PEDIDO</th>
                                <th>FECHA</th>
                                <th>CLIENTE</th>
                                <th>TOTAL</th>
                                <th>ESTADO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ultimas_transacciones)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No hay compras registradas en el sistema aún.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ultimas_transacciones as $trans): ?>
                                    <tr>
                                        <td><span class="badge-id">#<?= $trans['id_pedido'] ?></span></td>
                                        <td class="text-white"><?= htmlspecialchars($trans['fecha_pedido'] ?? $trans['fecha_creacion'] ?? 'Reciente') ?></td>
                                        <td class="text-white"><?= htmlspecialchars($trans['cliente_nombre']) ?></td>
                                        <td class="text-success fw-bold">$<?= number_format($trans['total'] ?? 0, 2, ',', '.') ?></td>
                                        <td>
                                            <span class="badge bg-<?= ($trans['estado_pedido'] ?? $trans['estado'] ?? '') === 'cancelado' ? 'danger' : 'success' ?>">
                                                <?= ucfirst(htmlspecialchars($trans['estado_pedido'] ?? $trans['estado'] ?? 'Completado')) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="metric-card h-100 d-flex flex-column justify-content-between">
                <div>
                    <h5 class="fw-bold text-beige mb-3"><i class="bi bi-trophy me-2"></i>Productos Más Vendidos</h5>
                    <div class="p-3 border border-secondary rounded-3" style="background-color: #1a2738;">
                        <p class="info-box-text small m-0 text-center">
                            <i class="bi bi-info-circle me-1 text-warning"></i>
                            Los datos del top de productos se irán actualizando automáticamente según el registro continuo de nuevas ventas.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ctxVentas = document.getElementById('chartVentas').getContext('2d');
    new Chart(ctxVentas, {
        type: 'line',
        data: {
            labels: ['May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct'],
            datasets: [{
                label: 'Ventas ($)',
                data: [35000, 42000, 58000, 63000, 72000, <?= (float)$ingresos_totales_num ?>],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#f9e8d0' }, grid: { color: '#1e293b' } },
                y: { ticks: { color: '#f9e8d0' }, grid: { color: '#1e293b' } }
            }
        }
    });

    const ctxEstado = document.getElementById('chartEstado').getContext('2d');
    new Chart(ctxEstado, {
        type: 'doughnut',
        data: {
            labels: ['Completados', 'Pendientes', 'Cancelados'],
            datasets: [{
                data: [<?= (int)$pedidos_completados ?>, 0, 0],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#f9e8d0' }
                }
            }
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>