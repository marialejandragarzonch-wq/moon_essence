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

$estilo_seleccionado = $_GET['estilo'] ?? 'todos';
$busqueda = trim($_GET['buscar'] ?? '');

// Consulta Base con JOIN a Categorías y Tiendas
$sql = "
    SELECT p.*, t.nombre_tienda, c.nombre AS categoria_nombre,
           (SELECT ruta_foto FROM fotos_producto fp WHERE fp.id_producto = p.id_producto LIMIT 1) AS imagen2
    FROM productos p
    LEFT JOIN tiendas t ON p.id_tienda = t.id_tienda
    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
    WHERE 1=1
";

$params = [];

// Filtro por Estilos
if ($estilo_seleccionado !== 'todos') {
    switch ($estilo_seleccionado) {
        case 'denim':
            $sql .= " AND (LOWER(p.nombre_producto) LIKE ? OR LOWER(p.descripcion) LIKE ? OR LOWER(c.nombre) LIKE ?)";
            array_push($params, '%denim%', '%abrigo%', '%chaqueta%');
            break;
            
        case 'sastre':
            $sql .= " AND (LOWER(p.nombre_producto) LIKE ? OR LOWER(p.descripcion) LIKE ? OR LOWER(c.nombre) LIKE ? OR LOWER(p.nombre_producto) LIKE ?)";
            array_push($params, '%sastre%', '%blazer%', '%elegante%', '%chaleco%');
            break;
            
        case 'falda':
            $sql .= " AND (LOWER(p.nombre_producto) LIKE ? OR LOWER(p.descripcion) LIKE ? OR LOWER(c.nombre) LIKE ?)";
            array_push($params, '%falda%', '%chic%', '%minifalda%');
            break;
            
        case 'casual':
            $sql .= " AND (LOWER(p.nombre_producto) LIKE ? OR LOWER(p.descripcion) LIKE ? OR LOWER(c.nombre) LIKE ? OR LOWER(p.nombre_producto) LIKE ? OR LOWER(p.nombre_producto) LIKE ? OR LOWER(p.nombre_producto) LIKE ? OR LOWER(p.nombre_producto) LIKE ?)";
            array_push($params, '%camisa%', '%camiseta%', '%básica%', '%top%', '%buzo%', '%tenis%', '%mary jane%');
            break;
    }
}

