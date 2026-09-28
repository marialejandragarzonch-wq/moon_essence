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

// Capturar la categoría seleccionada por la URL de forma limpia
$cat_seleccionada = $_GET['cat'] ?? 'todas';
$cat_limpia = trim(strtolower($cat_seleccionada));

// Obtener todos los productos de la base de datos con su categoría
$productos = [];
try {
    $stmt = $pdo->query("SELECT p.*, c.nombre_categoria FROM productos p INNER JOIN categorias c ON p.id_categoria = c.id_categoria ORDER BY p.id_producto DESC");
    $productos_totales = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $productos_totales = [];
}

// Filtrar los productos en PHP según el botón que se presionó
if ($cat_limpia === 'todas' || empty($cat_limpia)) {
    $productos = $productos_totales;
} else {
    $productos = [];
    foreach ($productos_totales as $prod) {
        $nombre_cat_db = trim(strtolower($prod['nombre_categoria'] ?? ''));
        if ($nombre_cat_db === $cat_limpia) {
            $productos[] = $prod;
        }
    }
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
        <p class="text-muted-moon lead">Explora colecciones exclusivas diseñadas para realzar tu elegancia.</p>
    </div>
</div>

<!-- Menú de Categorías -->
<div class="container mt-4">
    <div class="d-flex flex-wrap gap-2 justify-content-center">
        <a href="index.php?cat=todas" class="btn btn-sm <?= ($cat_limpia === 'todas') ? 'btn-beigecito' : 'btn-outline-secondary text-white' ?>">Todas</a>
        <a href="index.php?cat=chaquetas" class="btn btn-sm <?= ($cat_limpia === 'chaquetas') ? 'btn-beigecito' : 'btn-outline-secondary text-white' ?>">Chaquetas</a>
        <a href="index.php?cat=faldas" class="btn btn-sm <?= ($cat_limpia === 'faldas') ? 'btn-beigecito' : 'btn-outline-secondary text-white' ?>">Faldas</a>
        <a href="index.php?cat=básicos" class="btn btn-sm <?= ($cat_limpia === 'básicos' || $cat_limpia === 'basicos') ? 'btn-beigecito' : 'btn-outline-secondary text-white' ?>">Básicos</a>
        <a href="index.php?cat=sastre" class="btn btn-sm <?= ($cat_limpia === 'sastre') ? 'btn-beigecito' : 'btn-outline-secondary text-white' ?>">Sastre</a>
        <a href="index.php?cat=calzado" class="btn btn-sm <?= ($cat_limpia === 'calzado') ? 'btn-beigecito' : 'btn-outline-secondary text-white' ?>">Calzado</a>
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
            <p class="text-muted-moon mt-3">No hay productos en esta categoría por el momento.</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($productos as $index_prod => $prod): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card card-producto h-100 shadow-sm">
                        <?>
                        <?php 
                            $img_db = trim($prod['imagen'] ?? '');
                            $primera_imagen = "";
                            $segunda_imagen = "";

                            // Replicando la lógica de búsqueda robusta por prefijo numérico de la base de datos
                            preg_match('/^(\d+)/', $img_db, $matches);
                            if (!empty($matches[1])) {
                                $prefijo = $matches[1];
                                $archivos = scandir('uploads');
                                foreach ($archivos as $archivo) {
                                    if ($archivo === '.' || $archivo === '..') continue;
                                    if (strpos($archivo, $prefijo) === 0) {
                                        if (strpos($archivo, '_1') !== false || (strpos($archivo, '_2') === false && empty($primera_imagen))) {
                                            if (empty($primera_imagen)) {
                                                $primera_imagen = "uploads/" . $archivo;
                                            }
                                        }
                                        if (strpos($archivo, '_2') !== false || strpos($archivo, ' 2') !== false) {
                                            $segunda_imagen = "uploads/" . $archivo;
                                        }
                                    }
                                }
                            }

                            // Fallbacks por si acaso
                            if (empty($primera_imagen) && !empty($img_db) && file_exists("uploads/" . $img_db)) {
                                $primera_imagen = "uploads/" . $img_db;
                            }
                            
                            $carousel_id = "carouselProd" . $index_prod;
                        ?>

                        <!-- Carrusel Bootstrap para alternar entre la foto 1 y la foto 2 de forma fluida -->
                        <?php if (!empty($primera_imagen) && !empty($segunda_imagen)): ?>
                            <div id="<?= $carousel_id ?>" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img src="<?= htmlspecialchars($primera_imagen) ?>" class="img-catalogo" alt="Foto 1">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="<?= htmlspecialchars($segunda_imagen) ?>" class="img-catalogo" alt="Foto 2">
                                    </div>
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#<?= $carousel_id ?>" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Anterior</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#<?= $carousel_id ?>" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Siguiente</span>
                                </button>
                            </div>
                        <?php elseif (!empty($primera_imagen)): ?>
                            <img src="<?= htmlspecialchars($primera_imagen) ?>" alt="Prenda" class="img-catalogo">
                        <?php else: ?>
                            <div class="img-catalogo bg-dark d-flex align-items-center justify-content-center text-muted-moon">Sin imagen</div>
                        <?php endif; ?>
                        
                        <div class="card-body d-flex flex-column">
                            <span class="text-muted-moon small mb-1"><?= htmlspecialchars($prod['nombre_categoria'] ?? 'MOON STORE') ?></span>
                            <h5 class="card-title fw-bold text-white mb-2"><?= htmlspecialchars($prod['nombre_producto'] ?? '') ?></h5>
                            <p class="text-success fw-bold fs-5 mb-2">$<?= number_format($prod['precio'] ?? 0, 2, ',', '.') ?></p>
                            
                            <?php 
                                $nombreProd = strtolower($prod['nombre_producto'] ?? '');
                                $nombreCat = strtolower($prod['nombre_categoria'] ?? '');
                                $esCalzado = (strpos($nombreCat, 'calzado') !== false || strpos($nombreProd, 'tenis') !== false || strpos($nombreProd, 'bota') !== false || strpos($nombreProd, 'zapatilla') !== false || strpos($nombreProd, 'zapato') !== false);
                            ?>

                            <!-- Selector de Tallas Desplegable -->
                            <form action="carrito/agregar.php" method="POST" class="mt-auto">
                                <input type="hidden" name="id_producto" value="<?= $prod['id_producto'] ?>">
                                
                                <div class="mb-3">
                                    <label class="text-muted-moon small mb-1">Selecciona la Talla:</label>
                                    <select name="talla" class="form-select form-select-sm bg-dark text-white border-secondary" required>
                                        <option value="" selected disabled>Elige una talla</option>
                                        <?php 
                                            if ($esCalzado) {
                                                $tallas = ['35', '36', '37', '38', '39', '40', '41'];
                                            } else {
                                                $tallas = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
                                            }
                                            
                                            foreach ($tallas as $t) {
                                                echo "<option value=\"$t\">Talla $t</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-beigecito w-100 btn-sm py-2">
                                    <i class="bi bi-cart-plus me-1"></i> Agregar al Carrito
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