<?php
session_start();
// Si no hay sesión de administrador activa, redirigir al login
if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['usuario'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Moon Essence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --bg-cielo: #0b131e;
            --bg-tarjeta: #131c2a;
            --accent-luna: #f9e8d0;
            --texto-suave: #aebbc9;
            --border-color: #213045;
        }

        body {
            background-color: var(--bg-cielo);
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-admin {
            background-color: #05090e;
            border-bottom: 1px solid var(--border-color);
        }

        .brand-text {
            color: var(--accent-luna) !important;
            letter-spacing: 1px;
            text-decoration: none;
        }

        .card-dash {
            background-color: var(--bg-tarjeta);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 25px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-dash:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
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
            border-top: 1px solid var(--border-color);
            color: var(--texto-suave);
            margin-top: auto;
        }
    </style>
</head>
<body>

<!-- Navbar de Administración -->
<nav class="navbar navbar-expand-lg navbar-admin sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 brand-text d-flex align-items-center" href="admin_dashboard.php">
            <i class="bi bi-moon-stars-fill me-2"></i>Moon Essence <span class="fs-6 text-muted-moon ms-2">| Panel Admin</span>
        </a>

        <div class="d-flex align-items-center gap-2">
            <!-- Enlace Quiénes Somos (Retrocede un nivel a la raíz) -->
            <a href="/moon_essence/quienes_somos.php" class="btn btn-sm btn-outline-light">
                <i class="bi bi-people me-1"></i> Quiénes Somos
            </a>

            <!-- Botón Tratamiento de Datos (Abre el Modal) -->
            <button type="button" class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#modalTratamientoDatos">
                <i class="bi bi-shield-lock me-1"></i> Tratamiento de datos
            </button>

            <!-- Volver a la Tienda -->
            <a href="../index.php" class="btn btn-sm btn-outline-secondary text-white ms-2">
                <i class="bi bi-shop me-1"></i> Volver a la Tienda
            </a>

            <!-- Cerrar Sesión -->
            <a href="../auth/logout.php" class="btn btn-sm btn-danger">
                <i class="bi bi-box-arrow-right me-1"></i> Salir
            </a>
        </div>
    </div>
</nav>

<!-- Contenido del Dashboard -->
<div class="container my-5">
    <div class="mb-4">
        <h2 class="brand-text fw-bold mb-1">Panel de Control</h2>
        <p class="text-muted-moon">Bienvenido al centro de administración de Moon Essence.</p>
    </div>

    <div class="row g-4">
        <!-- Tarjeta Productos -->
        <div class="col-md-4">
            <div class="card-dash h-100 d-flex flex-column justify-content-between">
                <div>
                    <span class="text-muted-moon small text-uppercase fw-bold">Catálogo</span>
                    <h3 class="text-white fw-bold mt-1 mb-2">Productos</h3>
                    <p class="text-muted-moon small">Administra las prendas, precios, stock e imágenes del inventario.</p>
                </div>
                <a href="admin_productos.php" class="btn btn-beigecito w-100 mt-3">Gestionar Productos →</a>
            </div>
        </div>

        <!-- Tarjeta Categorías -->
        <div class="col-md-4">
            <div class="card-dash h-100 d-flex flex-column justify-content-between">
                <div>
                    <span class="text-muted-moon small text-uppercase fw-bold">Clasificación</span>
                    <h3 class="text-white fw-bold mt-1 mb-2">Categorías</h3>
                    <p class="text-muted-moon small">Administra las categorías activas para clasificar las prendas.</p>
                </div>
                <a href="admin_categorias.php" class="btn btn-beigecito w-100 mt-3">Gestionar Categorías →</a>
            </div>
        </div>

        <!-- Tarjeta Reportes -->
        <div class="col-md-4">
            <div class="card-dash h-100 d-flex flex-column justify-content-between">
                <div>
                    <span class="text-muted-moon small text-uppercase fw-bold">Métricas y KPIs</span>
                    <h3 class="text-white fw-bold mt-1 mb-2">Reportes Analíticos</h3>
                    <p class="text-muted-moon small">Consulta los indicadores de ventas, ingresos acumulados y resumen de pedidos.</p>
                </div>
                <a href="reportes.php" class="btn btn-beigecito w-100 mt-3">Ver Reportes Analíticos →</a>
            </div>
        </div>
    </div>
</div>

<footer class="py-4 mt-auto text-center">
    <div class="container">
        <p class="brand-text fw-bold mb-1 fs-6">Moon Essence</p>
        <p class="small m-0 text-muted-moon">Panel de Control - © 2026</p>
    </div>
</footer>

<!-- Modal de Tratamiento de Datos -->
<div class="modal fade" id="modalTratamientoDatos" tabindex="-1" aria-labelledby="modalTratamientoDatosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: #131c2a; border: 1px solid #213045; color: #ffffff; border-radius: 16px;">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title fw-bold" id="modalTratamientoDatosLabel" style="color: #f9e8d0;">
                    <i class="bi bi-shield-check-fill me-2"></i> Tratamiento de datos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-start">
                <p class="m-0" style="color: #aebbc9; line-height: 1.6; font-size: 0.95rem;">
                    El presente proyecto tiene fines exclusivamente académicos. Los datos utilizados durante el desarrollo, las pruebas y la demostración del sistema son ficticios y fueron creados únicamente con fines de simulación. El sistema no recolecta, almacena ni procesa datos personales reales de estudiantes, docentes o terceros.
                </p>
            </div>
            <div class="modal-footer border-top border-secondary">
                <button type="button" class="btn btn-sm px-4 fw-bold" style="background-color: #f9e8d0; color: #0b131e;" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>