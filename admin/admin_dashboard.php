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

// Contadores para las tarjetas del dashboard
$total_productos = 0;
$total_categorias = 0;
$ingresos_totales = 0;

try {
    $stmt_p = $pdo->query("SELECT COUNT(*) FROM productos");
    $total_productos = $stmt_p->fetchColumn();

    $stmt_c = $pdo->query("SELECT COUNT(*) FROM categorias");
    $total_categorias = $stmt_c->fetchColumn();

    $stmt_i = $pdo->query("SELECT SUM(total) FROM pedidos");
    $ingresos_totales = $stmt_i->fetchColumn() ?: 0;
} catch (Exception $e) {
    // Evita errores si alguna tabla aún no tiene registros
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Moon Essence</title>
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
            transition: transform 0.2s ease, border-color 0.2s ease;
        }
        .card-admin:hover {
            border-color: var(--accent-luna);
            transform: translateY(-3px);
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
    <div class="container">
        <a class="navbar-brand fw-bold fs-3 brand-text" href="admin_dashboard.php">
            <i class="bi bi-moon-stars-fill me-2"></i>Moon Essence <small class="fs-6 text-muted-moon">| Panel Admin</small>
        </a>
        <div class="d-flex align-items-center gap-3">
            <!-- Botón Volver a la Tienda -->
            <a href="../index.php" class="btn btn-sm btn-outline-light"><i class="bi bi-shop me-1"></i> Volver a la Tienda</a>
            
            <!-- Botón Salir (Cerrar sesión) con la ruta corregida ../auth/logout.php -->
            <a href="../auth/logout.php" class="btn btn-sm btn-danger"><i class="bi bi-box-arrow-right me-1"></i> Salir</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="mb-4">
        <h2 class="brand-text fw-bold">Panel de Control</h2>
        <p class="text-muted-moon">Bienvenido al centro de administración de Moon Essence.</p>
    </div>

    <div class="row g-4">
        <!-- Gestión de Productos -->
        <div class="col-md-4">
            <div class="card card-admin p-4 shadow-lg h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="text-uppercase small text-muted-moon fw-bold mb-1">Catálogo</div>
                    <h4 class="brand-text mb-2">Productos</h4>
                    <p class="text-muted-moon small">Administra las prendas, precios, stock e imágenes del inventario.</p>
                    <h2 class="text-warning fw-bold my-3"><?= $total_productos ?></h2>
                </div>
                <a href="admin_productos.php" class="btn btn-moon w-100 mt-3">Gestionar Productos <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>

        <!-- Gestión de Categorías -->
        <div class="col-md-4">
            <div class="card card-admin p-4 shadow-lg h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="text-uppercase small text-muted-moon fw-bold mb-1">Clasificación</div>
                    <h4 class="brand-text mb-2">Categorías</h4>
                    <p class="text-muted-moon small">Administra las categorías activas para clasificar las prendas.</p>
                    <h2 class="brand-text fw-bold my-3"><?= $total_categorias ?></h2>
                </div>
                <a href="admin_categorias.php" class="btn btn-moon w-100 mt-3">Gestionar Categorías <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>

        <!-- Reportes Analíticos -->
        <div class="col-md-4">
            <div class="card card-admin p-4 shadow-lg h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="text-uppercase small text-muted-moon fw-bold mb-1">Métricas y KPIs</div>
                    <h4 class="brand-text mb-2">Reportes Analíticos</h4>
                    <p class="text-muted-moon small">Consulta los indicadores de ventas, ingresos acumulados y resumen de pedidos.</p>
                    <h2 class="text-success fw-bold my-3">$<?= number_format($ingresos_totales, 2, ',', '.') ?></h2>
                </div>
                <a href="reportes.php" class="btn btn-moon w-100 mt-3">Ver Reportes Analíticos <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
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