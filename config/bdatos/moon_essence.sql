-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-09-2026 a las 18:14:13
-- Versión del servidor: 10.1.38-MariaDB
-- Versión de PHP: 7.1.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `moon_essence`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `nombre_categoria` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `nombre_categoria`, `descripcion`, `estado`) VALUES
(1, 'Chaquetas', 'Chaquetas', 'Colección de chaquetas, abrigos y prendas exteriores', 'Activo'),
(2, 'Faldas', 'Faldas', 'Faldas cortas, midi y diseños fluidos', 'Activo'),
(3, 'Básicos', 'Básicos', 'Camisetas básicas, tops y prendas esenciales', 'Activo'),
(4, 'Sastre', 'Sastre', 'Chalecos, blazers y prendas de corte elegante', 'Activo'),
(5, 'Calzado', 'Calzado', 'Tenis, zapatos mary jane y calzado exclusivo', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedidos`
--

CREATE TABLE `detalle_pedidos` (
  `id_detalle` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fotos_producto`
--

CREATE TABLE `fotos_producto` (
  `id_foto` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `url_imagen` varchar(255) NOT NULL,
  `ruta_foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `fotos_producto`
--

INSERT INTO `fotos_producto` (`id_foto`, `id_producto`, `url_imagen`, `ruta_foto`) VALUES
(1, 4, '', '1787580151_2_Chaqueta de jean Oversize 2.png'),
(2, 5, '', '1787581126_2_Camiseta beige 2.png'),
(7, 7, '', '1787584584_2_Falda corta azul 2.png'),
(8, 8, '', '1787584696_2_Falda larga 2.png'),
(9, 9, '', '1787584830_2_1787584233_2_Pantalon Morado 2.PNG'),
(10, 10, '', '1788186466_2_Top cafe 2.png'),
(11, 11, '', '1788188984_2_gabardina 2.PNG'),
(12, 12, '', '1788189261_2_Pantalon 2.png'),
(13, 13, '', '1788189485_2_Blazer Negro 2.png'),
(14, 14, '', '1788189681_2_Chaleco de sastre 2.png'),
(15, 15, '', '1788189999_2_Falda satinada 2.png'),
(16, 16, '', '1788190253_2_Buzo 2.png'),
(17, 17, '', '1788190461_2_Zapatos 2.png'),
(18, 18, '', '1788190787_2_Zapatos NB 2.png'),
(19, 19, '', '1788191232_2_Zapatillas 2.png'),
(20, 20, '', '1788191478_2_Botas 2.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id_notificacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `leido` tinyint(1) DEFAULT '0',
  `fecha_envio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_pedido` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) NOT NULL,
  `metodo_pago` enum('nequi','efectivo') NOT NULL,
  `estado_pedido` enum('pendiente','pagado','enviado','entregado','cancelado') DEFAULT 'pendiente',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `nombre_cliente` varchar(100) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `direccion` text,
  `ciudad` varchar(100) DEFAULT NULL,
  `notas` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id_pedido`, `id_usuario`, `fecha_pedido`, `total`, `metodo_pago`, `estado_pedido`, `fecha_creacion`, `nombre_cliente`, `telefono`, `direccion`, `ciudad`, `notas`) VALUES
(1, 1, '2026-08-19 15:25:14', '85000.00', 'nequi', 'pendiente', '2026-08-19 10:25:14', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `id_tienda` int(11) NOT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `nombre_producto` varchar(100) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT '0',
  `imagen` varchar(255) DEFAULT 'default.png',
  `estado_aprobacion` enum('pendiente','aprobado','rechazado') DEFAULT 'pendiente',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `aprobado` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `id_tienda`, `id_categoria`, `nombre_producto`, `descripcion`, `precio`, `stock`, `imagen`, `estado_aprobacion`, `fecha_creacion`, `aprobado`) VALUES
(4, 1, 1, 'Chaqueta oversize Denim', 'Chaqueta de mezclilla holgada con botones y bolsillos.', '95000.00', 8, '1787580151_1_Chaqueta de jean Oversize.png', 'pendiente', '2026-08-24 14:02:31', 1),
(5, 1, 3, 'Camiseta Básica Beige', 'Camiseta de algodón manga corta y cuello redondo.', '30000.00', 20, '1787581126_1_Camiseta beige .png', 'pendiente', '2026-08-24 14:18:46', 1),
(7, 1, 2, 'Falda Azul De Volantes', 'Minifalda con cintura elástica y diseño en capas.', '45000.00', 12, '1787584584_1_Falda corta azul .png', 'pendiente', '2026-08-24 15:16:24', 1),
(8, 1, 2, 'Falda Asimétrica Cuadros', 'Falda midi de cuadros con diseño moderno.', '65000.00', 11, '1787584696_1_Falda larga .png', 'pendiente', '2026-08-24 15:18:16', 1),
(9, 1, 4, 'Pantalón Sastre Morado', 'Pantalón elegante de talle alto y pierna ancha.', '70000.00', 8, '1787584830_1_1787584233_1_Pantalon Morado 1.PNG', 'pendiente', '2026-08-24 15:20:30', 1),
(10, 1, 3, 'Top Acanalado Café', 'Top básico de algodón acanalado color marrón, cuello redondo y silueta ajustada.', '45000.00', 20, '1788186466_1_Top cafe.png', 'pendiente', '2026-08-31 14:27:46', 1),
(11, 1, 1, 'Gabardina Beige Larga', 'Gabardina clásica para capas, color beige claro, con doble botonadura y cinturón ajustable. Largo hasta la rodilla.', '125000.00', 35, '1788188984_1_gabardinada 1.PNG', 'pendiente', '2026-08-31 15:09:44', 1),
(12, 1, 3, 'Jeans Wide Leg De Tiro Alto', 'Pantalón vaquero de corte ancho y silueta estilizada con tiro alto en azul clásico.', '110000.00', 39, '1788189261_1_Pantalon 1.png', 'pendiente', '2026-08-31 15:14:21', 1),
(13, 1, 1, 'Blazer Oversized Negro', 'Saco sastre de corte amplio y formal con solapas y un botón frontal.', '140000.00', 20, '1788189485_1_Blazer Negro 1.png', 'pendiente', '2026-08-31 15:18:05', 1),
(14, 1, 4, 'Chaleco Sastre Azul', 'Chaleco sastre entallado de diseño formal con botones frontales y escote en v.', '85000.00', 25, '1788189681_1_Chaleco de sastre 1.png', 'pendiente', '2026-08-31 15:21:21', 1),
(15, 1, 2, 'Falda Midi Satinada', 'Falda fluida de corte midi en tejido satinado con brillo sutil y elegante.', '90000.00', 19, '1788189999_1_Falda satinada 1.png', 'pendiente', '2026-08-31 15:26:39', 1),
(16, 1, 3, 'Buzo/Hoodie Oversize', 'Sudadera con capucha de corte amplio y interior afelpado para máximo confort.', '100000.00', 26, '1788190253_1_Buzo 1.png', 'pendiente', '2026-08-31 15:30:53', 1),
(17, 1, 5, 'Tenis Samba Borgoña', 'Zapatillas estilo retro en tono Vinotinto con franjas blancas y suela de goma.', '160000.00', 20, '1788190461_1_Zapatos 1.png', 'pendiente', '2026-08-31 15:34:21', 1),
(18, 1, 5, 'Tenis NB 530 White/Silver', 'Zapatillas deportivas estilo chunky en color blanco con detalles metalizados y suela amortiguada.', '180000.00', 16, '1788190787_1_Zpatos NB 1.png', 'pendiente', '2026-08-31 15:39:47', 1),
(19, 1, 5, 'Mary Jane Vinotinto Doble Correa', 'Zapatos tipo salón de charol en tono Vinotinto con punta cuadrada y doble correa con hebilla.', '115000.00', 24, '1788191232_1_Zapatillas 1.png', 'pendiente', '2026-08-31 15:47:12', 1),
(20, 1, 5, 'Botas Altas Café Tacón', 'Botas altas de cuero sintético en color marrón oscuro, tacón ancho y cierre lateral interno.', '150000.00', 15, '1788191478_1_Botas 1.png', 'pendiente', '2026-08-31 15:51:18', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiendas`
--

CREATE TABLE `tiendas` (
  `id_tienda` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `nombre_tienda` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `descripcion` text,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tiendas`
--

INSERT INTO `tiendas` (`id_tienda`, `usuario_id`, `id_usuario`, `nombre_tienda`, `logo`, `descripcion`, `fecha_creacion`) VALUES
(1, NULL, 1, 'Moon Store Oficial', NULL, 'Ropa urbana y diseños exclusivos', '2026-08-19 14:07:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` varchar(50) DEFAULT 'cliente',
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rol_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `email`, `password`, `correo`, `contrasena`, `rol`, `fecha_registro`, `rol_id`) VALUES
(1, 'Administrador Principal', 'admin@moon.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1X.wXuYGG0M80D1bT.5W3Kk4Yx7o5.C', 'admin@moon.com', '123456', 'admin', '2026-08-19 14:05:29', 2),
(4, 'Vendedor Tienda', 'vendedor@moon.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1X.wXuYGG0M80D1bT.5W3Kk4Yx7o5.C', 'vendedor@moon.com', '123456', 'vendedor', '2026-09-23 11:53:40', NULL),
(5, 'Cliente Ejemplo', 'cliente@moon.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1X.wXuYGG0M80D1bT.5W3Kk4Yx7o5.C', 'cliente@moon.com', '123456', 'cliente', '2026-09-23 11:53:40', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nombre_categoria` (`nombre_categoria`);

--
-- Indices de la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `fotos_producto`
--
ALTER TABLE `fotos_producto`
  ADD PRIMARY KEY (`id_foto`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id_notificacion`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_tienda` (`id_tienda`),
  ADD KEY `productos_ibfk_2` (`id_categoria`);

--
-- Indices de la tabla `tiendas`
--
ALTER TABLE `tiendas`
  ADD PRIMARY KEY (`id_tienda`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `fotos_producto`
--
ALTER TABLE `fotos_producto`
  MODIFY `id_foto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `tiendas`
--
ALTER TABLE `tiendas`
  MODIFY `id_tienda` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD CONSTRAINT `detalle_pedidos_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_pedidos_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `fotos_producto`
--
ALTER TABLE `fotos_producto`
  ADD CONSTRAINT `fotos_producto_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_tienda`) REFERENCES `tiendas` (`id_tienda`) ON DELETE CASCADE,
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `tiendas`
--
ALTER TABLE `tiendas`
  ADD CONSTRAINT `tiendas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
