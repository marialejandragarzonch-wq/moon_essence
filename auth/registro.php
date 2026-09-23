<?php
session_start();
$host = 'localhost';
$db   = 'moon_essence';
$user = 'root';
$pass = '';

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!empty($nombre) && !empty($email) && !empty($password)) {
            // Encriptar contraseña de forma segura
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Ajusta el nombre de la tabla y columnas si en tu base de datos se llaman diferente
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$nombre, $email, $passwordHash]);

            $exito = "¡Cuenta creada con éxito! Ya puedes iniciar sesión.";
        } else {
            $error = "Por favor completa todos los campos.";
        }
    } catch (PDOException $e) {
        $error = "Este correo ya está registrado o hubo un error en la base de datos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Moon Essence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --bg-cielo: #0b131e;
            --bg-tarjeta: #1a2332;
            --accent-luna: #f9e8d0;
            --texto-suave: #aebbc9;
        }
        body {
            background-color: var(--bg-cielo);
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .card-auth {
            background-color: var(--bg-tarjeta);
            border: 1px solid #28374d;
            border-radius: 14px;
        }
        .brand-text {
            color: var(--accent-luna) !important;
            letter-spacing: 1.5px;
            text-decoration: none;
        }
        .form-control {
            background-color: #121a24;
            border: 1px solid #28374d;
            color: #ffffff;
        }
        .form-control:focus {
            background-color: #121a24;
            border-color: var(--accent-luna);
            color: #ffffff;
            box-shadow: none;
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
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="text-center mb-4">
                <a href="../index.php" class="brand-text fw-bold fs-2">
                    <i class="bi bi-moon-stars-fill me-2"></i>Moon Essence
                </a>
            </div>
            <div class="card card-auth p-4 shadow-lg">
                <h4 class="brand-text mb-3 text-center">Crear una cuenta</h4>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2 small"><?= $error ?></div>
                <?php endif; ?>

                <?php if (!empty($exito)): ?>
                    <div class="alert alert-success py-2 small"><?= $exito ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label text-muted-moon small">Nombre completo</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted-moon small">Correo electrónico</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted-moon small">Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-beigecito w-100 py-2 mt-2">Registrarse</button>
                </form>

                <div class="text-center mt-3">
                    <a href="../index.php" class="text-muted-moon small text-decoration-none"><i class="bi bi-arrow-left me-1"></i> Volver a la tienda</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>