<?php
session_start();
require_once '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        // Consultar buscando por email o por correo para mayor compatibilidad
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? OR correo = ? LIMIT 1");
        $stmt->execute([$email, $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Verificar si la contraseña coincide (encriptada o en texto plano de respaldo)
            $password_valida = password_verify($password, $usuario['password']) || 
                               ($password === $usuario['contrasena']) || 
                               ($password === $usuario['password']);

            if ($password_valida) {
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol'] = $usuario['rol'];

                // Redirección según rol
                if ($usuario['rol'] === 'admin' || $usuario['rol'] === 'vendedor') {
                    header('Location: ../admin/admin_dashboard.php');
                } else {
                    header('Location: ../index.php');
                }
                exit;
            } else {
                $error = 'Contraseña incorrecta.';
            }
        } else {
            $error = 'El correo ingresado no está registrado.';
        }
    } else {
        $error = 'Por favor, completa todos los campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moon Essence - Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        }

        .navbar-moon {
            background-color: rgba(5, 9, 14, 0.95);
            border-bottom: 1px solid #233044;
            backdrop-filter: blur(8px);
        }

        .brand-text {
            color: var(--accent-luna) !important;
            letter-spacing: 1.5px;
        }

        .card-login {
            background-color: var(--bg-tarjeta);
            border: 1px solid #28374d;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .form-control-moon {
            background-color: rgba(5, 9, 14, 0.8) !important;
            border: 1px solid #28374d !important;
            color: #ffffff !important;
            border-radius: 8px;
            padding: 12px 15px;
        }

        .form-control-moon:focus {
            border-color: var(--accent-luna) !important;
            box-shadow: 0 0 10px rgba(249, 232, 208, 0.3);
        }

        .btn-moon {
            background-color: var(--accent-luna);
            color: #0b131e;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .btn-moon:hover {
            background-color: #ffffff;
            color: #0b131e;
            box-shadow: 0 0 12px rgba(249, 232, 208, 0.4);
        }

        .btn-outline-moon {
            border: 1px solid var(--accent-luna);
            color: var(--accent-luna);
            border-radius: 8px;
        }

        .btn-outline-moon:hover {
            background-color: var(--accent-luna);
            color: #0b131e;
        }

        .text-muted-moon {
            color: var(--texto-suave) !important;
        }

        footer {
            background-color: #05090e;
            border-top: 1px solid #233044;
            color: var(--texto-suave);
            margin-top: auto;
        }
    </style>
</head>
<body>

<!-- Menú Superior -->
<nav class="navbar navbar-expand-lg navbar-moon py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3 brand-text" href="../index.php">Moon Essence</a>
        <div class="d-flex align-items-center gap-2">
            <a href="../index.php" class="btn btn-sm btn-outline-moon">Volver a la Tienda</a>
        </div>
    </div>
</nav>

<!-- Tarjeta de Login -->
<div class="container my-5 py-4">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card card-login p-4">
                <div class="text-center mb-4">
                    <h3 class="brand-text fw-bold mb-1">Iniciar Sesión</h3>
                    <p class="text-muted-moon small">Accede a tu cuenta de Moon Essence</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger bg-danger text-white border-0 py-2 small mb-3 text-center" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label small text-muted-moon fw-bold">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control form-control-moon" placeholder="ejemplo@moon.com" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small text-muted-moon fw-bold">Contraseña</label>
                        <input type="password" name="password" class="form-control form-control-moon" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-moon w-100 py-2 mb-3">Iniciar Sesión</button>
                </form>

                <div class="text-center mt-3 border-top border-secondary pt-3">
                    <p class="small text-muted-moon mb-1">¿Aún no tienes cuenta?</p>
                    <a href="registro.php" class="brand-text small fw-bold text-decoration-none">Registrarse / Crear una cuenta</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="py-4">
    <div class="container text-center">
        <p class="brand-text fw-bold mb-1 fs-5">Moon Essence</p>
        <p class="small m-0">© 2026 Todos los derechos reservados.</p>
    </div>
</footer>

</body>
</html>