-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 29-10-2025 a las 22:11:05
-- Versión del servidor: 11.8.3-MariaDB-log
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u659361960_todofrenos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `codigos_verificacion`
--

CREATE TABLE `codigos_verificacion` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `codigo` varchar(6) NOT NULL,
  `tipo` enum('registro','login') NOT NULL,
  `expira_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `usado` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `codigos_verificacion`
--

INSERT INTO `codigos_verificacion` (`id`, `usuario_id`, `correo`, `codigo`, `tipo`, `expira_en`, `usado`, `created_at`) VALUES
(49, 27, 'jhonj117c@gmail.com', '200141', 'registro', '2025-10-22 01:18:27', 1, '2025-10-22 01:17:14'),
(54, 28, 'ivonnedgonzaleza@gmail.com', '623682', 'registro', '2025-10-22 01:59:16', 1, '2025-10-22 01:58:02'),
(55, 28, 'ivonnedgonzaleza@gmail.com', '692647', 'login', '2025-10-22 02:00:00', 1, '2025-10-22 01:59:30'),
(60, 29, 'jo8555725@gmail.com', '600532', 'registro', '2025-10-25 23:36:01', 1, '2025-10-25 23:34:36'),
(74, 32, 'ximenaherreraflorez12@gmail.com', '853236', 'registro', '2025-10-28 11:47:28', 1, '2025-10-28 11:46:35'),
(76, 33, 'bayonasebastian810@gmail.com', '152382', 'registro', '2025-10-28 15:08:42', 1, '2025-10-28 15:07:51'),
(85, 27, 'jhonj117c@gmail.com', '303996', 'login', '2025-10-29 21:28:02', 0, '2025-10-29 21:23:02'),
(86, 27, 'jhonj117c@gmail.com', '533292', 'login', '2025-10-29 21:24:38', 1, '2025-10-29 21:24:17'),
(88, 29, 'jo8555725@gmail.com', '927945', 'login', '2025-10-29 21:58:56', 1, '2025-10-29 21:58:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log_movimientos`
--

