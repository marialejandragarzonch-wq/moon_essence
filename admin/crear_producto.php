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

$mensaje = '';
$tipo_alerta = '';

// Procesar formulario al enviar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_producto   = trim($_POST['nombre_producto'] ?? '');
    $descripcion       = trim($_POST['descripcion'] ?? '');
    $precio            = (float)($_POST['precio'] ?? 0);
    $stock             = (int)($_POST['stock'] ?? 10);
    $id_categoria      = (int)($_POST['id_categoria'] ?? 1);
    $id_tienda         = 1; // Valor por defecto según tu BD
    $estado_aprobacion = 'aprobado';
    $aprobado          = 1;
    $fecha_creacion    = date('Y-m-d H:i:s');

    $imagen_principal  = '';
    $imagen_secundaria = '';

    // Crear la carpeta uploads si no existe
    $directorio_subida = '../uploads/';
    if (!is_dir($directorio_subida)) {
        mkdir($directorio_subida, 0777, true);
    }

    // 1. Subir Imagen Principal
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $ext1 = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $imagen_principal = time() . '_1_' . uniqid() . '.' . $ext1;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $directorio_subida . $imagen_principal);
    }

    // 2. Subir Imagen Secundaria
    if (isset($_FILES['imagen_secundaria']) && $_FILES['imagen_secundaria']['error'] === UPLOAD_ERR_OK) {
        $ext2 = pathinfo($_FILES['imagen_secundaria']['name'], PATHINFO_EXTENSION);
        $imagen_secundaria = time() . '_2_' . uniqid() . '.' . $ext2;
        move_uploaded_file($_FILES['imagen_secundaria']['tmp_name'], $directorio_subida . $imagen_secundaria);
    }

    if (!empty($nombre_producto) && $precio > 0 && $id_categoria > 0) {
        try {
            $sql = "INSERT INTO productos (
                        id_tienda, 
                        id_categoria, 
                        nombre_producto, 
                        descripcion, 
                        precio, 
                        stock, 
                        imagen, 
                        imagen_secundaria, 
                        estado_aprobacion, 
                        fecha_creacion, 
                        aprobado
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $id_tienda,
                $id_categoria,
                $nombre_producto,
                $descripcion,
                $precio,
                $stock,
                $imagen_principal,
                $imagen_secundaria,
                $estado_aprobacion,
                $fecha_creacion,
                $aprobado
            ]);

            $mensaje = "¡Producto <strong>'$nombre_producto'</strong> agregado correctamente!";
            $tipo_alerta = "success";
        } catch (PDOException $e) {
            $mensaje = "Error al guardar en la base de datos: " . $e->getMessage();
            $tipo_alerta = "danger";
        }
    } else {
        $mensaje = "Por favor completa todos los campos obligatorios (*).";
        $tipo_alerta = "warning";
    }
}

// Consultar las categorías registradas en la base de datos
try {
    $stmt_cats = $pdo->query("SELECT * FROM categorias ORDER BY id_categoria ASC");
    $categorias = $stmt_cats->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Categorías por defecto si falla la tabla
    $categorias = [
        ['id_categoria' => 1, 'nombre_categoria' => 'Chaquetas'],
        ['id_categoria' => 2, 'nombre_categoria' => 'Faldas'],
        ['id_categoria' => 3, 'nombre_categoria' => 'Básicos'],
        ['id_categoria' => 4, 'nombre_categoria' => 'Sastre'],
        ['id_categoria' => 5, 'nombre_categoria' => 'Calzado']
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Producto - Moon Essence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #0b131e;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .card-custom {
            background-color: #111a28;
            border: 1px solid #1f2d40;
            border-radius: 10px;
        }

        /* Color de texto claro e impreso visible dentro de las cajas */
        .form-control, .form-select {
            background-color: #0d1522 !important;
            border: 1px solid #233348 !important;
            color: #ffffff !important;
        }

        /* Color visible para el placeholder (texto de ayuda) */
        .form-control::placeholder, textarea::placeholder {
            color: #94a3b8 !important;
            opacity: 1 !important;
        }

        .form-control:focus, .form-select:focus {
            border-color: #f9e8d0 !important;
            box-shadow: 0 0 6px rgba(249, 232, 208, 0.3) !important;
        }

        .btn-moon {
            background-color: #fce7f3;
            color: #000000;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .btn-moon:hover {
            background-color: #ffffff;
            color: #000000;
        }

        .btn-outline-custom {
            border: 1px solid #374151;
            color: #ffffff;
            text-decoration: none;
        }

        .btn-outline-custom:hover {
            background-color: #1f2937;
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-warning m-0"><i class="bi bi-plus-circle me-2"></i>Agregar Nuevo Producto</h3>
                <a href="admin_productos.php" class="btn btn-outline-custom btn-sm px-3 py-1"><i class="bi bi-arrow-left me-1"></i> Volver al Panel</a>
            </div>

            <!-- Alerta de Respuesta -->
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-<?= $tipo_alerta ?> alert-dismissible fade show" role="alert">
                    <?= $mensaje ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card card-custom p-4 shadow">
                <form method="POST" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label class="form-label text-light">Nombre del Producto *</label>
                        <input type="text" name="nombre_producto" class="form-control" placeholder="Ej. Chaqueta Denim Oversize" required>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-light">Precio ($) *</label>
                            <input type="number" name="precio" class="form-control" step="0.01" placeholder="Ej. 95000" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-light">Stock (Cantidad) *</label>
                            <input type="number" name="stock" class="form-control" value="10" min="1" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-light">Categoría *</label>
                            <select name="id_categoria" class="form-select" required>
                                <option value="" selected disabled>Selecciona categoría</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id_categoria'] ?>">
                                        <?= htmlspecialchars($cat['nombre_categoria'] ?? $cat['nombre'] ?? 'Categoría ' . $cat['id_categoria']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3" placeholder="Descripción detallada de la prenda..."></textarea>
                    </div>

                    <!-- Campos para subir las 2 Imágenes -->
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-light">Imagen Principal del Producto *</label>
                            <input type="file" name="imagen" class="form-control" accept="image/*" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label text-light">Imagen Secundaria (Hover / Detalle)</label>
                            <input type="file" name="imagen_secundaria" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <div class="d-grid gap-2 pt-2">
                        <button type="submit" class="btn btn-moon py-2 fs-6"><i class="bi bi-check-circle me-1"></i> Guardar Producto</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>