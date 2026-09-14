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

// Consultas dinámicas para obtener los conteos reales de tu base de datos
$total_productos = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn() ?: 0;
$total_categorias = $pdo->query("SELECT COUNT(*) FROM categorias")->fetchColumn() ?: 0;
$total_tiendas = $pdo->query("SELECT COUNT(*) FROM tiendas")->fetchColumn() ?: 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moon Essence - Panel de Administración</title>
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

        /* Menú Principal */
        .navbar-moon {
            background-color: rgba(5, 9, 14, 0.95);
            border-bottom: 1px solid #233044;
            backdrop-filter: blur(8px);
        }

        .brand-text {
            color: var(--accent-luna) !important;
            letter-spacing: 1.5px;
        }

        /* Tarjetas del Dashboard */
        .card-dashboard {
            background-color: var(--bg-tarjeta);
            border: 1px solid #28374d;
            border-radius: 14px;
            transition: all 0.3s ease;
        }

        .card-dashboard:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4), 0 0 15px rgba(249, 232, 208, 0.1);
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
            color: #0b131e;
            box-shadow: 0 0 12px rgba(249, 232, 208, 0.4);
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
            <a href="index.php" class="btn btn-sm btn-outline-moon">Volver a la Tienda</a>
            <a href="admin_dashboard.php" class="btn btn-sm btn-moon">Panel Admin</a>
        </div>
    </div>
</nav>

<!-- Encabezado del Panel -->
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="brand-text fw-bold m-0">Panel de Administración</h2>
            <p class="text-muted-moon mb-0">Gestión general del catálogo, categorías y analítica de ventas.</p>
        </div>
    </div>

    <!-- Módulos de Gestión principales -->
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
        
        <!-- Tarjeta 1: Productos -->
        <div class="col">
            <div class="card h-100 card-dashboard p-4 d-flex flex-column justify-content-between">
                <div>
                    <span class="text-uppercase small text-muted-moon fw-bold">Catálogo</span>
                    <h4 class="text-white fw-bold mt-1">Productos</h4>
                    <p class="text-muted-moon small">Administra los productos guardados, precios, fotos y existencias.</p>
                </div>
                <div>
                    <h1 class="display-5 fw-bold brand-text my-3"><?= $total_productos ?></h1>
                    <a href="admin_productos.php" class="btn btn-outline-moon w-100 py-2">Gestionar Productos →</a>
                </div>
            </div>
        </div>

        <!-- Tarjeta 2: Categorías -->
        <div class="col">
            <div class="card h-100 card-dashboard p-4 d-flex flex-column justify-content-between">
                <div>
                    <span class="text-uppercase small text-muted-moon fw-bold">Clasificación</span>
                    <h4 class="text-white fw-bold mt-1">Categorías</h4>
                    <p class="text-muted-moon small">Administra las categorías activas para clasificar las prendas.</p>
                </div>
                <div>
                    <h1 class="display-5 fw-bold brand-text my-3"><?= $total_categorias ?></h1>
                    <a href="admin_categorias.php" class="btn btn-outline-moon w-100 py-2">Gestionar Categorías →</a>
                </div>
            </div>
        </div>

        <!-- Tarjeta 3: Reportes y KPIs -->
        <div class="col">
            <div class="card h-100 card-dashboard p-4 d-flex flex-column justify-content-between border-warning">
                <div>
                    <span class="text-uppercase small text-muted-moon fw-bold">Métricas y KPIs</span>
                    <h4 class="text-white fw-bold mt-1">Reportes Analíticos</h4>
                    <p class="text-muted-moon small">Consulta los indicadores de ventas, ingresos acumulados y resumen de pedidos.</p>
                </div>
                <div>
                    <p class="fs-5 text-white fw-semibold my-3">Estadísticas en tiempo real</p>
                    <a href="reportes.php" class="btn btn-moon w-100 py-2">Ver Reportes Analíticos →</a>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Footer -->
<footer class="py-4 mt-auto">
    <div class="container text-center">
        <p class="brand-text fw-bold mb-1 fs-5">Moon Essence</p>
        <p class="small m-0">© 2026 Todos los derechos reservados. Panel de Administración.</p>
    </div>
</footer>

</body>
</html>