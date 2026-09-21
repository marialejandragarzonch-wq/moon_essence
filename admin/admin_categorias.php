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
    <title>Gestión de Categorías - Moon Essence</title>
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
            <span class="btn btn-sm btn-outline-light active rounded-pill px-3">Categorías</span>
            <a href="admin_productos.php" class="btn btn-sm btn-outline-light rounded-pill px-3">Productos</a>
            <a href="index.php" class="btn btn-sm btn-outline-light rounded-pill px-3">Ver Tienda</a>
        </div>
    </div>
</nav>

<div class="container my-auto pb-5">
    <div class="card card-custom p-2">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3">
                <div>
                    <h3 class="fw-bold m-0 text-white"><i class="fa-solid fa-list me-2 text-primary"></i>Gestión de Categorías</h3>
                    <p class="text-light opacity-75 small m-0 mt-1">Secciones activas de tu catálogo.</p>
                </div>
                <a href="crear_categoria.php" class="btn btn-principal px-4 py-2"><i class="fa-solid fa-plus me-1"></i> Nueva Categoría</a>
            </div>

            <div class="table-responsive">
                <table class="table table-light table-hover table-striped align-middle m-0 rounded overflow-hidden">
                    <thead class="table-dark">
                        <tr>
                            <th style="color: #ffffff;">ID</th>
                            <th style="color: #ffffff;">Nombre</th>
                            <th style="color: #ffffff;">Descripción</th>
                            <th style="color: #ffffff;">Estado</th>
                            <th class="text-center" style="color: #ffffff;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($con) {
                            $query = "SELECT * FROM categorias ORDER BY id_categoria DESC";
                            $resultado = mysqli_query($con, $query);
                            
                            if ($resultado && mysqli_num_rows($resultado) > 0) {
                                while($row = mysqli_fetch_assoc($resultado)) {
                                    $nombre = $row['nombre'] ?? $row['nombre_categoria'] ?? 'Sin nombre';
                                    $descripcion = !empty($row['descripcion']) ? $row['descripcion'] : 'Sin descripción';
                                    $estado = $row['estado'] ?? 1;
                        ?>
                        <tr>
                            <td class="fw-bold" style="color: #1e293b !important;"><?=$row['id_categoria']?></td>
                            <td class="fw-bold" style="color: #0f172a !important;"><?=$nombre?></td>
                            <td style="color: #334155 !important;"><?=$descripcion?></td>
                            <td>
                                <?php if ($estado == 1): ?>
                                    <span class="badge bg-success px-3 py-2 rounded-pill">Activa</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary px-3 py-2 rounded-pill">Inactiva</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="editar_categoria.php?id=<?=$row['id_categoria']?>" class="btn btn-sm btn-primary me-1" title="Editar"><i class="fa-solid fa-pen"></i></a>
                                <a href="eliminar_categoria.php?id=<?=$row['id_categoria']?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar esta categoría?')" title="Eliminar"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php 
                                }
                            } else {
                                echo '<tr><td colspan="5" class="text-center text-dark py-4">No hay categorías registradas aún.</td></tr>';
                            }
                        } else {
                            echo '<tr><td colspan="5" class="text-center text-danger py-4">Error de conexión a la base de datos.</td></tr>';
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