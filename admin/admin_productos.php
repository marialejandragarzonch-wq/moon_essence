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

// Obtener productos
$productos = [];
try {
    $stmt = $pdo->query("SELECT * FROM productos ORDER BY id_producto DESC");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $productos = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Moon Essence</title>
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
            text-decoration: none;
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
        .img-tabla {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #28374d;
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

<!-- Barra de navegación unificada -->
<nav class="navbar navbar-expand-lg navbar-moon sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3 brand-text" href="admin_dashboard.php">
            <i class="bi bi-moon-stars-fill me-2"></i>Moon Essence <small class="fs-6 text-muted-moon">| Admin</small>
        </a>
        <div class="d-flex align-items-center gap-2">
            <a href="admin_dashboard.php" class="btn btn-sm btn-outline-light"><i class="bi bi-speedometer2 me-1"></i> Panel</a>
            <a href="admin_categorias.php" class="btn btn-sm btn-outline-light"><i class="bi bi-tags me-1"></i> Categorías</a>
            <a href="../index.php" class="btn btn-sm btn-beigecito"><i class="bi bi-shop me-1"></i> Ver Tienda</a>
            <a href="../auth/logout.php" class="btn btn-sm btn-danger"><i class="bi bi-box-arrow-right"></i> Salir</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="card card-admin p-4 shadow-lg">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="brand-text mb-1"><i class="bi bi-box-seam me-2"></i>Gestión de Productos</h3>
                <p class="text-muted-moon small m-0">Inventario y catálogo de prendas.</p>
            </div>
            <a href="crear_producto.php" class="btn btn-beigecito"><i class="bi bi-plus-lg me-1"></i> Nuevo Producto</a>
        </div>

        <?php if (empty($productos)): ?>
            <div class="text-center py-5">
                <i class="bi bi-box2 fs-1 text-muted-moon"></i>
                <p class="text-muted-moon mt-3">No hay productos registrados.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-moon align-middle">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $prod): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($prod['imagen'])): ?>
                                        <img src="../uploads/<?= htmlspecialchars($prod['imagen']) ?>" alt="Producto" class="img-tabla">
                                    <?php else: ?>
                                        <span class="text-muted-moon small">Sin foto</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-warning">#<?= $prod['id_producto'] ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($prod['nombre_producto']) ?></td>
                                <td class="text-success fw-bold">$<?= number_format($prod['precio'], 2, ',', '.') ?></td>
                                <td><span class="badge bg-info text-dark"><?= $prod['stock'] ?> un.</span></td>
                                <td class="text-end">
                                    <a href="editar_producto.php?id=<?= $prod['id_producto'] ?>" class="btn btn-sm btn-outline-warning me-1"><i class="bi bi-pencil"></i></a>
                                    <a href="eliminar_producto.php?id=<?= $prod['id_producto'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro de eliminar este producto?');"><i class="bi bi-trash"></i></a>
                                </td>
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