CREATE TABLE `log_movimientos` (
  `id` int(11) NOT NULL,
  `movimiento_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `tipo_movimiento` enum('entrada','salida') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `referencia` varchar(255) DEFAULT '',
  `notas` text DEFAULT '',
  `usuario_id` int(11) NOT NULL,
  `fecha_log` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `log_movimientos`
--

INSERT INTO `log_movimientos` (`id`, `movimiento_id`, `producto_id`, `tipo_movimiento`, `cantidad`, `precio_unitario`, `referencia`, `notas`, `usuario_id`, `fecha_log`) VALUES
(12, 18, 11, 'salida', 10, 100000.00, '2131', '', 27, '2025-10-29 21:30:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `tipo` enum('entrada','salida') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `referencia` varchar(255) DEFAULT '',
  `notas` text DEFAULT '',
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimientos`
--

INSERT INTO `movimientos` (`id`, `producto_id`, `tipo`, `cantidad`, `precio_unitario`, `referencia`, `notas`, `fecha`, `usuario_id`, `created_at`, `updated_at`) VALUES
(18, 11, 'salida', 10, 100000.00, '2131', '', '2025-10-29 21:30:25', 27, '2025-10-29 21:30:25', '2025-10-29 21:30:25');

--
-- Disparadores `movimientos`
--
DELIMITER $$
CREATE TRIGGER `actualizar_cantidad_producto` AFTER INSERT ON `movimientos` FOR EACH ROW BEGIN
    IF NEW.tipo = 'entrada' THEN
        UPDATE productos 
        SET cantidad = cantidad + NEW.cantidad 
        WHERE id = NEW.producto_id;
    ELSEIF NEW.tipo = 'salida' THEN
        UPDATE productos 
        SET cantidad = cantidad - NEW.cantidad 
        WHERE id = NEW.producto_id;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_movimientos` AFTER INSERT ON `movimientos` FOR EACH ROW BEGIN
    INSERT INTO log_movimientos (
        movimiento_id, 
        producto_id, 
        tipo_movimiento, 
        cantidad,
        precio_unitario,
        referencia,
        notas,
        usuario_id, 
        fecha_log
    ) VALUES (
        NEW.id, 
        NEW.producto_id, 
        NEW.tipo, 
        NEW.cantidad,
        NEW.precio_unitario,
        NEW.referencia,
        NEW.notas,
        NEW.usuario_id, 
        NOW()
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `validar_stock_salida` BEFORE INSERT ON `movimientos` FOR EACH ROW BEGIN
    DECLARE stock_actual INT DEFAULT 0;
    
    IF NEW.tipo = 'salida' THEN
        SELECT cantidad INTO stock_actual 
        FROM productos 
        WHERE id = NEW.producto_id;
        
        IF stock_actual < NEW.cantidad THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Error: No hay suficiente stock disponible';
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(255) NOT NULL,
  `cantidad` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `imagen`, `cantidad`) VALUES
(11, 'disco de freno(trw)', 'instalale a tu vehiculo tus nuevos discos de freno', 100000.00, '1756343522_images (1).jpg', 150),
(17, 'MANUAL DEL USUARIO', 'este qr los llevara a un video explicativo sobre el uso de todo el sistema de informacion de inventario de la empresa todo frenos juanjo', 0.00, '1761774575_manual de usuario.png', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `correo_verificado` tinyint(1) DEFAULT 0,
  `rol` enum('usuario','admin','administrador','dueño','empleado') NOT NULL DEFAULT 'usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `correo`, `clave`, `correo_verificado`, `rol`) VALUES
(27, 'jj', 'jhonj117c@gmail.com', '$2y$10$mSxxb.uLYqeUWbLWpzo5teMJS0VaoS3xTlINgECk..6CsTyBkbjzy', 1, 'administrador'),
(28, 'Ivonne ', 'ivonnedgonzaleza@gmail.com', '$2y$10$OPAt9o.XmBvD5YYDbFBljevlnbpk1kqoWGtW8wKdZrN2D5TCv4ABi', 1, 'empleado'),
(29, 'juan_srr', 'jo8555725@gmail.com', '$2y$10$7KWIj3NyaxVK/NX5sFlEkuZ9422Vcbhf2rmnayRWPG319uFGE/JB.', 1, 'usuario'),
(32, 'oko', 'ximenaherreraflorez12@gmail.com', '$2y$10$q7wu033nre1ffObr2yv5cuyIqL7jMMFB4vBy7/xjm1hw9VZWgCodS', 1, 'usuario'),
(33, 'Sebastian', 'bayonasebastian810@gmail.com', '$2y$10$njRwgacXPuWclUs2.7rtMON8lSU2SAZc2daiyhbc2Vy0K0X.wTi3y', 1, 'usuario');

--
-- Disparadores `usuarios`
--
DELIMITER $$
CREATE TRIGGER `validar_email_unico` BEFORE INSERT ON `usuarios` FOR EACH ROW BEGIN
    DECLARE email_count INT DEFAULT 0;
    
    SELECT COUNT(*) INTO email_count 
    FROM usuarios 
    WHERE correo = NEW.correo;
    
    IF email_count > 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Error: El correo electronico ya esta registrado';
    END IF;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `codigos_verificacion`
--
ALTER TABLE `codigos_verificacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_tipo` (`usuario_id`,`tipo`),
  ADD KEY `idx_correo_codigo` (`correo`,`codigo`),
  ADD KEY `idx_expira` (`expira_en`);

--
-- Indices de la tabla `log_movimientos`
--
ALTER TABLE `log_movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_movimiento` (`movimiento_id`),
  ADD KEY `idx_fecha_log` (`fecha_log`),
  ADD KEY `idx_producto_log` (`producto_id`),
  ADD KEY `idx_usuario_log` (`usuario_id`);

--
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_producto` (`producto_id`),
  ADD KEY `idx_fecha` (`fecha`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_usuario` (`usuario_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `codigos_verificacion`
--
ALTER TABLE `codigos_verificacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT de la tabla `log_movimientos`
--
ALTER TABLE `log_movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `codigos_verificacion`
--
ALTER TABLE `codigos_verificacion`
  ADD CONSTRAINT `fk_codigos_verificacion_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `log_movimientos`
--
ALTER TABLE `log_movimientos`
  ADD CONSTRAINT `fk_log_movimiento` FOREIGN KEY (`movimiento_id`) REFERENCES `movimientos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_log_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_log_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD CONSTRAINT `movimientos_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `movimientos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
