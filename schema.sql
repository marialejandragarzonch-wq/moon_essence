CREATE DATABASE IF NOT EXISTS moon_essence CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE moon_essence;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO roles (nombre) VALUES ('admin'), ('emprendedor'), ('comprador');

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE CASCADE
);

CREATE TABLE tiendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL UNIQUE,
    nombre_tienda VARCHAR(100) NOT NULL,
    logo VARCHAR(255) DEFAULT 'default_logo.png',
    descripcion TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO categorias (nombre) VALUES ('Camisas'), ('Blusas'), ('Pantalones'), ('Jeans'), ('Chaquetas');

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tienda_id INT NOT NULL,
    categoria_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    imagen VARCHAR(255) DEFAULT 'default_product.png',
    aprobado TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tienda_id) REFERENCES tiendas(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
);

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comprador_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    metodo_pago ENUM('Nequi', 'Efectivo') NOT NULL,
    estado ENUM('Pendiente', 'Pagado', 'Enviado', 'Completado') DEFAULT 'Pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comprador_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE detalle_pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
);