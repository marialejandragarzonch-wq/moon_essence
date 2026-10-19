<?php
session_start();

$host = 'localhost';
$db   = 'moon_essence';
$user = 'root';
$pass = '';

$total_productos = 0;
$total_categorias = 0;
$total_ventas = "85.000,00";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Contar total de productos
    $stmtP = $pdo->query("SELECT COUNT(*) FROM productos");
    $total_productos = $stmtP->fetchColumn();

    // Contar total de categorías
    $stmtC = $pdo->query("SELECT COUNT(*) FROM categorias");
    $total_categorias = $stmtC->fetchColumn();

} catch (PDOException $e) {
    // Mantener valores por defecto si falla la conexión
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
            --bg-principal: #0b131e;
            --bg-tarjeta: #131c2a;
            --accent-luna: #f9e8d0;
            --texto-suave: #aebbc9;
            --border-color: #233044;
        }
        body {
            background-color: var(--bg-principal);
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar-admin {
            background-color: rgba(5, 9, 14, 0.95);
            border-bottom: 1px solid var(--border-color);
            backdrop-filter: blur(8px);
        }
        .brand-text {
            color: var(--accent-luna) !important;
            letter-spacing: 1.5px;
            text-decoration: none;
        }
        .card-admin {
            background-color: var(--bg-tarjeta);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 28px;
            height: 100%;
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
            border-top: 1px solid var(--border-color);
            color: var(--texto-suave);
            margin-top: auto;
        }
    </style>
</head>
<body>

<!-- Navbar Superior -->
<nav class="navbar navbar-expand-lg navbar-admin sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 brand-text" href="admin_dashboard.php">
            <i class="bi bi-moon-stars-fill me-2"></i>Moon Essence <span class="fs-6 text-muted-moon fw-normal">| Panel Admin</span>
        </a>
        <div class="d-flex align-items-center gap-2">
            <a href="../index.php" class="btn btn-sm btn-outline-light">
                <i class="bi bi-shop me-1"></i> Volver a la Tienda
            </a>
            <!-- Botón Salir / Cerrar Sesión -->
            <a href="../auth/logout.php" class="btn btn-sm btn-danger fw-bold">
                <i class="bi bi-box-arrow-right me-1"></i> Salir
            </a>
        </div>
    </div>
</nav>

<!-- Contenido Principal -->
<div class="container my-5">
    <div class="mb-4">
        <h2 class="fw-bold brand-text mb-1">Panel de Control</h2>
        <p class="text-muted-moon">Bienvenido al centro de administración de Moon Essence.</p>
    </div>

    <div class="row g-4">
        <!-- Tarjeta Productos -->
        <div class="col-md-4">
            <div class="card-admin d-flex flex-column justify-content-between">
                <div>
                    <span class="text-uppercase small fw-bold text-muted-moon">CATÁLOGO</span>
                    <h3 class="fw-bold text-white mt-1 mb-2">Productos</h3>
                    <p class="text-muted-moon small">Administra las prendas, precios, stock e imágenes del inventario.</p>
                    <div class="display-5 fw-bold brand-text my-3"><?= $total_productos ?></div>
                </div>
                <!-- Enlace redirigido a admin_productos.php -->
                <a href="admin_productos.php" class="btn btn-beigecito w-100 mt-2">Gestionar Productos &rarr;</a>
            </div>
        </div>

        <!-- Tarjeta Categorías -->
        <div class="col-md-4">
            <div class="card-admin d-flex flex-column justify-content-between">
                <div>
                    <span class="text-uppercase small fw-bold text-muted-moon">CLASIFICACIÓN</span>
                    <h3 class="fw-bold text-white mt-1 mb-2">Categorías</h3>
                    <p class="text-muted-moon small">Administra las categorías activas para clasificar las prendas.</p>
                    <div class="display-5 fw-bold brand-text my-3"><?= $total_categorias ?></div>
                </div>
                <!-- Enlace redirigido a admin_categorias.php -->
                <a href="admin_categorias.php" class="btn btn-beigecito w-100 mt-2">Gestionar Categorías &rarr;</a>
            </div>
        </div>

        <!-- Tarjeta Reportes Analíticos -->
        <div class="col-md-4">
            <div class="card-admin d-flex flex-column justify-content-between">
                <div>
                    <span class="text-uppercase small fw-bold text-muted-moon">MÉTRICAS Y KPIS</span>
                    <h3 class="fw-bold text-white mt-1 mb-2">Reportes Analíticos</h3>
                    <p class="text-muted-moon small">Consulta los indicadores de ventas, ingresos acumulados y resumen de pedidos.</p>
                    <div class="display-5 fw-bold text-success my-3">$<?= $total_ventas ?></div>
                </div>
                <!-- Enlace redirigido a reportes.php -->
                <a href="reportes.php" class="btn btn-beigecito w-100 mt-2">Ver Reportes Analíticos &rarr;</a>
            </div>
        </div>
    </div>
</div>

<footer class="py-4 mt-auto">
    <div class="container text-center">
        <p class="brand-text fw-bold mb-1 fs-6">Moon Essence</p>
        <p class="small m-0">Panel de Control - © 2026</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>