// Filtro por Buscador Manual
if ($busqueda !== '') {
    $sql .= " AND (LOWER(p.nombre_producto) LIKE ? OR LOWER(p.descripcion) LIKE ? OR LOWER(t.nombre_tienda) LIKE ?)";
    $param_busqueda = "%" . strtolower($busqueda) . "%";
    array_push($params, $param_busqueda, $param_busqueda, $param_busqueda);
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moon Essence - Encuentra tu Estilo</title>
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

        /* Hero Banner con Buscador */
        .hero-banner {
            background: linear-gradient(rgba(11, 19, 30, 0.75), rgba(11, 19, 30, 0.95)), 
                        url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat;
            padding: 70px 0 45px 0;
            border-bottom: 1px solid #233044;
        }

        .hero-title {
            font-size: 2.8rem;
            font-weight: 300;
            color: var(--accent-luna);
            letter-spacing: 2px;
        }

        .search-input {
            background-color: rgba(5, 9, 14, 0.8) !important;
            border: 1px solid #28374d !important;
            color: #ffffff !important;
            border-radius: 30px;
            padding: 12px 25px;
            font-size: 0.95rem;
        }

        .search-input:focus {
            border-color: var(--accent-luna) !important;
            box-shadow: 0 0 10px rgba(249, 232, 208, 0.3);
        }

        /* Menú de Estilos */
        .style-nav-container {
            background-color: #05090e;
            border-bottom: 1px solid #28374d;
            padding: 14px 0;
        }

        .btn-style-filter {
            color: var(--texto-suave);
            border: 1px solid #28374d;
            border-radius: 25px;
            padding: 8px 20px;
            margin: 0 4px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            font-size: 0.9rem;
        }

        .btn-style-filter:hover, .btn-style-filter.active {
            background-color: var(--accent-luna);
            color: #0b131e;
            border-color: var(--accent-luna);
            font-weight: 600;
            box-shadow: 0 0 10px rgba(249, 232, 208, 0.3);
        }

        /* Tarjetas de Producto */
        .card-product {
            background-color: var(--bg-tarjeta);
            border: 1px solid #28374d;
            border-radius: 14px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card-product:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.5), 0 0 15px rgba(249, 232, 208, 0.1);
        }

        .card-img-container {
            height: 360px;
            position: relative;
            overflow: hidden;
            width: 100%;
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
            transition: opacity 0.3s ease-in-out;
        }

        .card-img-container:hover .img-secundaria {
            opacity: 1;
        }

        .badge-trend {
            position: absolute;
            top: 12px;
            left: 12px;
            z-index: 3;
            background-color: var(--accent-luna);
            color: #0b131e;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .price-tag {
            color: var(--accent-luna);
            font-size: 1.25rem;
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
            <a href="index.php" class="btn btn-sm btn-outline-moon">Tienda</a>
            <a href="login.php" class="btn btn-sm btn-outline-moon">Iniciar Sesión</a>
            <a href="registro.php" class="btn btn-sm btn-moon">Registrarse</a>
            <a href="admin_dashboard.php" class="btn btn-sm btn-outline-moon">Panel Admin</a>
            <a href="checkout.php" class="btn btn-moon px-3 ms-2">Carrito (0)</a>
        </div>
    </div>
</nav>

<!-- Hero Banner -->
<div class="hero-banner text-center">
    <div class="container">
        <h1 class="hero-title mb-2">Define Tu Estilo</h1>
        <p class="text-muted-moon fs-5 mb-4">Explora colecciones exclusivas o busca la prenda exacta que deseas.</p>
        
        <form action="index.php" method="GET" class="row justify-content-center">
            <?php if ($estilo_seleccionado !== 'todos'): ?>
                <input type="hidden" name="estilo" value="<?= htmlspecialchars($estilo_seleccionado) ?>">
            <?php endif; ?>
            <div class="col-md-6 col-lg-5">
                <input type="text" name="buscar" class="form-control search-input" 
                       placeholder="Escribe lo que buscas (ej. Chaqueta, Falda)..." 
                       value="<?= htmlspecialchars($busqueda) ?>">
            </div>
        </form>
    </div>
</div>

<!-- Menú 2: Navegación por Estilos -->
<div class="style-nav-container text-center sticky-top" style="top: 71px; z-index: 999;">
    <div class="container">
        <span class="text-white me-2 fw-bold small text-uppercase" style="letter-spacing: 1px;">Estilos:</span>
        <a href="index.php?estilo=todos<?= $busqueda !== '' ? '&buscar='.urlencode($busqueda) : '' ?>" class="btn-style-filter <?= $estilo_seleccionado === 'todos' ? 'active' : '' ?>">Ver Todos</a>
        <a href="index.php?estilo=denim<?= $busqueda !== '' ? '&buscar='.urlencode($busqueda) : '' ?>" class="btn-style-filter <?= $estilo_seleccionado === 'denim' ? 'active' : '' ?>">Denim / Abrigos</a>
        <a href="index.php?estilo=sastre<?= $busqueda !== '' ? '&buscar='.urlencode($busqueda) : '' ?>" class="btn-style-filter <?= $estilo_seleccionado === 'sastre' ? 'active' : '' ?>">Sastre / Elegante</a>
        <a href="index.php?estilo=falda<?= $busqueda !== '' ? '&buscar='.urlencode($busqueda) : '' ?>" class="btn-style-filter <?= $estilo_seleccionado === 'falda' ? 'active' : '' ?>">Chic / Faldas</a>
        <a href="index.php?estilo=casual<?= $busqueda !== '' ? '&buscar='.urlencode($busqueda) : '' ?>" class="btn-style-filter <?= $estilo_seleccionado === 'casual' ? 'active' : '' ?>">Básicos / Casual</a>
    </div>
</div>

<!-- Catálogo -->
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="brand-text fw-normal m-0 text-capitalize">
                <?php 
                    $titulos = [
                        'todos'  => 'Colección Completa',
                        'denim'  => 'Estilo: Denim & Abrigos',
                        'sastre' => 'Estilo: Sastre & Elegante',
                        'falda'  => 'Estilo: Chic & Faldas',
                        'casual' => 'Estilo: Básicos & Casual'
                    ];
                    echo $titulos[$estilo_seleccionado] ?? 'Colección';
                ?>
            </h3>
            <?php if ($busqueda !== ''): ?>
                <small class="text-muted-moon">Resultados para: "<strong><?= htmlspecialchars($busqueda) ?></strong>"</small>
            <?php endif; ?>
        </div>
        <span class="text-muted-moon"><?= count($productos) ?> Prendas disponibles</span>
    </div>

    <?php if (empty($productos)): ?>
        <div class="text-center py-5">
            <p class="text-muted-moon fs-5">No hay prendas disponibles bajo este filtro por ahora.</p>
            <a href="index.php" class="btn btn-moon mt-2">Ver todo el catálogo</a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($productos as $index => $prod): ?>
            <div class="col">
                <div class="card h-100 card-product">
                    <div class="card-img-container">
                        <?php if ($index % 2 == 0): ?>
                            <span class="badge-trend">Popular</span>
                        <?php endif; ?>
                        
                        <img src="uploads/<?= htmlspecialchars($prod['imagen'] ?? 'default.png') ?>" class="img-principal" alt="<?= htmlspecialchars($prod['nombre_producto']) ?>">
                        <?php if (!empty($prod['imagen2'])): ?>
                            <img src="uploads/<?= htmlspecialchars($prod['imagen2']) ?>" class="img-secundaria" alt="Vista secundaria">
                        <?php endif; ?>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between p-4">
                        <div>
                            <small class="text-muted-moon text-uppercase fs-7"><?= htmlspecialchars($prod['nombre_tienda'] ?? 'Moon Store') ?></small>
                            <h5 class="card-title fw-bold text-white mt-1 mb-2"><?= htmlspecialchars($prod['nombre_producto']) ?></h5>
                            <p class="card-text text-muted-moon small mb-3"><?= htmlspecialchars($prod['descripcion']) ?></p>
                        </div>
                        <div>
                            <div class="d-flex align-items-center mb-2 gap-1">
                                <span class="badge bg-outline border border-secondary text-white-50" style="font-size:0.7rem;">S</span>
                                <span class="badge bg-outline border border-secondary text-white-50" style="font-size:0.7rem;">M</span>
                                <span class="badge bg-outline border border-secondary text-white-50" style="font-size:0.7rem;">L</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="price-tag fw-bold">$<?= number_format($prod['precio'], 2, '.', ',') ?></span>
                                <span class="badge bg-dark border border-secondary text-light"><?= htmlspecialchars($prod['categoria_nombre'] ?? 'General') ?></span>
                            </div>
                            <a href="agregar_carrito.php?id=<?= $prod['id_producto'] ?>" class="btn btn-moon w-100 py-2">Añadir al Carrito</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Footer -->
<footer class="py-4 mt-5">
    <div class="container text-center">
        <p class="brand-text fw-bold mb-1 fs-5">Moon Essence</p>
        <p class="small m-0">© 2026 Todos los derechos reservados. Moda independiente.</p>
    </div>
</footer>

</body>
</html>