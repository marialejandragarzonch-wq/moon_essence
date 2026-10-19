<?php
// Iniciar la sesión para poder destruirla
session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Borrar la cookie de sesión del navegador si existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir la sesión por completo
session_destroy();

// Redirigir al inicio de sesión
header("Location: login.php");
exit();
?>