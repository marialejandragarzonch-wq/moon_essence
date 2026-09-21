<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Moon Essence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<div class="card shadow p-4" style="width: 100%; max-width: 400px;">
    <div class="card-body">
        <h3 class="text-center mb-4 text-danger fw-bold">Moon Essence</h3>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger py-2" role="alert">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="procesar_login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="correo@ejemplo.com">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-danger w-100 btn-lg mt-3">Ingresar</button>
        </form>
    </div>
</div>

</body>
</html>