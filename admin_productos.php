<?php
if (file_exists(__DIR__ . '/config/conexion.php')) {
    include_once __DIR__ . '/config/conexion.php';
} elseif (file_exists(__DIR__ . '/conexion.php')) {
    include_once __DIR__ . '/conexion.php';
}

$con = $conexion ?? $conn ?? $db ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos - Moon Essence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0b0f19 !important; color: #ffffff !important; font-family: system-ui, sans-serif; }
        .navbar-custom { background-color: #0b0f19 !important; border-bottom: 1px solid #1e293b; }
        .card-custom { background-color: #111827 !important; border: 1px solid #1f293d !important; border-radius: 12px; }
        .btn-principal { background-color: #3b82f6 !important; color: #ffffff !important; font-weight: 600; border: none; border-radius: 20px; }
        .btn-principal:hover { background-color: #2563eb !important; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-custom py-3 px-4 mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-white fs-4" href="index.php">Moon Essence</a>
        <div class="d-flex align-items-center gap-2">
            <a href="admin_categorias.php" class="btn btn-sm btn-outline-light rounded-pill px-3">Categorías</a>
            <span class="btn btn-sm btn-outline-light active rounded-pill px-3">Productos</span>
            <a href="index.php" class="btn btn-sm btn-outline-light rounded-pill px-3">Ver Tienda</a>
        </div>
    </div>
</nav>

<div class="container my-auto pb-5">
    <div class="card card-custom p-2">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3">
                <div>
                    <h3 class="fw-bold m-0 text-white"><i class="fa-solid fa-shirt me-2 text-primary"></i>Gestión de Productos</h3>
                    <p class="text-light opacity-75 small m-0 mt-1">Catálogo de prendas registradas en Moon Essence.</p>
                </div>
                <a href="crear_producto.php" class="btn btn-principal px-4 py-2"><i class="fa-solid fa-plus me-1"></i> Nuevo Producto</a>
            </div>

            <div class="table-responsive">
                <table class="table table-light table-hover table-striped align-middle m-0 rounded overflow-hidden">
                    <thead class="table-dark">
                        <tr>
                            <th style="color: #ffffff;">ID</th>
                            <th style="color: #ffffff;">Imagen</th>
                            <th style="color: #ffffff;">Prenda</th>
                            <th style="color: #ffffff;">Categoría</th>
                            <th style="color: #ffffff;">Precio</th>
                            <th style="color: #ffffff;">Stock</th>
                            <th class="text-center" style="color: #ffffff;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($con) {
                            $query = "SELECT p.*, c.nombre as cat_nombre, c.nombre_categoria 
                                      FROM productos p 
                                      LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                                      ORDER BY p.id_producto DESC";
                            $resultado = mysqli_query($con, $query);
                            
                            if ($resultado && mysqli_num_rows($resultado) > 0) {
                                while($row = mysqli_fetch_assoc($resultado)) {
                                    $cat = $row['nombre_categoria'] ?? $row['cat_nombre'] ?? 'Sin categoría';
                                    $nombre_prenda = $row['nombre'] ?? $row['nombre_producto'] ?? 'Prenda sin nombre';
                                    $stock = $row['stock'] ?? '0';

                                    // Busca el nombre de la foto en la base de datos
                                    $foto_db = $row['imagen'] ?? $row['imagen_url'] ?? $row['foto'] ?? $row['img'] ?? '';

                                    // Apunta a la carpeta uploads/
                                    if (!empty($foto_db)) {
                                        if (filter_var($foto_db, FILTER_VALIDATE_URL)) {
                                            $src_imagen = $foto_db;
                                        } else {
                                            $nombre_archivo = basename($foto_db);
                                            $src_imagen = 'uploads/' . $nombre_archivo;
                                        }
                                    } else {
                                        $src_imagen = 'https://via.placeholder.com/150/1e293b/ffffff?text=Sin+Foto';
                                    }
                        ?>
                        <tr>
                            <td class="fw-bold" style="color: #1e293b !important;"><?=$row['id_producto']?></td>
                            <td>
                                <img src="<?=$src_imagen?>" 
                                     alt="<?=$nombre_prenda?>" 
                                     style="width: 45px; height: 45px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1;" 
                                     onerror="this.onerror=null; this.src='https://via.placeholder.com/150/1e293b/ffffff?text=Sin+Foto';">
                            </td>
                            <td class="fw-bold" style="color: #0f172a !important;"><?=$nombre_prenda?></td>
                            <td><span class="badge bg-dark text-white px-3 py-2 rounded-pill"><?=$cat?></span></td>
                            <td class="fw-bold" style="color: #0284c7 !important;">$<?=number_format($row['precio'], 0, ',', '.')?></td>
                            <td style="color: #334155 !important;"><?=$stock?> uds</td>
                            <td class="text-center">
                                <a href="editar_producto.php?id=<?=$row['id_producto']?>" class="btn btn-sm btn-primary me-1" title="Editar"><i class="fa-solid fa-pen"></i></a>
                                <a href="eliminar_producto.php?id=<?=$row['id_producto']?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este producto?')" title="Eliminar"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php 
                                }
                            } else {
                                echo '<tr><td colspan="7" class="text-center text-dark py-4">No hay productos registrados aún.</td></tr>';
                            }
                        } else {
                            echo '<tr><td colspan="7" class="text-center text-danger py-4">Error de conexión a la base de datos.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>