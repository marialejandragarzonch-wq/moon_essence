<?php
session_start();
$host = 'localhost';
$db   = 'moon_essence';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo_ingresado = trim($_POST['email'] ?? '');
    $password_ingresado = $_POST['password'] ?? '';

    if (empty($correo_ingresado) || empty($password_ingresado)) {
        header("Location: login.php?error=empty");
        exit();
    }

    // Buscamos al usuario usando tanto 'email' como 'correo' para asegurarnos de que lo encuentre
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? OR correo = ?");
    $stmt->execute([$correo_ingresado, $correo_ingresado]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Verificamos en la columna 'password' o 'contrasena'
        $hash_en_db = $usuario['password'] ?? $usuario['contrasena'] ?? '';

        // Comprobamos si la contraseña coincide (soporta tanto hash como texto plano por seguridad)
        if (password_verify($password_ingresado, $hash_en_db) || $password_ingresado === $hash_en_db) {
            
            // Creamos las variables de sesión para que el saludo aparezca en el index
            $_SESSION['usuario_id'] = $usuario['id_usuario'] ?? $usuario['id'] ?? 1;
            $_SESSION['nombre'] = $usuario['nombre'] ?? 'Usuario';
            $_SESSION['usuario'] = $usuario['email'] ?? $usuario['correo'] ?? '';

            // Redirigimos a la tienda principal con la sesión activa
            header("Location: ../index.php");
            exit();
        }
    }

    // Si falla, regresa al login con error
    header("Location: login.php?error=invalid");
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>