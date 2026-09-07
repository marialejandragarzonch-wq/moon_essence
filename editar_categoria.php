<?php
// Búsqueda del archivo de conexión real
if (file_exists(__DIR__ . '/config/conexion.php')) {
    include_once __DIR__ . '/config/conexion.php';
} elseif (file_exists(__DIR__ . '/config/db.php')) {
    include_once __DIR__ . '/config/db.php';
} elseif (file_exists(__DIR__ . '/conexion.php')) {
    include_once __DIR__ . '/conexion.php';
}

$con = $conexion ?? $conn ?? $db ?? null;
$mensaje = '';

// Obtener los datos de la categoría a editar
if (isset($_GET['id'])) {
    $id_categoria = (int)$_GET['id'];
    $query_select = "SELECT * FROM categorias WHERE id_categoria = $id_categoria";
    $result_select = mysqli_query($con, $query_select);
    $categoria = mysqli_fetch_assoc($result_select);

    if (!$categoria) {
        header('Location: admin_categorias.php');
        exit;
    }
} else {
    header('Location: admin_categorias.php');
    exit;
}

// Guardar los cambios actualizados
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id_categoria'];
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $descripcion = mysqli_real_escape_string($con, $_POST['descripcion']);
    $estado = mysqli_real_escape_string($con, $_POST['estado']);

    if (!empty($nombre)) {
        $query_update = "UPDATE categorias 
                        SET nombre = '$nombre', 
                            nombre_categoria = '$nombre', 
                            descripcion = '$descripcion', 
                            estado = '$estado' 
                        WHERE id_categoria = $id";
        
        if (mysqli_query($con, $query_update)) {
            header('Location: admin_categorias.php');
            exit;
        } else {
            $mensaje = "Error al actualizar: " . mysqli_error($con);
        }
    } else {
        $mensaje = "El nombre de la categoría es obligatorio.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Categoría - Moon Essence</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #0b0f19 !important;
            color: #ffffff !important;
            font-family: system-ui, -apple-system, sans-serif;
        }

        .navbar-custom {
            background-color: #0b0f19 !important;
            border-bottom: 1px solid #1e293b;
        }

        .card-custom {
            background-color: #111827 !important;
            border: 1px solid #1f293d !important;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }

        .text-gold {
            color: #f3e8c8 !important;
        }

        .btn-gold {
            background-color: #f3e8c8 !important;
            color: #0b0f19 !important;
            font-weight: 600;
            border: none;
            border-radius: 20px;
        }

        .btn-gold:hover {
            background-color: #e2d4ac !important;
            color: #0b0f19 !important;
        }

        .form-control, .form-select {
            background-color: #1a2333 !important;
            border: 1px solid #2d3748 !important;
            color: #ffffff !important;
        }

        .form-control:focus, .form-select:focus {
            border-color: #f3e8c8 !important;
            box-shadow: 0 0 5px rgba(243, 232, 200, 0.3) !important;
        }

        footer {
            border-top: 1px solid #1e293b;
            color: #64748b;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- Navegación Superior -->
<nav class="navbar navbar-expand-lg navbar-custom py-3 px-4 mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-white fs-4" href="index.php">
            Moon Essence
        </a>
        <div class="d-flex align-items-center gap-2">
            <a href="admin_categorias.php" class="btn btn-sm btn-outline-light rounded-pill px-3">Volver a Categorías</a>
        </div>
    </div>
</nav>

<!-- Contenido Principal -->
<div class="container my-auto pb-5" style="max-width: 600px;">
    <div class="card card-custom p-3">
        <div class="card-body">
            <h4 class="fw-bold mb-3 text-white">
                <i class="fa-solid fa-pen me-2 text-gold"></i>Editar Categoría #<?=$categoria['id_categoria']?>
            </h4>
            
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-danger py-2 small"><?=$mensaje?></div>
            <?php endif; ?>

            <form action="editar_categoria.php?id=<?=$categoria['id_categoria']?>" method="POST">
                <input type="hidden" name="id_categoria" value="<?=$categoria['id_categoria']?>">

                <div class="mb-3">
                    <label class="form-label small text-muted">Nombre de la Categoría</label>
                    <input type="text" name="nombre" class="form-control" value="<?=htmlspecialchars($categoria['nombre_categoria'] ?? $categoria['nombre'])?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3"><?=htmlspecialchars($categoria['descripcion'] ?? '')?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label small text-muted">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="Activo" <?=($categoria['estado'] ?? '') == 'Activo' ? 'selected' : ''?>>Activo</option>
                        <option value="Inactivo" <?=($categoria['estado'] ?? '') == 'Inactivo' ? 'selected' : ''?>>Inactivo</option>
                    </select>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="admin_categorias.php" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
                    <button type="submit" class="btn btn-gold px-4">Actualizar Categoría</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Pie de página -->
<footer class="py-3 text-center small mt-auto">
    <p class="m-0">© 2026 Moon Essence - Todos los derechos reservados.</p>
</footer>

</body>
</html>