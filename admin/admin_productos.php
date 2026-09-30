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
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

$mensaje = '';
$tipo_alerta = '';

// Lógica para ELIMINAR producto
if (isset($_GET['action']) && $_GET['action'] === 'eliminar' && isset($_GET['id'])) {
    $id_eliminar = (int)$_GET['id'];
    if ($id_eliminar > 0) {
        try {
            // Obtener nombres de imágenes para borrarlas del servidor
            $stmt_img = $pdo->prepare("SELECT imagen, imagen_secundaria FROM productos WHERE id_producto = ?");
            $stmt_img->execute([$id_eliminar]);
            $prod_img = $stmt_img->fetch(PDO::FETCH_ASSOC);

            if ($prod_img) {
                if (!empty($prod_img['imagen']) && file_exists('../uploads/' . $prod_img['imagen'])) {
                    @unlink('../uploads/' . $prod_img['imagen']);
                }
                if (!empty($prod_img['imagen_secundaria']) && file_exists('../uploads/' . $prod_img['imagen_secundaria'])) {
                    @unlink('../uploads/' . $prod_img['imagen_secundaria']);
                }
            }

            // Eliminar de la base de datos
            $stmt_del = $pdo->prepare("DELETE FROM productos WHERE id_producto = ?");
            $stmt_del->execute([$id_eliminar]);

            $mensaje = "Producto #$id_eliminar eliminado correctamente.";
            $tipo_alerta = "success";
        } catch (PDOException $e) {
            $mensaje = "Error al eliminar el producto: " . $e->getMessage();
            $tipo_alerta = "danger";
        }
    }
}

// Consultar todos los productos con su categoría
$sql = "SELECT p.*, COALESCE(c.nombre_categoria, 'General') AS categoria_nombre 
        FROM productos p 
        LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
        ORDER BY p.id_producto DESC";

$stmt = $pdo->query($sql);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos - Moon Essence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #0b111e;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .card-custom {
            background-color: #101926;
            border: 1px solid #1c2a3e;
            border-radius: 12px;
        }

        .table-custom {
            color: #d1d5db;
            vertical-align: middle;
        }

        .table-custom th {
            background-color: #101926;
            color: #ffffff;
            border-bottom: 2px solid #1c2a3e;
            padding: 14px;
        }

        .table-custom td {
            background-color: #101926;
            border-bottom: 1px solid #1c2a3e;
            padding: 12px 14px;
        }

        .badge-cat {
            background-color: #1f293d;
            color: #93c5fd;
            border: 1px solid #2d3e58;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
        }

        .img-thumb {
            width: 42px;
            height: 42px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #2d3e58;
        }

        .btn-action-edit {
            background-color: transparent;
            border: 1px solid #b45309;
            color: #f59e0b;
            padding: 5px 10px;
            border-radius: 6px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-action-edit:hover {
            background-color: #f59e0b;
            color: #000000;
        }

        .btn-action-delete {
            background-color: transparent;
            border: 1px solid #991b1b;
            color: #ef4444;
            padding: 5px 10px;
            border-radius: 6px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-action-delete:hover {
            background-color: #ef4444;
            color: #ffffff;
        }

        .btn-new-product {
            background-color: #fce7f3;
            color: #000000;
            font-weight: 600;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
        }

        .btn-new-product:hover {
            background-color: #ffffff;
            color: #000000;
        }

        .btn-dashboard {
            background-color: #1f293d;
            color: #ffffff;
            border: 1px solid #2d3e58;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
        }

        .btn-dashboard:hover {
            background-color: #2d3e58;
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="container my-5">
    
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold m-0 d-flex align-items-center gap-2">
            <i class="bi bi-box-seam"></i> Catálogo de Productos
        </h3>
        <div class="d-flex gap-2">
            <a href="dashboard.php" class="btn btn-dashboard btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="crear_producto.php" class="btn btn-new-product btn-sm d-flex align-items-center gap-1">
                + Nuevo Producto
            </a>
        </div>
    </div>

    <!-- Mensaje de alerta -->
    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-<?= $tipo_alerta ?> alert-dismissible fade show" role="alert">
            <?= $mensaje ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Tabla de Productos -->
    <div class="card card-custom p-3 shadow-lg">
        <div class="table-responsive">
            <table class="table table-custom align-middle m-0">
                <thead>
                    <tr>
                        <th>Ref / ID</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($productos) > 0): ?>
                        <?php foreach ($productos as $prod): ?>
                            <tr>
                                <td class="text-muted fw-semibold">Ref: <?= $prod['id_producto'] ?></td>
                                <td>
                                    <?php if (!empty($prod['imagen'])): ?>
                                        <img src="../uploads/<?= htmlspecialchars($prod['imagen']) ?>" class="img-thumb" alt="Prod">
                                    <?php else: ?>
                                        <div class="img-thumb d-flex align-items-center justify-content-center bg-secondary text-white fs-6">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-medium text-white"><?= htmlspecialchars($prod['nombre_producto']) ?></td>
                                <td>
                                    <span class="badge-cat"><?= htmlspecialchars($prod['categoria_nombre']) ?></span>
                                </td>
                                <td class="fw-bold text-white">$<?= number_format($prod['precio'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- Botón Editar -->
                                        <a href="editar_producto.php?id=<?= $prod['id_producto'] ?>" class="btn-action-edit" title="Editar Producto">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <!-- Botón Eliminar con Confirmación -->
                                        <a href="admin_productos.php?action=eliminar&id=<?= $prod['id_producto'] ?>" 
                                           class="btn-action-delete" 
                                           title="Eliminar Producto"
                                           onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No hay productos registrados en el catálogo.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>