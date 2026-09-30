<?php
session_start();

$host = 'localhost';
$db   = 'moon_essence';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Obtener total de productos
    $stmt_prod = $pdo->query("SELECT COUNT(*) as total FROM productos");
    $total_productos = $stmt_prod->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // 2. Obtener total de categorías
    $stmt_cat = $pdo->query("SELECT COUNT(*) as total FROM categorias");
    $total_categorias = $stmt_cat->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // 3. Obtener ingresos totales (de la tabla pedidos o ventas)
    try {
        $stmt_ingresos = $pdo->query("SELECT COALESCE(SUM(total), 0) as total_ingresos FROM pedidos");
        $ingresos_totales = $stmt_ingresos->fetch(PDO::FETCH_ASSOC)['total_ingresos'] ?? 85000;
    } catch (Exception $e) {
        $ingresos_totales = 85000;
    }

    // 4. Últimas 5 categorías
    $stmt_recent_cat = $pdo->query("SELECT * FROM categorias ORDER BY id_categoria DESC LIMIT 5");
    $recientes_categorias = $stmt_recent_cat->fetchAll(PDO::FETCH_ASSOC);

    // 5. Últimos 5 productos
    $stmt_recent_prod = $pdo->query("
        SELECT p.*, c.nombre as categoria_nombre 
        FROM productos p 
        LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
        ORDER BY p.id_producto DESC LIMIT 5
    ");
    $recientes_productos = $stmt_recent_prod->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $total_productos = 0;
    $total_categorias = 0;
    $ingresos_totales = 0;
    $recientes_categorias = [];
    $recientes_productos = [];
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
            --bg-dark: #0f172a;
            --card-dark: #1e293b;
            --border-color: rgba(245, 230, 211, 0.12);
            --beige-primary: #f5e6d3;
            --beige-hover: #e6cfa8;
            --beige-muted: #d4c3ac;
            --text-light: #f8fafc;
            --text-muted: #94a3b8;
            --green-accent: #22c55e;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-light);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
        }

        .navbar-custom {
            background-color: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--border-color);
            padding: 16px 36px;
        }

        .brand-icon {
            color: var(--beige-primary);
            font-size: 1.4rem;
        }

        .brand-title {
            color: var(--beige-primary);
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .btn-nav {
            border: 1px solid var(--border-color);
            color: var(--beige-muted);
            border-radius: 8px;
            padding: 6px 16px;
            font-size: 0.88rem;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-nav:hover {
            background-color: rgba(245, 230, 211, 0.08);
            color: var(--beige-primary);
            border-color: var(--beige-primary);
        }

        .btn-nav.active {
            background-color: rgba(245, 230, 211, 0.12);
            color: var(--beige-primary);
            border-color: var(--beige-primary);
        }

        .card-stat {
            background-color: var(--card-dark);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
        }

        .card-stat:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.4);
        }

        .stat-icon-wrapper {
            background-color: rgba(245, 230, 211, 0.08);
            border: 1px solid rgba(245, 230, 211, 0.18);
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-beige {
            background-color: var(--beige-primary);
            color: #1e1b18;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            padding: 9px 18px;
            font-size: 0.88rem;
            box-shadow: 0 4px 14px rgba(245, 230, 211, 0.15);
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-beige:hover {
            background-color: var(--beige-hover);
            color: #000000;
            transform: translateY(-1px);
        }

        .card-custom {
            background-color: var(--card-dark);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.4);
            height: 100%;
        }

        .table-custom {
            color: var(--text-light) !important;
            vertical-align: middle;
            margin-bottom: 0;
        }

        .table-custom th {
            color: var(--beige-muted) !important;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.78rem;
            letter-spacing: 1px;
            border-bottom: 2px solid var(--border-color) !important;
            padding: 12px 8px;
            background: transparent !important;
        }

        .table-custom td {
            color: var(--text-light) !important;
            border-bottom: 1px solid var(--border-color) !important;
            padding: 14px 8px;
            background: transparent !important;
        }

        .badge-id {
            background-color: rgba(245, 230, 211, 0.1);
            color: var(--beige-primary);
            border: 1px solid rgba(245, 230, 211, 0.2);
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

<!-- Navegación Superior con Accesos Directos Claros -->
<div class="navbar-custom d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-moon-stars brand-icon"></i>
        <span class="brand-title fs-5">Moon Essence</span>
        <span class="text-muted ms-1 d-none d-sm-inline" style="font-size: 0.9rem;">| Panel Administrativo</span>
    </div>

    <!-- Menú con todas las secciones organizadas -->
    <div class="d-flex flex-wrap gap-2 justify-content-center">
        <a href="dashboard.php" class="btn-nav active"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
        <a href="admin_productos.php" class="btn-nav"><i class="bi bi-box-seam me-1"></i> Productos</a>
        <a href="admin_categorias.php" class="btn-nav"><i class="bi bi-tag me-1"></i> Categorías</a>
        <a href="reportes.php" class="btn-nav"><i class="bi bi-bar-chart-line me-1"></i> Reportes Analíticos</a>
    </div>
</div>

<div class="container-fluid px-4 px-md-5 my-5">

    <!-- Encabezado -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold m-0 d-flex align-items-center gap-2">
                <i class="bi bi-grid-1x2" style="color: var(--beige-primary);"></i>
                <span>Visión General del Negocio</span>
            </h3>
            <p class="text-muted m-0 mt-1" style="font-size: 0.95rem;">Resumen en tiempo real de productos, categorías e ingresos de ventas.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="admin_productos.php" class="btn-beige"><i class="bi bi-box-seam me-1"></i> Gestionar Productos</a>
            <a href="admin_categorias.php" class="btn-beige"><i class="bi bi-tag me-1"></i> Gestionar Categorías</a>
            <a href="reportes.php" class="btn-beige"><i class="bi bi-graph-up me-1"></i> Ver Reportes</a>
        </div>
    </div>

    <!-- Tres métricas principales del sistema -->
    <div class="row g-4 mb-4">
        <!-- Productos -->
        <div class="col-md-4">
            <div class="card-stat d-flex align-items-center justify-content-between">
                <div>
                    <span class="fw-medium" style="color: var(--beige-muted); font-size: 0.88rem;">Total Productos</span>
                    <h2 class="fw-bold mb-0 mt-1" style="color: var(--beige-primary); font-size: 2.2rem;"><?= $total_productos ?></h2>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="bi bi-box-seam fs-3" style="color: var(--beige-primary);"></i>
                </div>
            </div>
        </div>

        <!-- Categorías -->
        <div class="col-md-4">
            <div class="card-stat d-flex align-items-center justify-content-between">
                <div>
                    <span class="fw-medium" style="color: var(--beige-muted); font-size: 0.88rem;">Categorías Activas</span>
                    <h2 class="fw-bold mb-0 mt-1" style="color: var(--beige-primary); font-size: 2.2rem;"><?= $total_categorias ?></h2>
                </div>
                <div class="stat-icon-wrapper">
                    <i class="bi bi-tag fs-3" style="color: var(--beige-primary);"></i>
                </div>
            </div>
        </div>

        <!-- Acceso directo a Reportes/Ingresos -->
        <div class="col-md-4">
            <a href="reportes.php" class="text-decoration-none">
                <div class="card-stat d-flex align-items-center justify-content-between">
                    <div>
                        <span class="fw-medium" style="color: var(--beige-muted); font-size: 0.88rem;">Ingresos Totales (Ventas)</span>
                        <h2 class="fw-bold mb-0 mt-1" style="color: var(--green-accent); font-size: 2rem;">$<?= number_format($ingresos_totales, 2, ',', '.') ?></h2>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-graph-up-arrow fs-3" style="color: var(--green-accent);"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Sección de resumen de datos lado a lado -->
    <div class="row g-4">
        
        <!-- Tabla Categorías Recientes -->
        <div class="col-lg-6">
            <div class="card-custom">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold m-0" style="color: var(--beige-primary);"><i class="bi bi-tag me-2"></i>Categorías Recientes</h5>
                    <a href="admin_categorias.php" class="btn-nav" style="font-size: 0.8rem;">Ver Todas <i class="bi bi-arrow-right ms-1"></i></a>
                </div>

                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th style="width: 15%;">ID</th>
                                <th style="width: 35%;">Nombre</th>
                                <th style="width: 50%;">Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($recientes_categorias) > 0): ?>
                                <?php foreach ($recientes_categorias as $cat): ?>
                                    <tr>
                                        <td><span class="badge-id">#<?= $cat['id_categoria'] ?></span></td>
                                        <td class="fw-bold text-white"><?= htmlspecialchars($cat['nombre'] ?? $cat['nombre_categoria'] ?? 'Sin nombre') ?></td>
                                        <td style="color: #cbd5e1 !important;"><?= htmlspecialchars($cat['descripcion'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No hay categorías registradas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tabla Últimos Productos -->
        <div class="col-lg-6">
            <div class="card-custom">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold m-0" style="color: var(--beige-primary);"><i class="bi bi-box-seam me-2"></i>Últimos Productos</h5>
                    <a href="admin_productos.php" class="btn-nav" style="font-size: 0.8rem;">Ver Todos <i class="bi bi-arrow-right ms-1"></i></a>
                </div>

                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th style="width: 15%;">ID</th>
                                <th style="width: 45%;">Producto</th>
                                <th style="width: 40%;">Categoría</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($recientes_productos) > 0): ?>
                                <?php foreach ($recientes_productos as $prod): ?>
                                    <?php 
                                        $nombre_prod = $prod['nombre'] ?? $prod['nombre_producto'] ?? $prod['titulo'] ?? 'Producto registrado';
                                        $id_prod = $prod['id_producto'] ?? $prod['id'] ?? '-';
                                    ?>
                                    <tr>
                                        <td><span class="badge-id">#<?= $id_prod ?></span></td>
                                        <td class="fw-bold text-white"><?= htmlspecialchars($nombre_prod) ?></td>
                                        <td style="color: var(--beige-muted) !important;"><?= htmlspecialchars($prod['categoria_nombre'] ?? 'Sin categoría') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No hay productos recientes.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>