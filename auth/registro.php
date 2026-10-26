<?php
session_start();
$host = 'localhost';
$db   = 'moon_essence';
$user = 'root';
$pass = '';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($nombre) && !empty($email) && !empty($password)) {
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                $error = "El correo ya se encuentra registrado.";
            } else {
                $passHash = password_hash($password, PASSWORD_DEFAULT);
                $stmtIns = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :pass)");
                $stmtIns->execute([
                    'nombre' => $nombre,
                    'email'  => $email,
                    'pass'   => $passHash
                ]);

                $_SESSION['usuario_id'] = $pdo->lastInsertId();
                $_SESSION['nombre'] = $nombre;
                $_SESSION['usuario'] = $nombre;

                header("Location: ../index.php");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Error al registrar la cuenta.";
        }
    } else {
        $error = "Por favor completa todos los campos requeridos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - Moon Essence</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #0b131e;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        .register-card {
            background-color: #16202c;
            border: 1px solid #233044;
            border-radius: 16px;
            width: 100%;
            max-width: 440px;
            padding: 36px 32px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            text-align: center;
        }
        .form-control-custom {
            background-color: #0d1520;
            border: 1px solid #202d3d;
            color: #ffffff;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 0.9rem;
        }
        .form-control-custom:focus {
            background-color: #0d1520;
            color: #ffffff;
            border-color: #f5e6d3;
            box-shadow: none;
        }
        .req-list {
            text-align: left;
            font-size: 0.75rem;
            color: #8fa0b5;
            padding-left: 18px;
            margin-top: 8px;
            margin-bottom: 16px;
        }
        .btn-submit-custom {
            background-color: #f5e6d3;
            color: #0b131e;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            padding: 12px;
            width: 100%;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .btn-submit-custom:hover { background-color: #ffffff; }
        .link-login {
            color: #ffffff;
            font-weight: 700;
            text-decoration: none;
        }
        .link-login:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="register-card">
    <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
        <i class="bi bi-moon-stars-fill fs-3" style="color: #f5e6d3;"></i>
        <h2 class="fw-bold m-0" style="color: #f5e6d3; letter-spacing: 1px;">Moon Essence</h2>
    </div>
    <p class="text-white-50 small mb-4">Crea tu cuenta para gestionar tus compras</p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 small mb-3"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="registro.php" method="POST" class="text-start">
        <div class="mb-3">
            <label class="form-label small text-white-50 mb-1">Nombre Completo</label>
            <input type="text" name="nombre" class="form-control form-control-custom" placeholder="Ej. Ana María" required>
        </div>

        <div class="mb-3">
            <label class="form-label small text-white-50 mb-1">Correo Electrónico</label>
            <input type="email" name="email" class="form-control form-control-custom" placeholder="tu@email.com" required>
        </div>

        <div class="mb-2">
            <label class="form-label small text-white-50 mb-1">Contraseña Segura</label>
            <input type="password" name="password" class="form-control form-control-custom" placeholder="••••••••" required>
        </div>

        <ul class="req-list">
            <li>Mínimo 8 caracteres</li>
            <li>Al menos una letra mayúscula (A-Z)</li>
            <li>Al menos una letra minúscula (a-z)</li>
            <li>Al menos un número (0-9)</li>
        </ul>

        <button type="submit" class="btn-submit-custom mb-3">
            <i class="bi bi-person-plus-fill"></i> Crear Cuenta
        </button>
    </form>

    <hr style="border-color: #233044;" class="my-3">

    <p class="small text-white-50 m-0">
        ¿Ya tienes una cuenta? <a href="login.php" class="link-login">Inicia Sesión</a>
    </p>
</div>

</body>
</html>