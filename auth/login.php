<?php
session_start();

// Si ya hay una sesión activa, redirigir según el rol
if (isset($_SESSION['rol'])) {
    if ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === '1') {
        header("Location: ../admin/admin_dashboard.php");
        exit();
    } else {
        header("Location: ../index.php");
        exit();
    }
}

$host = 'localhost';
$db   = 'moon_essence';
$user = 'root';
$pass = '';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Buscar el usuario por email o correo
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email OR correo = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                // Verificar la contraseña (soporta hash o texto plano según la migración de tu BD)
                $password_valida = false;
                if (isset($usuario['password']) && password_verify($password, $usuario['password'])) {
                    $password_valida = true;
                } elseif (isset($usuario['contrasena']) && password_verify($password, $usuario['contrasena'])) {
                    $password_valida = true;
                } elseif ((isset($usuario['password']) && $usuario['password'] === $password) || (isset($usuario['contrasena']) && $usuario['contrasena'] === $password)) {
                    $password_valida = true;
                }

                if ($password_valida) {
                    // Iniciar sesión guardando datos del usuario
                    $_SESSION['usuario_id'] = $usuario['id_usuario'] ?? $usuario['id'];
                    $_SESSION['nombre']     = $usuario['nombre'] ?? $usuario['nombre_usuario'] ?? 'Usuario';
                    $_SESSION['email']      = $usuario['email'] ?? $usuario['correo'];
                    
                    // Manejo dinámico del rol (por id de rol o texto)
                    $rol = $usuario['rol'] ?? $usuario['rol_id'] ?? 'cliente';
                    $_SESSION['rol'] = $rol;

                    // CORRECCIÓN CLAVE: Redirigir al administrador a la pantalla limpia admin_dashboard.php
                    if ($rol === 'admin' || $rol == 1) {
                        header("Location: ../admin/admin_dashboard.php");
                    } else {
                        header("Location: ../index.php");
                    }
                    exit();
                } else {
                    $error = "Contraseña incorrecta.";
                }
            } else {
                $error = "El correo electrónico no está registrado.";
            }
        } catch (PDOException $e) {
            $error = "Error al conectar con la base de datos.";
        }
    } else {
        $error = "Por favor, completa todos los campos.";
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
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background-color: var(--bg-tarjeta);
            border: 1px solid #28374d;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        .brand-text {
            color: var(--accent-luna) !important;
            letter-spacing: 1.5px;
            text-decoration: none;
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
        .form-control {
            background-color: #0b131e;
            border: 1px solid #28374d;
            color: #ffffff;
        }
        .form-control:focus {
            background-color: #0b131e;
            color: #ffffff;
            border-color: var(--accent-luna);
            box-shadow: none;
        }
        .text-muted-moon {
            color: var(--texto-suave) !important;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <a href="../index.php" class="brand-text fw-bold fs-3">
            <i class="bi bi-moon-stars-fill me-2"></i>Moon Essence
        </a>
        <p class="text-muted-moon small mt-2">Ingresa tus credenciales para acceder</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 small text-center" role="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label text-muted-moon small">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="nombre@ejemplo.com" required>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label text-muted-moon small">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-beigecito w-100 py-2 mb-3">Iniciar Sesión</button>
    </form>

    <div class="text-center mt-3">
        <a href="../index.php" class="text-muted-moon small text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Volver a la Tienda
        </a>
    </div>
</div>

</body>
</html>