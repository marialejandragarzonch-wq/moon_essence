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

$mensaje = '';
$tipo_mensaje = '';

// PROCESAR GUARDAR (CREAR / EDITAR)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'guardar') {
        $id = isset($_POST['id_categoria']) ? intval($_POST['id_categoria']) : 0;
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);

        if (!empty($nombre)) {
            if ($id > 0) {
                // Actualizar categoría existente
                $stmt = $pdo->prepare("UPDATE categorias SET nombre = ?, descripcion = ? WHERE id_categoria = ?");
                $stmt->execute([$nombre, $descripcion, $id]);
                $mensaje = "Categoría actualizada correctamente.";
            } else {
                // Crear nueva categoría
                $stmt = $pdo->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)");
                $stmt->execute([$nombre, $descripcion]);
                $mensaje = "Categoría creada correctamente.";
            }
            $tipo_mensaje = "success";
        } else {
            $mensaje = "El nombre de la categoría es obligatorio.";
            $tipo_mensaje = "danger";
        }
    }
}

// PROCESAR ELIMINAR CATEGORÍA
if (isset($_GET['eliminar'])) {
    $id_eliminar = intval($_GET['eliminar']);
    if ($id_eliminar > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM categorias WHERE id_categoria = ?");
            $stmt->execute([$id_eliminar]);
            $mensaje = "Categoría eliminada correctamente.";
            $tipo_mensaje = "success";
        } catch (PDOException $e) {
            $mensaje = "No se puede eliminar esta categoría porque tiene productos asociados.";
            $tipo_mensaje = "danger";
        }
    }
}

