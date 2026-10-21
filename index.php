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

// Obtener categorías con imagen representativa para el menú superior
try {
    $stmt_cat = $pdo->query("
        SELECT c.*, 
               (SELECT imagen FROM productos p WHERE p.id_categoria = c.id_categoria AND p.imagen IS NOT NULL AND p.imagen != '' LIMIT 1) as foto_representativa
        FROM categorias c 
        ORDER BY c.nombre_categoria ASC
    ");
    $categorias = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $categorias = [];
}

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
    <title>Moon Essence - Tienda Oficial</title>
    
    <!-- Fuentes Elegantes Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --bg-principal: #0b131e;        /* Azul nocturno original */
            --bg-darker: #05090e;           /* Azul más oscuro para nav/footer */
            --azul-claro: #c4dafa;          /* Azul pastel de acento */
            --accent-beige: #f5e6d3;        /* Beige botón carrito original */
            --verde-precio: #2ecc71;        /* Verde precio original */
            --card-bg: #111a26;             /* Tarjeta fondo oscuro original */
            --border-card: #1c2a3a;
            --font-editorial: 'Playfair Display', serif;
            --font-sans: 'Montserrat', sans-serif;
        }

        body {
            background-color: var(--bg-principal);
            color: #ffffff;
            font-family: var(--font-sans);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        h1, h2, h3, h4, .brand-logo, .footer-title, .cat-card-header {
            font-family: var(--font-editorial);
        }

        /* Banner Promocional Descuento */
        .promo-bar {
            background-color: var(--azul-claro);
            color: #0b131e;
            font-size: 0.8rem;
            font-weight: 700;
            text-align: center;
            padding: 6px 0;
            letter-spacing: 1px;
        }

        /* Navegación Estilo Koaj */
        .navbar-koaj {
            background-color: var(--bg-darker);
            border-bottom: 1px solid rgba(196, 218, 250, 0.2);
            padding: 12px 0;
        }

        .brand-logo {
            font-weight: 700;
            font-size: 1.6rem;
            color: #ffffff !important;
            letter-spacing: 2px;
            text-decoration: none;
        }

        .nav-link-koaj {
            color: #ffffff !important;
            font-weight: 600;
            font-size: 0.88rem;
            letter-spacing: 1px;
            padding: 8px 14px !important;
            transition: color 0.2s ease;
        }

        .nav-link-koaj:hover {
            color: var(--azul-claro) !important;
        }

        .icon-btn {
            color: #ffffff;
            font-size: 1.25rem;
            text-decoration: none;
            position: relative;
            padding: 4px 8px;
            transition: color 0.2s ease;
        }

        .icon-btn:hover {
            color: var(--azul-claro);
        }

        /* Megamenú Dropdown */
        .dropdown-menu-custom {
            background-color: var(--bg-darker);
            border: 1px solid var(--azul-claro);
            border-radius: 8px;
            padding: 12px;
        }

        .dropdown-item-custom {
            color: #ffffff;
            padding: 6px 16px;
            font-size: 0.85rem;
            text-decoration: none;
            display: block;
            border-radius: 4px;
        }

        .dropdown-item-custom:hover {
            background-color: rgba(196, 218, 250, 0.15);
            color: var(--azul-claro);
        }

        /* TARJETAS DE CATEGORÍA SUPERIORES */
        .cat-card-frame {
            border: 3px solid var(--azul-claro);
            background-color: #05090e;
            border-radius: 6px;
            position: relative;
            overflow: hidden;
            height: 360px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
            transition: transform 0.25s ease;
            text-decoration: none;
        }

        .cat-card-frame:hover {
            transform: translateY(-5px);
        }

        .cat-card-header {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 3;
            background: linear-gradient(180deg, rgba(11, 19, 30, 0.95) 0%, rgba(11, 19, 30, 0) 100%);
            padding: 14px 12px;
            color: var(--azul-claro);
            font-weight: 700;
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .cat-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
        }

        .cat-card-footer {
            position: absolute;
            bottom: 12px;
            left: 12px;
            right: 12px;
            z-index: 3;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .price-badge-blue {
            background-color: var(--azul-claro);
            color: #0b131e;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 6px 14px;
            border-radius: 4px;
        }

        .btn-plus-blue {
            background-color: var(--azul-claro);
            color: #0b131e;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            font-weight: bold;
            border: none;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            transition: all 0.2s ease;
        }

        .btn-plus-blue:hover {
            background-color: #ffffff;
            color: var(--bg-principal);
            transform: scale(1.1);
        }

        /* TARJETAS DE PRODUCTO CON SELECTOR DE TALLAS Y BOTÓN BEIGE */
        .card-product-original {
            background-color: var(--card-bg);
            border: 1px solid var(--border-card);
            border-radius: 12px;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: transform 0.25s ease;
        }

        .card-product-original:hover {
            transform: translateY(-4px);
        }

        .card-img-container {
            height: 300px;
            position: relative;
            overflow: hidden;
            width: 100%;
            background-color: #081018;
        }

        .card-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .card-img-container .img-principal {
            position: relative;
            z-index: 1;
        }

        .card-img-container .img-secundaria {
            position: absolute;
            top: 0;
            left: 0;
            z-index: 2;
            opacity: 0;
            transition: opacity 0.35s ease-in-out;
        }

        .card-img-container:hover .img-secundaria {
            opacity: 1;
        }

        .card-body-original {
            padding: 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex-grow: 1;
        }

        .prod-cat-title {
            color: #8fa0b5;
            font-size: 0.8rem;
            margin-bottom: 4px;
        }

        .prod-name {
            color: #ffffff;
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .prod-price {
            color: var(--verde-precio);
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .select-talla-label {
            color: #8fa0b5;
            font-size: 0.75rem;
            margin-bottom: 4px;
            display: block;
        }

        .select-talla {
            background-color: #0a121d;
            color: #ffffff;
            border: 1px solid #223347;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 0.85rem;
            width: 100%;
            margin-bottom: 12px;
        }

        .btn-add-cart-original {
            background-color: var(--accent-beige);
            color: #0b131e;
            font-weight: 700;
            border: none;
            border-radius: 6px;
            padding: 10px;
            width: 100%;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: opacity 0.2s ease;
        }

        .btn-add-cart-original:hover {
            opacity: 0.9;
        }

        /* FOOTER COMERCIAL */
        footer {
            background-color: var(--bg-darker);
            border-top: 1px solid rgba(196, 218, 250, 0.2);
            color: #ffffff;
            margin-top: auto;
            padding-top: 40px;
            padding-bottom: 20px;
        }

        .footer-title {
            color: var(--azul-claro);
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 14px;
            letter-spacing: 1px;
        }

        .footer-link {
            color: #ffffff;
            text-decoration: none;
            font-size: 0.85rem;
            display: block;
            margin-bottom: 8px;
            transition: color 0.2s ease;
        }

        .footer-link:hover {
            color: var(--azul-claro);
        }

        .payment-icons i {
            font-size: 1.8rem;
            color: var(--azul-claro);
            margin-right: 12px;
        }
    </style>
</head>
<body>

<!-- Banner Promocional Descuento -->
<div class="promo-bar">
    <i class="bi bi-tag-fill me-1"></i> ¡OBTÉN UN 20% DE DESCUENTO AL REGISTRARTE O FORMAR PARTE DE NUESTRA COMUNIDAD!
</div>

<!-- Navegación Estilo Koaj -->
<nav class="navbar navbar-expand-lg navbar-koaj sticky-top">
    <div class="container">
        
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center me-4" href="index.php">
            <i class="bi bi-moon-stars-fill me-2" style="color: var(--azul-claro);"></i>
            <span class="brand-logo">MOON ESSENCE</span>
        </a>

        <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navKoaj">
            <i class="bi bi-list fs-2"></i>
        </button>

        <div class="collapse navbar-collapse" id="navKoaj">
            
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link nav-link-koaj" href="index.php">
                        <i class="bi bi-house-door me-1"></i> INICIO
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link nav-link-koaj dropdown-toggle" href="#" id="dropMujer" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        MUJER
                    </a>
                    <ul class="dropdown-menu dropdown-menu-custom" aria-labelledby="dropMujer">
                        <li><a class="dropdown-item-custom fw-bold" href="index.php?cat=todas">Ver Todo</a></li>
                        <li><hr class="dropdown-divider border-secondary"></li>
                        <?php foreach ($categorias as $cat): ?>
                            <li>
                                <a class="dropdown-item-custom" href="index.php?cat=<?= strtolower($cat['nombre_categoria']) ?>">
                                    <?= htmlspecialchars($cat['nombre_categoria']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link nav-link-koaj" href="quienes_somos.php">QUIÉNES SOMOS</a>
                </li>
            </ul>

            <!-- Iconos de Navegación Derechos -->
            <div class="d-flex align-items-center gap-3">
                
                <a href="index.php" class="icon-btn" title="Buscar productos">
                    <i class="bi bi-search"></i>
                </a>

                <a href="#" class="icon-btn" data-bs-toggle="modal" data-bs-target="#modalFavoritos" title="Mis Favoritos">
                    <i class="bi bi-heart"></i>
                </a>

                <a href="carrito/ver.php" class="icon-btn" title="Carrito de Compras">
                    <i class="bi bi-bag"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                        0
                    </span>
                </a>

                <a href="auth/login.php" class="btn btn-sm px-3 fw-bold ms-2" style="background-color: var(--azul-claro); color: #0b131e;">
                    <i class="bi bi-person-circle me-1"></i> Ingresar
                </a>

            </div>

        </div>
    </div>
</nav>

<main class="container my-5">

    <!-- SECCIÓN: CATEGORÍas PRINCIPALES (SÓLO SE MUESTRA EN EL INICIO / "TODAS") -->
    <?php if ($cat_limpia === 'todas' || empty($cat_limpia)): ?>
        <div class="mb-5">
            <h3 class="fw-bold mb-4 d-flex align-items-center" style="color: var(--azul-claro);">
                <i class="bi bi-grid-fill me-2"></i> CATEGORÍAS PRINCIPALES
            </h3>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3">
                <?php if (count($categorias) > 0): ?>
                    <?php foreach ($categorias as $c): ?>
                        <?php 
                            $nombre_cat = $c['nombre_categoria'] ?? '';
                            $foto_cat = !empty($c['foto_representativa']) 
                                        ? 'uploads/' . $c['foto_representativa'] 
                                        : 'https://via.placeholder.com/300x400/05090e/c4dafa?text=' . urlencode($nombre_cat);
                        ?>
                        <div class="col">
                            <a href="index.php?cat=<?= strtolower($nombre_cat) ?>" class="cat-card-frame">
                                <div class="cat-card-header">
                                    <?= htmlspecialchars($nombre_cat) ?> ✦
                                </div>

                                <img src="<?= htmlspecialchars($foto_cat) ?>" class="cat-card-img" alt="<?= htmlspecialchars($nombre_cat) ?>" onerror="this.src='uploads/default.png'">

                                <div class="cat-card-footer">
                                    <span class="price-badge-blue">Ver Prendas</span>
                                    <span class="btn-plus-blue">+</span>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p class="text-white-50">No hay categorías registradas en la base de datos.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- SECCIÓN: CATÁLOGO DE PRODUCTOS -->
    <div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0" style="color: #ffffff;">
                <?= ($cat_limpia !== 'todas' && !empty($cat_limpia)) ? 'Productos Filtrados: ' . ucfirst($cat_limpia) : 'Colección Completa' ?>
            </h3>
            <?php if ($cat_limpia !== 'todas' && !empty($cat_limpia)): ?>
                <a href="index.php?cat=todas" class="btn btn-sm px-3 fw-bold" style="background-color: var(--azul-claro); color: #0b131e;">
                    <i class="bi bi-arrow-left me-1"></i> Volver a todas las categorías
                </a>
            <?php endif; ?>
        </div>

        <?php if (empty($productos)): ?>
            <div class="text-center py-5">
                <i class="bi bi-box-seam fs-1 text-white-50 mb-3 d-block"></i>
                <h4 class="fw-bold">No se encontraron productos en esta categoría</h4>
                <a href="index.php?cat=todas" class="btn btn-sm mt-3 px-4 fw-bold" style="background-color: var(--azul-claro); color: #0b131e;">Ver catálogo general</a>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php foreach ($productos as $prod): ?>
                    <?php
                        // Lógica exacta de búsqueda de imágenes múltiples para hover
                        $img_db = trim($prod['imagen'] ?? '');
                        $primera_imagen = "";
                        $segunda_imagen = "";

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

                        if (empty($primera_imagen) && !empty($img_db) && file_exists("uploads/" . $img_db)) {
                            $primera_imagen = "uploads/" . $img_db;
                        }
                        if (empty($segunda_imagen)) {
                            $segunda_imagen = $primera_imagen;
                        }

                        $nombre_prod = $prod['nombre_producto'] ?? '';
                        $cat_nombre  = $prod['nombre_categoria'] ?? 'Prenda';
                        $precio_prod = $prod['precio'] ?? 0;
                    ?>
                    <div class="col">
                        <div class="card-product-original">
                            
                            <!-- Contenedor de fotos con animación de hover -->
                            <div class="card-img-container">
                                <img src="<?= htmlspecialchars($primera_imagen) ?>" class="img-principal" alt="Foto 1" onerror="this.src='uploads/default.png'">
                                <img src="<?= htmlspecialchars($segunda_imagen) ?>" class="img-secundaria" alt="Foto 2" onerror="this.src='uploads/default.png'">
                            </div>

                            <!-- Cuerpo de la tarjeta con tallas y botón beige -->
                            <div class="card-body-original">
                                <div>
                                    <div class="prod-cat-title"><?= htmlspecialchars($cat_nombre) ?></div>
                                    <div class="prod-name"><?= htmlspecialchars($nombre_prod) ?></div>
                                    <div class="prod-price">$<?= number_format($precio_prod, 2, ',', '.') ?></div>
                                </div>

                                <?php 
                                    $nombreProdLower = strtolower($nombre_prod);
                                    $nombreCatLower = strtolower($cat_nombre);
                                    $esCalzado = (strpos($nombreCatLower, 'calzado') !== false || strpos($nombreProdLower, 'tenis') !== false || strpos($nombreProdLower, 'bota') !== false || strpos($nombreProdLower, 'zapatilla') !== false || strpos($nombreProdLower, 'zapato') !== false);
                                ?>

                                <form action="carrito/agregar.php" method="POST">
                                    <input type="hidden" name="id_producto" value="<?= $prod['id_producto'] ?>">
                                    
                                    <label class="select-talla-label">Selecciona la Talla:</label>
                                    <select name="talla" class="select-talla" required>
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

                                    <button type="submit" class="btn-add-cart-original">
                                        <i class="bi bi-cart3"></i> Agregar al Carrito
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</main>

<!-- PIE DE PÁGINA (FOOTER) COMERCIAL -->
<footer>
    <div class="container">
        <div class="row g-4 mb-4">
            
            <div class="col-md-4">
                <h5 class="footer-title">MOON ESSENCE</h5>
                <p style="font-size: 0.85rem; color: #cbd5e1; line-height: 1.6;">
                    Diseñamos prendas con la esencia de la elegancia y versatilidad nocturna. Calidad premium para expresar tu autenticidad.
                </p>
                <a href="quienes_somos.php" class="footer-link fw-bold">
                    <i class="bi bi-arrow-right-circle me-1"></i> Conoce más en Quiénes Somos
                </a>
            </div>

            <div class="col-md-4">
                <h5 class="footer-title">AYUDA Y POLÍTICAS</h5>
                <a href="#" class="footer-link" data-bs-toggle="modal" data-bs-target="#modalTratamientoDatos">
                    <i class="bi bi-shield-check me-1"></i> Política de tratamiento de datos
                </a>
                <a href="quienes_somos.php" class="footer-link">
                    <i class="bi bi-info-circle me-1"></i> Términos y condiciones
                </a>
            </div>

            <div class="col-md-4">
                <h5 class="footer-title">CONTACTO</h5>
                <p class="mb-2" style="font-size: 0.85rem; color: #cbd5e1;">
                    <i class="bi bi-envelope-fill me-2" style="color: var(--azul-claro);"></i>
                    contacto@moonessence.com
                </p>
                
                <h6 class="footer-title mt-4 mb-2">MÉTODOS DE PAGO</h6>
                <div class="payment-icons">
                    <i class="bi bi-credit-card-fill" title="Tarjeta de Crédito"></i>
                    <i class="bi bi-paypal" title="PayPal"></i>
                    <i class="bi bi-cash-stack" title="Efectivo / Transferencia"></i>
                </div>
            </div>

        </div>

        <hr style="border-color: rgba(196, 218, 250, 0.2);">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 pt-2">
            <p class="m-0 small text-white-50">
                © 2026 MOON ESSENCE. Todos los derechos reservados.
            </p>
            <div class="d-flex gap-3 fs-5" style="color: var(--azul-claro);">
                <a href="#" class="text-decoration-none text-reset"><i class="bi bi-instagram"></i></a>
                <a href="#" class="text-decoration-none text-reset"><i class="bi bi-facebook"></i></a>
                <a href="#" class="text-decoration-none text-reset"><i class="bi bi-tiktok"></i></a>
            </div>
        </div>
    </div>
</footer>

<!-- Modal de Favoritos -->
<div class="modal fade" id="modalFavoritos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: var(--bg-darker); border: 1px solid var(--azul-claro); color: #ffffff; border-radius: 12px;">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold" style="color: var(--azul-claro);">
                    <i class="bi bi-heart-fill me-2"></i> Mis Favoritos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-person-lock fs-1 mb-3 d-block" style="color: var(--azul-claro);"></i>
                <h5 class="fw-bold">Inicia sesión para guardar tus prendas</h5>
                <p class="text-white-50" style="font-size: 0.85rem;">
                    Debes tener una cuenta en Moon Essence para agregar productos a tu lista de favoritos.
                </p>
                <a href="auth/login.php" class="btn fw-bold mt-2 px-4" style="background-color: var(--azul-claro); color: #0b131e;">
                    Iniciar Sesión / Registrarse
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Tratamiento de Datos -->
<div class="modal fade" id="modalTratamientoDatos" tabindex="-1" aria-labelledby="modalTratamientoDatosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: var(--bg-darker); border: 1px solid var(--azul-claro); color: #ffffff; border-radius: 12px;">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold" id="modalTratamientoDatosLabel" style="color: var(--azul-claro);">
                    <i class="bi bi-shield-check me-2"></i> Política de Tratamiento de Datos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-start">
                <p class="m-0" style="color: #cbd5e1; line-height: 1.6; font-size: 0.9rem;">
                    El presente proyecto tiene fines exclusivamente académicos. Los datos utilizados durante el desarrollo, las pruebas y la demostración del sistema son ficticios y fueron creados únicamente con fines de simulación. El sistema no recolecta, almacena ni procesa datos personales reales de estudiantes, docentes o terceros.
                </p>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-sm px-4 fw-bold" style="background-color: var(--azul-claro); color: #0b131e;" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>