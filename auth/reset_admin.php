<?php
$host = 'localhost';
$db   = 'moon_essence';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Generar los hashes seguros nuevos
    $hash_admin    = password_hash('MoonAdmin2026*', PASSWORD_DEFAULT);
    $hash_vendedor = password_hash('VentasMoon2026!', PASSWORD_DEFAULT);
    $hash_cliente  = password_hash('TiendaEssence55$', PASSWORD_DEFAULT);

    // 2. Actualizar utilizando los correos exactos que vemos en phpMyAdmin
    $stmt1 = $pdo->prepare("UPDATE usuarios SET contrasena = :pass WHERE email = 'admin@moon.com' OR correo = 'admin@moon.com'");
    $stmt1->execute(['pass' => $hash_admin]);

    $stmt2 = $pdo->prepare("UPDATE usuarios SET contrasena = :pass WHERE email = 'vendedor@moon.com' OR correo = 'vendedor@moon.com'");
    $stmt2->execute(['pass' => $hash_vendedor]);

    $stmt3 = $pdo->prepare("UPDATE usuarios SET contrasena = :pass WHERE email = 'cliente@moon.com' OR correo = 'cliente@moon.com'");
    $stmt3->execute(['pass' => $hash_cliente]);

    echo "<div style='font-family: Arial, sans-serif; text-align: center; margin-top: 50px;'>";
    echo "<h2 style='color: green;'>¡Las contraseñas de los 3 roles se han actualizado con éxito!</h2>";
    echo "<p>Admin (ID 1) - Filas actualizadas: " . $stmt1->rowCount() . "</p>";
    echo "<p>Vendedor (ID 4) - Filas actualizadas: " . $stmt2->rowCount() . "</p>";
    echo "<p>Cliente (ID 5) - Filas actualizadas: " . $stmt3->rowCount() . "</p>";
    echo "<br><a href='auth/login.php' style='padding: 10px 20px; background-color: #0b131e; color: #fff; text-decoration: none; border-radius: 5px;'>Ir al Login</a>";
    echo "</div>";

} catch (PDOException $e) {
    echo "Error en la base de datos: " . $e->getMessage();
}
?>