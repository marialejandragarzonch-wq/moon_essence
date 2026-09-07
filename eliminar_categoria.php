<?php
// Incluir la conexión a la base de datos
if (file_exists(__DIR__ . '/config/conexion.php')) {
    include_once __DIR__ . '/config/conexion.php';
} elseif (file_exists(__DIR__ . '/conexion.php')) {
    include_once __DIR__ . '/conexion.php';
}

$con = $conexion ?? $conn ?? $db ?? null;

// Verificar que se reciba un ID válido por la URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_categoria = intval($_GET['id']);

    if ($con) {
        // Eliminar la categoría seleccionada
        $query = "DELETE FROM categorias WHERE id_categoria = $id_categoria";
        mysqli_query($con, $query);
    }
}

// Redirigir de vuelta a la lista de categorías
header("Location: admin_categorias.php");
exit();
?>