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
    die("Error de conexión a la base de datos.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = $_POST['id_producto'] ?? null;
    $talla = $_POST['talla'] ?? 'M';

    if ($id_producto) {
        // Verificar si el producto existe
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            // Inicializar el carrito en sesión si no existe
            if (!isset($_SESSION['carrito'])) {
                $_SESSION['carrito'] = [];
            }

            // Crear un identificador único para el producto según su talla seleccionada
            $clave_carrito = $id_producto . '_' . $talla;

            // Si ya está en el carrito, sumamos 1 a la cantidad, si no, lo agregamos
            if (isset($_SESSION['carrito'][$clave_carrito])) {
                $_SESSION['carrito'][$clave_carrito]['cantidad']++;
            } else {
                $_SESSION['carrito'][$clave_carrito] = [
                    'id_producto' => $producto['id_producto'],
                    'nombre'      => $producto['nombre_producto'],
                    'precio'      => $producto['precio'],
                    'imagen'      => $producto['imagen'] ?? '',
                    'talla'       => $talla,
                    'cantidad'    => 1
                ];
            }
        }
    }
}

// Redirigir de vuelta a la tienda o al carrito
header("Location: ../index.php");
exit();