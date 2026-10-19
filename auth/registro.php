<?php
session_start();

$host = 'localhost';
$db   = 'moon_essence';
$user = 'root';
$pass = '';

$error_msg = "";
$exito_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $nombre   = trim($_POST['nombre'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Expresión regular solicitada para la contraseña
        $patron_password = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/';

        // 1. Validar la contraseña contra la Expresión Regular
        if (!preg_match($patron_password, $password)) {
            $error_msg = "La contraseña debe tener al menos 8 caracteres, incluir una mayúscula, una minúscula y un número.";
        } else {
            // 2. Verificar si el correo ya existe
            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
            $stmtCheck->execute([':email' => $email]);
            
            if ($stmtCheck->fetchColumn() > 0) {
                $error_msg = "El correo electrónico ya se encuentra registrado.";
            } else {
                // 3. Encriptar la contraseña con BCRYPT
                $password_hash = password_hash($password, PASSWORD_BCRYPT);

                // 4. Registrar al usuario en la base de datos
                $stmtInsert = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol, fecha_registro) 
                                             VALUES (:nombre, :email, :password, 'cliente', NOW())");
                
                $stmtInsert->execute([
                    ':nombre'   => $nombre,
                    ':email'    => $email,
                    ':password' => $password_hash
                ]);

                $exito_msg = "¡Registro exitoso! Ya puedes iniciar sesión con tu cuenta.";
            }
        }
    } catch (PDOException $e) {
        $error_msg = "Error en la base de datos: " . $e->getMessage();
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
            align-items: center;
            justify-content: center;
        }

        .card-registro {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .brand-text {
            color: var(--accent-luna) !important;
            letter-spacing: 1px;
            text-decoration: none;
        }

        .form-control {
            background-color: #0e1724 !important;
            border: 1px solid var(--border-color) !important;
            color: #ffffff !important;
            padding: 12px 15px;
            border-radius: 8px;
        }

        .form-control:focus {
            border-color: var(--accent-luna) !important;
            box-shadow: 0 0 8px rgba(249, 232, 208, 0.2);
        }

        .form-control::placeholder {
            color: #5d708a !important;
        }

        .btn-moon {
            background-color: var(--accent-luna) !important;
            color: #0b131e !important;
            font-weight: 700;
            border: none;
            padding: 12px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .btn-moon:hover {
            background-color: #ffffff !important;
            color: #0b131e !important;
        }

        .text-muted-moon {
            color: var(--texto-suave) !important;
        }

        .req-list {
            font-size: 0.8rem;
            color: var(--texto-suave);
            margin-top: 6px;
            padding-left: 20px;
        }
    </style>
</head>
<body>

<div class="card-registro">
    <div class="text-center mb-4">
        <a href="../index.php" class="brand-text fs-3 fw-bold d-block mb-1">
            <i class="bi bi-moon-stars-fill me-2"></i>Moon Essence
        </a>
        <p class="text-muted-moon small">Crea tu cuenta para gestionar tus compras</p>
    </div>

    <!-- Alertas de Error o Éxito -->
    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger p-3 small text-center mb-4">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= htmlspecialchars($error_msg) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($exito_msg)): ?>
        <div class="alert alert-success p-3 small text-center mb-4">
            <i class="bi bi-check-circle-fill me-1"></i> <?= htmlspecialchars($exito_msg) ?>
            <div class="mt-2">
                <a href="login.php" class="btn btn-sm btn-outline-success fw-bold">Iniciar Sesión Ahora</a>
            </div>
        </div>
    <?php else: ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label text-white fw-medium">Nombre Completo</label>
                <input type="text" class="form-control" name="nombre" placeholder="Ej. Ana María" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-white fw-medium">Correo Electrónico</label>
                <input type="email" class="form-control" name="email" placeholder="tu@email.com" required>
            </div>

            <div class="mb-4">
                <label class="form-label text-white fw-medium">Contraseña Segura</label>
                <input type="password" 
                       class="form-control" 
                       name="password" 
                       placeholder="••••••••" 
                       required 
                       pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                       title="Debe contener al menos 8 caracteres, una letra mayúscula, una minúscula y un número.">
                
                <ul class="req-list">
                    <li>Mínimo 8 caracteres</li>
                    <li>Al menos una letra mayúscula (A-Z)</li>
                    <li>Al menos una letra minúscula (a-z)</li>
                    <li>Al menos un número (0-9)</li>
                </ul>
            </div>

            <button type="submit" class="btn btn-moon w-100 fw-bold shadow">
                <i class="bi bi-person-plus-fill me-1"></i> Crear Cuenta
            </button>
        </form>

    <?php endif; ?>

    <div class="text-center mt-4 pt-3 border-top border-secondary">
        <span class="text-muted-moon small">¿Ya tienes una cuenta?</span>
        <a href="login.php" class="brand-text fw-bold small ms-1">Inicia Sesión</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>