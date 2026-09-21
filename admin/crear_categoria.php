<?php
if (file_exists(__DIR__ . '/config/conexion.php')) { include_once __DIR__ . '/config/conexion.php'; }
elseif (file_exists(__DIR__ . '/conexion.php')) { include_once __DIR__ . '/conexion.php'; }

$con = $conexion ?? $conn ?? $db ?? null;
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $descripcion = mysqli_real_escape_string($con, $_POST['descripcion']);

    if (!empty($nombre)) {
        $query = "INSERT INTO categorias (nombre, nombre_categoria, descripcion, estado) VALUES ('$nombre', '$nombre', '$descripcion', 'Activo')";
        if (mysqli_query($con, $query)) {
            header('Location: admin_categorias.php');
            exit;
        } else {
            $mensaje = "Error al guardar: " . mysqli_error($con);
        }
    } else {
        $mensaje = "El nombre es obligatorio.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Categoría - Moon Essence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0b0f19; color: #fff; font-family: system-ui, sans-serif; }
        .card-custom { background-color: #111827; border: 1px solid #1f293d; border-radius: 12px; }
        .text-gold { color: #f3e8c8; }
        .btn-gold { background-color: #f3e8c8; color: #0b0f19; font-weight: 600; border-radius: 20px; }
        .form-control { background-color: #1a2333 !important; border: 1px solid #2d3748 !important; color: #fff !important; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
<div class="container my-auto py-5" style="max-width: 500px;">
    <div class="card card-custom p-3">
        <div class="card-body">
            <h4 class="fw-bold mb-3 text-gold">Nueva Categoría</h4>
            <?php if ($mensaje): ?><div class="alert alert-danger"><?=$mensaje?></div><?php endif; ?>
            <form action="crear_categoria.php" method="POST">
                <div class="mb-3">
                    <label class="form-label small text-muted">Nombre de la Categoría</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small text-muted">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3"></textarea>
                </div>
                <div class="d-flex gap-2 justify-content-end">
                    <a href="admin_categorias.php" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
                    <button type="submit" class="btn btn-gold px-4">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>