// OBTENER CATEGORÍAS
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY id_categoria DESC");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías - Moon Essence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --bg-dark: #0f172a;
            --card-dark: #1e293b;
            --border-color: rgba(245, 230, 211, 0.12);
            --beige-primary: #f5e6d3;
            --beige-hover: #e6cfa8;
            --beige-muted: #d4c3ac;
            --text-light: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-light);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
        }

        /* Navbar elegante */
        .navbar-custom {
            background-color: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--border-color);
            padding: 16px 36px;
        }

        .brand-icon {
            color: var(--beige-primary);
            font-size: 1.4rem;
        }

        .brand-title {
            color: var(--beige-primary);
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .btn-nav {
            border: 1px solid var(--border-color);
            color: var(--beige-muted);
            border-radius: 8px;
            padding: 6px 16px;
            font-size: 0.88rem;
            transition: all 0.2s ease;
        }

        .btn-nav:hover {
            background-color: rgba(245, 230, 211, 0.08);
            color: var(--beige-primary);
            border-color: var(--beige-primary);
        }

        /* Tarjeta Principal */
        .card-custom {
            background-color: var(--card-dark);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 36px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.4);
        }

        /* Iconos beige */
        .icon-beige {
            color: var(--beige-primary);
        }

        /* Botón beige principal */
        .btn-beige {
            background-color: var(--beige-primary);
            color: #1e1b18;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            padding: 10px 22px;
            font-size: 0.92rem;
            box-shadow: 0 4px 14px rgba(245, 230, 211, 0.15);
            transition: all 0.2s ease;
        }

        .btn-beige:hover {
            background-color: var(--beige-hover);
            color: #000000;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(245, 230, 211, 0.25);
        }

        /* Tabla estilizada */
        .table-custom {
            color: var(--text-light) !important;
            vertical-align: middle;
            margin-bottom: 0;
        }

        .table-custom th {
            color: var(--beige-muted) !important;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.78rem;
            letter-spacing: 1px;
            border-bottom: 2px solid var(--border-color) !important;
            padding: 16px 12px;
            background: transparent !important;
        }

        .table-custom td {
            color: var(--text-light) !important;
            border-bottom: 1px solid var(--border-color) !important;
            padding: 18px 12px;
            background: transparent !important;
        }

        .table-custom tbody tr {
            transition: background-color 0.2s ease;
        }

        .table-custom tbody tr:hover {
            background-color: rgba(245, 230, 211, 0.03) !important;
        }

        /* Insignia elegante para el ID */
        .badge-id {
            background-color: rgba(245, 230, 211, 0.1);
            color: var(--beige-primary);
            border: 1px solid rgba(245, 230, 211, 0.2);
            padding: 6px 10px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        /* Botones de acción */
        .btn-action-edit {
            background-color: rgba(245, 230, 211, 0.1);
            color: var(--beige-primary);
            border: 1px solid rgba(245, 230, 211, 0.2);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .btn-action-edit:hover {
            background-color: var(--beige-primary);
            color: #121824;
        }

        .btn-action-delete {
            background-color: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.2);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .btn-action-delete:hover {
            background-color: #ef4444;
            color: #ffffff;
        }

        /* Modal */
        .modal-content {
            background-color: var(--card-dark);
            border: 1px solid var(--border-color);
            color: var(--text-light);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        }

        .modal-header, .modal-footer {
            border-color: var(--border-color);
            padding: 20px 24px;
        }

        .modal-title {
            color: var(--beige-primary);
        }

        .form-label {
            color: var(--beige-muted);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-control-custom {
            background-color: #0f172a !important;
            border: 1px solid var(--border-color) !important;
            color: #ffffff !important;
            border-radius: 10px;
            padding: 12px 16px;
        }

        .form-control-custom::placeholder {
            color: #64748b !important;
        }

        .form-control-custom:focus {
            background-color: #0f172a !important;
            border-color: var(--beige-primary) !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 0.25rem rgba(245, 230, 211, 0.15) !important;
        }
    </style>
</head>
<body>

<!-- Navegación -->
<div class="navbar-custom d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-moon-stars brand-icon"></i>
        <span class="brand-title fs-5">Moon Essence</span>
        <span class="text-muted ms-1" style="font-size: 0.9rem;">| Panel Administrativo</span>
    </div>
    <div class="d-flex gap-2">
        <a href="dashboard.php" class="btn btn-nav text-decoration-none"><i class="bi bi-speedometer2 me-1"></i> Panel</a>
        <a href="admin_productos.php" class="btn btn-nav text-decoration-none"><i class="bi bi-box-seam me-1"></i> Productos</a>
    </div>
</div>

<!-- Contenedor Principal Amplio -->
<div class="container-fluid px-4 px-md-5 my-5">

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-<?= $tipo_mensaje ?> alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <?= $mensaje ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card-custom">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
            <div>
                <h3 class="fw-bold m-0 d-flex align-items-center gap-2">
                    <i class="bi bi-tag icon-beige"></i>
                    <span>Gestión de Categorías</span>
                </h3>
                <p class="text-muted m-0 mt-1" style="font-size: 0.92rem;">Administra las secciones y la organización de tu catálogo.</p>
            </div>
            <div>
                <button class="btn btn-beige d-flex align-items-center gap-2" onclick="abrirModalCrear()">
                    <i class="bi bi-plus-lg"></i> Nueva Categoría
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th style="width: 10%;">ID</th>
                        <th style="width: 25%;">Nombre</th>
                        <th style="width: 50%;">Descripción</th>
                        <th style="width: 15%; text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($categorias) > 0): ?>
                        <?php foreach ($categorias as $cat): ?>
                            <tr>
                                <td><span class="badge-id">#<?= $cat['id_categoria'] ?></span></td>
                                <td class="fw-bold text-white"><?= htmlspecialchars($cat['nombre']) ?></td>
                                <td style="color: #cbd5e1 !important;"><?= htmlspecialchars($cat['descripcion'] ?? '-') ?></td>
                                <td class="text-end">
                                    <button class="btn btn-action-edit me-1" 
                                            onclick="abrirModalEditar(<?= $cat['id_categoria'] ?>, '<?= htmlspecialchars(addslashes($cat['nombre'])) ?>', '<?= htmlspecialchars(addslashes($cat['descripcion'] ?? '')) ?>')" 
                                            title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <a href="admin_categorias.php?eliminar=<?= $cat['id_categoria'] ?>" 
                                       class="btn btn-action-delete" 
                                       onclick="return confirm('¿Estás seguro de que deseas eliminar esta categoría?');" 
                                       title="Eliminar">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No se encontraron categorías registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Crear / Editar Categoría -->
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="admin_categorias.php" method="POST">
                <input type="hidden" name="action" value="guardar">
                <input type="hidden" name="id_categoria" id="id_categoria" value="0">
                
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTitulo">Nueva Categoría</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la Categoría</label>
                        <input type="text" name="nombre" id="nombre_categoria" class="form-control form-control-custom" placeholder="Ej. Chaquetas, Faldas..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" id="descripcion_categoria" class="form-control form-control-custom" rows="3" placeholder="Breve descripción de la sección..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-nav" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-beige">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modalElement = document.getElementById('modalCategoria');
    const modalCategoria = new bootstrap.Modal(modalElement);

    function abrirModalCrear() {
        document.getElementById('modalTitulo').innerText = 'Nueva Categoría';
        document.getElementById('id_categoria').value = '0';
        document.getElementById('nombre_categoria').value = '';
        document.getElementById('descripcion_categoria').value = '';
        modalCategoria.show();
    }

    function abrirModalEditar(id, nombre, descripcion) {
        document.getElementById('modalTitulo').innerText = 'Editar Categoría';
        document.getElementById('id_categoria').value = id;
        document.getElementById('nombre_categoria').value = nombre;
        document.getElementById('descripcion_categoria').value = descripcion;
        modalCategoria.show();
    }
</script>
</body>
</html>