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

// Obtener el ID del producto desde la URL
$id_producto = $_GET['id'] ?? 0;

// Consultar el producto y su categoría
$stmt = $pdo->prepare("
    SELECT p.*, c.nombre_categoria 
    FROM productos p 
    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
    WHERE p.id_producto = ?
");
$stmt->execute([$id_producto]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header("Location: ../index.php");
    exit;
}

// Detectar si es calzado basándonos en la categoría o el nombre del producto
$nombreCategoria = strtolower($producto['nombre_categoria'] ?? '');
$nombreProducto  = strtolower($producto['nombre_producto'] ?? '');
$esCalzado = (strpos($nombreCategoria, 'zapat') !== false || 
              strpos($nombreCategoria, 'calzado') !== false || 
              strpos($nombreProducto, 'tenis') !== false || 
              strpos($nombreProducto, 'bota') !== false || 
              strpos($nombreProducto, 'zapatilla') !== false || 
              strpos($nombreProducto, 'mary jane') !== false);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($producto['nombre_producto']) ?> - Moon Essence</title>
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
        .card-detalle {
            background-color: var(--bg-tarjeta);
            border: 1px solid #28374d;
            border-radius: 14px;
        }
        .img-producto {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #28374d;
        }
        .form-select {
            background-color: #121a24;
            border: 1px solid #28374d;
            color: #ffffff;
        }
        .form-select:focus {
            background-color: #121a24;
            border-color: var(--accent-luna);
            color: #ffffff;
            box-shadow: none;
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

<nav class="navbar navbar-expand-lg navbar-moon sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3 brand-text" href="index.php">
            <i class="bi bi-moon-stars-fill me-2"></i>Moon Essence
        </a>
        <a href="index.php" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left me-1"></i> Volver a la Tienda</a>
    </div>
</nav>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card card-detalle p-4 p-md-5 shadow-lg">
                <div class="row g-4 align-items-center">
                    
                    <div class="col-md-6 text-center">
                        <?php if (!empty($producto['imagen'])): ?>
                            <img src="uploads/<?= htmlspecialchars($producto['imagen']) ?>" alt="Producto" class="img-producto shadow">
                        <?php else: ?>
                            <div class="p-5 bg-dark text-muted-moon rounded border border-secondary">Sin imagen</div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <h2 class="brand-text fw-bold mb-3"><?= htmlspecialchars($producto['nombre_producto']) ?></h2>
                        <h3 class="text-success fw-bold mb-3">$<?= number_format($producto['precio'], 2, ',', '.') ?></h3>
                        <p class="text-muted-moon mb-4"><?= nl2br(htmlspecialchars($producto['descripcion'] ?? 'Sin descripción.')) ?></p>

                        <form action="carrito/agregar.php" method="POST">
                            <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">

                            <div class="mb-4">
                                <label class="form-label text-muted-moon small fw-bold">Selecciona tu Talla:</label>
                                <select name="talla" class="form-select py-2" required>
                                    <option value="" disabled selected>-- Elige una talla --</option>
                                    
                                    <?php if ($esCalzado): ?>
                                        <!-- Tallas para Zapatos -->
                                        <option value="35">35</option>
                                        <option value="36">36</option>
                                        <option value="37">37</option>
                                        <option value="38">38</option>
                                        <option value="39">39</option>
                                        <option value="40">40</option>
                                        <option value="41">41</option>
                                        <option value="42">42</option>
                                    <?php else: ?>
                                        <!-- Tallas para Prendas de Ropa -->
                                        <option value="XS">XS</option>
                                        <option value="S">S</option>
                                        <option value="M">M</option>
                                        <option value="L">L</option>
                                        <option value="XL">XL</option>
                                        <option value="XXL">XXL</option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-beigecito py-2 fs-6">
                                    <i class="bi bi-bag-plus-fill me-2"></i> Añadir al Carrito
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
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