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

$id_producto = $_GET['id'] ?? null;
$producto = [
    'nombre_producto' => '',
    'descripcion' => '',
    'precio' => '',
    'stock' => '',
    'imagen' => 'default.png'
];

if ($id_producto) {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id_producto = ?");
    $stmt->execute([$id_producto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre_producto'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $imagen = $producto['imagen'];

    // Imagen principal
    if (!empty($_FILES['imagen']['name'])) {
        $imagen = time() . '_1_' . $_FILES['imagen']['name'];
        move_uploaded_file($_FILES['imagen']['tmp_name'], 'uploads/' . $imagen);
    }

    if ($id_producto) {
        $stmt = $pdo->prepare("UPDATE productos SET nombre_producto=?, descripcion=?, precio=?, stock=?, imagen=? WHERE id_producto=?");
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $imagen, $id_producto]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO productos (id_tienda, id_categoria, nombre_producto, descripcion, precio, stock, imagen, aprobado) VALUES (1, 1, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([$nombre, $descripcion, $precio, $stock, $imagen]);
        $id_producto = $pdo->lastInsertId();
    }

    // Imagen secundaria (guarda en fotos_producto)
    if (!empty($_FILES['imagen2']['name'])) {
        $imagen2 = time() . '_2_' . $_FILES['imagen2']['name'];
        move_uploaded_file($_FILES['imagen2']['tmp_name'], 'uploads/' . $imagen2);

        $stmt_foto = $pdo->prepare("INSERT INTO fotos_producto (id_producto, ruta_foto) VALUES (?, ?)");
        $stmt_foto->execute([$id_producto, $imagen2]);
    }

    header('Location: admin_productos.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $id_producto ? 'Editar' : 'Nuevo' ?> Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5" style="max-width: 600px;">
    <div class="card shadow">
        <div class="card-body p-4">
            <h3 class="mb-4"><?= $id_producto ? 'Editar' : 'Nuevo' ?> Producto</h3>
            
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Nombre del Producto</label>
                    <input type="text" name="nombre_producto" class="form-control" value="<?= htmlspecialchars($producto['nombre_producto']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3" required><?= htmlspecialchars($producto['descripcion']) ?></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Precio</label>
                        <input type="number" step="any" name="precio" class="form-control" value="<?= $producto['precio'] ?>" required placeholder="Ej: 95000">
                    </div>
                    <div class="col">
                        <label class="form-label">Stock</label>
                        <input type="number" name="stock" class="form-control" value="<?= $producto['stock'] ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Imagen Principal</label>
                    <input type="file" name="imagen" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Segunda Imagen (Opcional)</label>
                    <input type="file" name="imagen2" class="form-control">
                </div>

                <button type="submit" class="btn btn-success w-100 btn-lg mt-3">Guardar Producto</button>
                <a href="admin_productos.php" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>