<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiénes Somos - Moon Essence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --bg-principal: #0b131e;
            --bg-card: #131c2a;
            --accent-luna: #f9e8d0;
            --texto-suave: #aebbc9;
            --border-color: #213045;
        }

        body {
            background-color: var(--bg-principal);
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-moon {
            background-color: #05090e !important;
            border-bottom: 1px solid var(--border-color);
        }

        .brand-text {
            color: var(--accent-luna) !important;
            letter-spacing: 1px;
            text-decoration: none;
        }

        .card-integrante {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 30px;
            text-align: center;
            height: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-integrante:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(249, 232, 208, 0.08);
        }

        .avatar-box {
            width: 160px;
            height: 160px;
            margin: 0 auto 20px auto;
            border-radius: 50%;
            border: 3px solid var(--accent-luna);
            overflow: hidden;
            background-color: #1a2738;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-placeholder {
            color: var(--texto-suave);
            font-size: 3.5rem;
        }

        .text-beige {
            color: var(--accent-luna) !important;
        }

        .text-muted-moon {
            color: var(--texto-suave) !important;
        }

        .btn-outline-moon {
            border: 1px solid var(--accent-luna) !important;
            color: var(--accent-luna) !important;
            background: transparent;
        }

        .btn-outline-moon:hover {
            background-color: var(--accent-luna) !important;
            color: #0b131e !important;
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

<!-- Navbar Dinámico -->
<nav class="navbar navbar-expand-lg navbar-moon sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 brand-text" href="index.php">
            <i class="bi bi-moon-stars-fill me-2"></i>Moon Essence
        </a>
        <div class="d-flex align-items-center gap-3">
            <a href="index.php" class="btn btn-sm btn-outline-moon">
                <i class="bi bi-shop me-1"></i> Tienda
            </a>
            <a href="quienes_somos.php" class="btn btn-sm text-dark fw-bold" style="background-color: var(--accent-luna);">
                <i class="bi bi-people me-1"></i> Quiénes Somos
            </a>
        </div>
    </div>
</nav>

<!-- Contenido Principal -->
<div class="container my-5" style="max-width: 900px;">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-beige display-5 mb-2">Quiénes Somos</h1>
        <p class="text-muted-moon fs-5">Conoce al equipo detrás de Moon Essence.</p>
    </div>

    <!-- Mensaje del Grupo -->
    <div class="card bg-transparent border-0 mb-5">
        <div class="card-body text-center p-4" style="background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px;">
            <h3 class="text-beige fw-bold mb-3"><i class="bi bi-stars me-2"></i>Grupo Moon Essence</h3>
            <p class="text-muted-moon fs-5 m-0">
                Los integrantes del grupo Moon Essence somos: <strong class="text-white">Mariana y Evelyn</strong>.
            </p>
        </div>
    </div>

    <!-- Tarjetas de Integrantes -->
    <div class="row g-4 justify-content-center">
        
        <!-- Mariana (Administradora) -->
        <div class="col-md-5">
            <div class="card-integrante">
                <div class="avatar-box">
                    <img src="uploads/Administradora.PNG" alt="Mariana" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="avatar-placeholder" style="display: none;"><i class="bi bi-person-fill"></i></div>
                </div>
                <h4 class="text-beige fw-bold mb-1">Mariana</h4>
                <span class="badge bg-primary mb-3">Administradora</span>
                <p class="text-muted-moon small">Encargada de la gestión administrativa, catálogo y estructura general de la plataforma.</p>
            </div>
        </div>

        <!-- Evelyn (Vendedora) -->
        <div class="col-md-5">
            <div class="card-integrante">
                <div class="avatar-box">
                    <img src="uploads/vendedora.png" alt="Evelyn" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="avatar-placeholder" style="display: none;"><i class="bi bi-person-fill"></i></div>
                </div>
                <h4 class="text-beige fw-bold mb-1">Evelyn</h4>
                <span class="badge bg-success mb-3">Vendedora</span>
                <p class="text-muted-moon small">Encargada de la atención al cliente, seguimiento de pedidos y gestión de ventas.</p>
            </div>
        </div>

    </div>
</div>

<footer class="py-4 mt-auto text-center">
    <div class="container">
        <p class="brand-text fw-bold mb-1 fs-6">Moon Essence</p>
        <p class="small m-0 text-muted-moon">© 2026 - Todos los derechos reservados</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>