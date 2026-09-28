<?php
session_start();
$datos = $_SESSION['datos_pedido'] ?? null;

if (!$datos) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Moon Essence - ¡Gracias por tu Compra!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #0b131e; color: #ffffff; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card-custom { background-color: #1a2332; border: 1px solid #28374d; border-radius: 16px; max-width: 550px; width: 100%; }
        .brand-text { color: #f9e8d0 !important; }
        .btn-beigecito { background-color: #f9e8d0; color: #0b131e; border: none; font-weight: 600; }
        .btn-beigecito:hover { background-color: #ffffff; color: #0b131e; }
    </style>
</head>
<body>

<div class="card card-custom p-4 text-center shadow-lg my-4">
    <div class="my-3">
        <i class="bi bi-moon-stars-fill display-1 brand-text"></i>
    </div>
    
    <h2 class="brand-text fw-bold">¡Gracias por tu compra, <?= htmlspecialchars($datos['nombre']) ?>!</h2>
    <p class="text-muted fs-6">Tu pedido ha sido procesado bajo la luna con éxito.</p>

    <div class="bg-dark bg-opacity-50 p-3 rounded-3 text-start my-3 border border-secondary">
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Número de Orden:</span>
            <span class="fw-bold text-warning"><?= $datos['orden_id'] ?></span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Dirección de entrega:</span>
            <span><?= htmlspecialchars($datos['direccion']) ?>, <?= htmlspecialchars($datos['ciudad']) ?></span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Método de pago:</span>
            <span><?= htmlspecialchars($datos['metodo_pago']) ?></span>
        </div>
        <div class="d-flex justify-content-between">
            <span class="text-muted">Total pagado:</span>
            <span class="fw-bold text-success">$<?= number_format($datos['total'], 0, ',', '.') ?></span>
        </div>
    </div>

    <p class="small text-muted mb-4">
        <i class="bi bi-envelope-check me-1"></i> Hemos registrado tu orden. Te llamaremos al teléfono <strong><?= htmlspecialchars($datos['telefono']) ?></strong> para coordinar la entrega.
    </p>

    <a href="index.php" class="btn btn-beigecito py-2 px-4">
        <i class="bi bi-bag me-1"></i> Volver a la Tienda
    </a>
</div>

</body>
</html>