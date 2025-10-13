-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-10-2025 a las 18:36:48
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `todofrenos`
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
(43, 22, 'jhonj117c@gmail.com', '431057', 'login', '2025-10-13 16:33:50', 1, '2025-10-13 16:32:26');

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
(1, 11, 'entrada', 30, 800000.00, '8009', '', '2025-10-04 16:37:19', 22, '2025-10-04 16:37:19', '2025-10-04 16:37:19'),
(3, 11, 'salida', 100, 100000.00, '9090', 'raksjdlkasjdkl', '2025-10-04 17:35:07', 22, '2025-10-04 17:35:07', '2025-10-04 17:35:07'),
(6, 11, 'entrada', 80, 90000.00, '5789', 'khnk', '2025-10-05 00:03:42', 22, '2025-10-05 00:03:42', '2025-10-05 00:03:42');

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
(11, 'freno', 'asjdljskcmviosjioa', 10000.00, '1756343522_images (1).jpg', 90);

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
  `rol` enum('dueño','admin','empleado','usuario') NOT NULL DEFAULT 'usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `correo`, `clave`, `correo_verificado`, `rol`) VALUES
(6, 'Dani', 'danieladaerrano23@gmail.com', '$2y$10$AT6N7w39UPF3FyRoKA/9OORB84eJA73ZLf6xMG215aOoT7j4AbPEe', 1, 'usuario'),
(7, 'Daniel Acero', 'dani.daniel8912@gmail.com', '$2y$10$T/Mi408ChulZvMowQshKweAJKVg8DYECYFwZcmEPTHbFsGRXwGP62', 1, 'usuario'),
(9, 'Juan_srr', 'jo8555725@gmail.com', '$2y$10$HacUPbSIp2zXlas8Ksn.e.DExO3k1ehzlaGqgJNbmrEkJ7X1WS1wS', 1, 'admin'),
(10, 'andruf', 'andrufernandez0815@gmail.com', '$2y$10$BJKUUejiSwJX/ihmSs69Uevub8iEDG.lQxp5HUayjB3U1/FGkOw.S', 1, 'usuario'),
(12, '123', 'jhonjcacaisc@juandelcorral.edu.co', '$2y$10$WTLHeBP/KWprRWoqZs4bA.f1HeK3KGfU7MBlwEPK5GaagFgEAhHbG', 1, 'usuario'),
(14, '1', '1235@gmail.com', '$2y$10$dG9X0dejbCjnCr7s4wKM/eTX3sCuJrWb46RINxY0oy2SChXiy1Rc6', 1, 'admin'),
(22, 'jj', 'jhonj117c@gmail.com', '$2y$10$XN9m0VCC3XJnDNrXEjchSu/s2Kcv6.lEzK673HwC/HtAKU5gVv11W', 1, 'admin'),
(23, 'Maikol', 'cmaikolyezid@gmail.com', '$2y$10$ih7s2PKKr6w.xWD.LZkJ6uXQduuCOWrZI1alzS1aElLtVyFExRpt2', 1, 'empleado');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Restricciones para tablas volcadas
--

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
