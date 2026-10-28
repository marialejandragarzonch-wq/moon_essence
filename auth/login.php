<?php
session_start();
$host = 'localhost';
$db   = 'moon_essence';
$user = 'root';
$pass = '';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_ingresado = trim($_POST['email'] ?? '');
    $password_ingresado = trim($_POST['password'] ?? '');

    if (!empty($email_ingresado) && !empty($password_ingresado)) {
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Búsqueda corregida utilizando únicamente la columna existente 'correo'
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = :correo LIMIT 1");
            $stmt->execute(['correo' => $email_ingresado]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

           if ($usuario) {
                // Leemos el hash correctamente de la columna 'contrasena'
                $pass_hash = $usuario['contrasena'] ?? '';
                $nombre_usuario = $usuario['nombre'] ?? 'Usuario';
                $rol_usuario = strtolower(trim($usuario['rol'] ?? ''));

                $es_valida = false;

                // Verificamos el hash con password_verify o comparación directa por compatibilidad
                if (!empty($pass_hash) && password_verify($password_ingresado, $pass_hash)) {
                    $es_valida = true;
                } elseif ($password_ingresado === $pass_hash) {
                    $es_valida = true;
                }

                if ($es_valida) {
                    $_SESSION['usuario_id'] = $usuario['id_usuario'] ?? 1;
                    $_SESSION['nombre'] = $nombre_usuario;
                    $_SESSION['usuario'] = $nombre_usuario;
                    $_SESSION['rol'] = $rol_usuario;

                    // Redirección exacta al panel de administración o a la tienda según el rol
                    if ($rol_usuario === 'admin') {
                        header("Location: ../admin/admin_dashboard.php");
                    } else {
                        header("Location: ../index.php");
                    }
                    exit();
                } else {
                    $error = "Contraseña incorrecta.";
                }
            } else {
                $error = "El correo no está registrado.";
            }
        } catch (PDOException $e) {
            $error = "Error de conexión con la base de datos: " . $e->getMessage();
        }
    } else {
        $error = "Por favor completa todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Moon Essence</title>
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
        }
        .login-card {
            background-color: #16202c;
            border: 1px solid #233044;
            border-radius: 16px;
            width: 100%;
            max-width: 440px;
            padding: 40px 32px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            text-align: center;
        }
        .form-control-custom {
            background-color: #0d1520;
            border: 1px solid #202d3d;
            color: #ffffff;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.9rem;
        }
        .form-control-custom:focus {
            background-color: #0d1520;
            color: #ffffff;
            border-color: #f5e6d3;
            box-shadow: none;
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
            margin-top: 10px;
        }
        .btn-submit-custom:hover { background-color: #ffffff; }
        .link-back {
            color: #8fa0b5;
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-block;
            margin-top: 20px;
        }
        .link-back:hover { color: #ffffff; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
        <i class="bi bi-moon-stars-fill fs-3" style="color: #f5e6d3;"></i>
        <h2 class="fw-bold m-0" style="color: #f5e6d3; letter-spacing: 1px;">Moon Essence</h2>
    </div>
    <p class="text-white-50 small mb-4">Ingresa tus credenciales para acceder</p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 small mb-3"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST" class="text-start">
        <div class="mb-3">
            <label class="form-label small text-white-50 mb-1">Correo Electrónico</label>
            <input type="email" name="email" class="form-control form-control-custom" placeholder="nombre@ejemplo.com" required>
        </div>
        <div class="mb-4">
            <label class="form-label small text-white-50 mb-1">Contraseña</label>
            <input type="password" name="password" class="form-control form-control-custom" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn-submit-custom">Iniciar Sesión</button>
    </form>

    <a href="../index.php" class="link-back">
        <i class="bi bi-arrow-left me-1"></i> Volver a la Tienda
    </a>
</div>

</body>
</html>