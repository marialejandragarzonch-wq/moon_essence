<?php
session_start();
$host = 'localhost';
$db   = 'moon_essence'; 
$user = 'root';
$pass = ''; 

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Expresión regular para validar políticas de contraseña segura
    $patron_password = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/';

    if (!empty($nombre) && !empty($email) && !empty($password)) {
        // Validar contraseña con la expresión regular solicitada
        if (!preg_match($patron_password, $password)) {
            $error = "La contraseña no cumple con los requisitos de seguridad establecidos.";
        } else {
            try {
                $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // 1. Verificar si el correo ya está registrado (Sincronizado con 'email' => $email)
                // $stmt_check = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email OR correo = :email LIMIT 1");
                $stmt_check = $pdo->prepare("SELECT * FROM usuarios WHERE correo = :email LIMIT 1");
                $stmt_check->execute(['email' => $email]);
                
                if ($stmt_check->rowCount() > 0) {
                    $error = "El correo electrónico ya se encuentra registrado.";
                } else {
                    // Encriptar contraseña de forma segura
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $rol_por_defecto = 'cliente';

                    // 2. Consulta SQL de inserción con los 4 parámetros exactos
                    $stmt_insert = $pdo->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES (:nombre, :correo, :password, :rol)");

                    $stmt_insert->execute([
                        'nombre'   => $nombre,
                        'correo'   => $email,
                        'password' => $password_hash,
                        'rol'      => $rol_por_defecto
                    ]);

                    $exito = "¡Cuenta registrada con éxito! Ya puedes iniciar sesión.";
                }
            } catch (PDOException $e) {
                // Muestra el error exacto de MySQL en caso de que ocurra algún inconveniente
                echo "<div style='color: red; background: #ffe6e6; padding: 15px; margin: 10px; border-radius: 5px; font-family: Arial;'>";
                echo "<b>Error exacto de MySQL:</b> " . $e->getMessage();
                echo "</div>";
            }
        }
    } else {
        $error = "Por favor completa todos los campos del formulario.";
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
        .registro-card {
            background-color: #16202c;
            border: 1px solid #233044;
            border-radius: 16px;
            width: 100%;
            max-width: 440px;
            padding: 35px 32px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
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
        }
        .link-back:hover { color: #ffffff; }
    </style>
</head>
<body>

<div class="registro-card">
    <div class="text-center mb-4">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
            <i class="bi bi-moon-stars-fill fs-3" style="color: #f5e6d3;"></i>
            <h2 class="fw-bold m-0" style="color: #f5e6d3; letter-spacing: 1px;">Moon Essence</h2>
        </div>
        <p class="text-white-50 small">Crea tu cuenta para gestionar tus compras</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 small mb-3 text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($exito)): ?>
        <div class="alert alert-success py-2 small mb-3 text-center"><?= htmlspecialchars($exito) ?></div>
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
        <div class="mb-3">
            <label class="form-label small text-white-50 mb-1">Contraseña Segura</label>
            <input type="password" name="password" class="form-control form-control-custom" placeholder="••••••••" required>
            <ul class="text-white-50 small mt-2 ps-3" style="font-size: 0.8rem; line-height: 1.4;">
                <li>Mínimo 8 caracteres</li>
                <li>Al menos una letra mayúscula (A-Z)</li>
                <li>Al menos una letra minúscula (a-z)</li>
                <li>Al menos un número (0-9)</li>
            </ul>
        </div>
        <button type="submit" class="btn-submit-custom">Crear Cuenta</button>
    </form>

    <div class="text-center mt-3">
        <a href="login.php" class="link-back">¿Ya tienes una cuenta? <strong>Inicia Sesión</strong></a>
    </div>
</div>

</body>
</html>