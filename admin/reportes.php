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

// Consultas analíticas (se asume una tabla 'ventas' o se devuelven valores en 0 si aún no hay ventas)
try {
    $ingresos = $pdo->query("SELECT SUM(total) FROM ventas")->fetchColumn() ?: 0;
    $pedidos = $pdo->query("SELECT COUNT(*) FROM ventas")->fetchColumn() ?: 0;
    
    $stmt_ventas = $pdo->query("SELECT * FROM ventas ORDER BY fecha DESC LIMIT 10");
    $ventas = $stmt_ventas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Si aún no has creado la tabla ventas, se inicializan variables por defecto
    $ingresos = 0;
    $pedidos = 0;
    $ventas = [];
}

$ticket_promedio = $pedidos > 0 ? $ingresos / $pedidos : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moon Essence - Reportes Analíticos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .card-kpi {
            background-color: var(--bg-tarjeta);
            border: 1px solid #28374d;
            border-radius: 14px;
        }

        .btn-moon {
            background-color: var(--accent-luna);
            color: #0b131e;
            font-weight: 600;
            border: none;
            border-radius: 8px;
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

        .table-moon {
            background-color: var(--bg-tarjeta);
            color: #ffffff;
            border-color: #28374d;
        }

        .table-moon th {
            background-color: #05090e;
            color: var(--accent-luna);
            border-color: #28374d;
        }

        .table-moon td {
            border-color: #28374d;
        }

        footer {
            background-color: #05090e;
            border-top: 1px solid #233044;
            color: var(--texto-suave);
        }
    </style>
</head>
<body>

<!-- Menú Principal -->
<nav class="navbar navbar-expand-lg navbar-moon sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3 brand-text" href="index.php">Moon Essence</a>
        <div class="d-flex align-items-center gap-2">
            <a href="admin_dashboard.php" class="btn btn-sm btn-outline-moon">← Volver al Panel</a>
        </div>
    </div>
</nav>

<!-- Contenido del Reporte -->
<div class="container my-5">
    <div class="mb-4">
        <h2 class="brand-text fw-bold m-0">Reportes Analíticos</h2>
        <p class="text-muted-moon">Métricas de rendimiento financiero y registro de ventas.</p>
    </div>

    <!-- Indicadores KPIs -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card-kpi p-4 text-center">
                <small class="text-uppercase text-muted-moon fw-bold">Ingresos Totales</small>
                <h2 class="brand-text fw-bold mt-2">$<?= number_format($ingresos, 2, '.', ',') ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-kpi p-4 text-center">
                <small class="text-uppercase text-muted-moon fw-bold">Pedidos Completados</small>
                <h2 class="text-white fw-bold mt-2"><?= $pedidos ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-kpi p-4 text-center">
                <small class="text-uppercase text-muted-moon fw-bold">Ticket Promedio</small>
                <h2 class="text-white fw-bold mt-2">$<?= number_format($ticket_promedio, 2, '.', ',') ?></h2>
            </div>
        </div>
    </div>

    <!-- Tabla de Detalle -->
    <h4 class="text-white mb-3">Últimas Transacciones</h4>
    <div class="table-responsive rounded border border-secondary">
        <table class="table table-moon mb-0 align-middle">
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
                <?php if (!empty($ventas)): ?>
                    <?php foreach ($ventas as $v): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($v['id_venta'] ?? $v['id']) ?></td>
                            <td><?= htmlspecialchars($v['fecha']) ?></td>
                            <td><?= htmlspecialchars($v['cliente'] ?? 'Cliente General') ?></td>
                            <td class="brand-text fw-bold">$<?= number_format($v['total'], 2, '.', ',') ?></td>
                            <td><span class="badge bg-success">Completado</span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted-moon">No se registraron ventas en el sistema por el momento.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Footer -->
<footer class="py-4 mt-auto">
    <div class="container text-center">
        <p class="brand-text fw-bold mb-1 fs-5">Moon Essence</p>
        <p class="small m-0">© 2026 Todos los derechos reservados.</p>
    </div>
</footer>

</body>
</html>