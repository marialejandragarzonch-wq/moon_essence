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
    <title>Moon Essence - Encuentra tu Estilo</title>
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
        .hero-section {
            background: linear-gradient(rgba(11, 19, 30, 0.7), rgba(11, 19, 30, 0.9)), url('uploads/hero-bg.jpg') center/cover;
            padding: 80px 0;
            border-bottom: 1px solid #233044;
            text-align: center;
        }
        .card-producto {
            background-color: var(--bg-tarjeta);
            border: 1px solid #28374d;
            border-radius: 14px;
            transition: transform 0.2s ease;
        }
        .card-producto:hover {
            transform: translateY(-5px);
        }
        .img-catalogo {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
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

<!-- Navbar de la Tienda -->
<nav class="navbar navbar-expand-lg navbar-moon sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3 brand-text" href="index.php">
            <i class="bi bi-moon-stars-fill me-2"></i>Moon Essence
        </a>
        <div class="d-flex align-items-center gap-3">
            <a href="auth/login.php" class="btn btn-sm btn-outline-light">Iniciar Sesión</a>
            <a href="auth/registro.php" class="btn btn-sm btn-beigecito">Registrarse</a>
            <a href="carrito/ver.php" class="btn btn-sm btn-outline-warning"><i class="bi bi-cart-fill me-1"></i> Carrito</a>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <h1 class="display-5 brand-text fw-bold mb-3">Define Tu Estilo</h1>
        <p class="text-muted-moon lead">Explora colecciones exclusivas de calzado y prendas para ti.</p>
    </div>
</div>

<!-- Catálogo de Productos -->
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="brand-text m-0">Colección Completa</h3>
        <span class="text-muted-moon small"><?= count($productos) ?> Prendas disponibles</span>
    </div>

    <?php if (empty($productos)): ?>
        <div class="text-center py-5">
            <i class="bi bi-shop fs-1 text-muted-moon"></i>
            <p class="text-muted-moon mt-3">No hay productos disponibles por el momento.</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($productos as $prod): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card card-producto h-100 shadow-sm">
                        <?php if (!empty($prod['imagen'])): ?>
                            <img src="uploads/<?= htmlspecialchars($prod['imagen']) ?>" alt="Prenda" class="img-catalogo">
                        <?php else: ?>
                            <div class="img-catalogo bg-dark d-flex align-items-center justify-content-center text-muted-moon">Sin imagen</div>
                        <?php endif; ?>
                        
                        <div class="card-body d-flex flex-column">
                            <span class="text-muted-moon small mb-1">MOON STORE OFICIAL</span>
                            <h5 class="card-title fw-bold text-white mb-2"><?= htmlspecialchars($prod['nombre_producto'] ?? '') ?></h5>
                            <p class="text-success fw-bold fs-5 mb-2">$<?= number_format($prod['precio'] ?? 0, 2, ',', '.') ?></p>
                            
                            <?php 
                                $nombreProd = strtolower($prod['nombre_producto'] ?? '');
                                $esCalzado = (strpos($nombreProd, 'tenis') !== false || strpos($nombreProd, 'bota') !== false || strpos($nombreProd, 'zapatilla') !== false || strpos($nombreProd, 'mary jane') !== false);
                            ?>

                            <!-- Visualización limpia de la talla en la tarjeta (sin selectores) -->
                            <div class="mb-3">
                                <span class="text-muted-moon small">Talla:</span>
                                <span class="badge bg-secondary text-white ms-1 px-2 py-1">
                                    <?= $esCalzado ? '35 - 41' : 'XS - XXL' ?>
                                </span>
                            </div>

                            <!-- Botón de compra directo -->
                            <form action="carrito/agregar.php" method="POST" class="mt-auto">
                                <input type="hidden" name="id_producto" value="<?= $prod['id_producto'] ?>">
                                <input type="hidden" name="talla" value="<?= $esCalzado ? '36' : 'M' ?>">
                                
                                <button type="submit" class="btn btn-beigecito w-100 btn-sm py-2">
                                    <i class="bi bi-cart-plus me-1"></i> Comprar / Agregar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
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