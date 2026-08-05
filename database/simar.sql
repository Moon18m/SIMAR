-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-07-2026 a las 01:39:17
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `simar`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alertas`
--

CREATE TABLE `alertas` (
  `id_alerta` int(11) NOT NULL,
  `tipo` enum('Temperatura','Humedad','Puerta','Corriente','Inventario','Sistema') NOT NULL,
  `nivel` enum('Información','Advertencia','Crítica') NOT NULL,
  `mensaje` varchar(255) NOT NULL,
  `estado` enum('Activa','Resuelta') NOT NULL DEFAULT 'Activa',
  `fecha_hora` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alertas`
--

INSERT INTO `alertas` (`id_alerta`, `tipo`, `nivel`, `mensaje`, `estado`, `fecha_hora`) VALUES
(1, 'Temperatura', 'Advertencia', 'La temperatura del refrigerador superó el límite permitido.', 'Activa', '2026-07-14 11:02:06'),
(4, 'Temperatura', 'Información', '67', 'Activa', '2026-07-18 18:47:44'),
(5, 'Humedad', 'Advertencia', 'Alerta de prueba generada desde SIMAR.', 'Resuelta', '2026-07-24 17:49:13'),
(6, 'Humedad', 'Advertencia', 'Alerta de prueba generada desde SIMAR.', 'Activa', '2026-07-24 17:52:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dispositivos`
--

CREATE TABLE `dispositivos` (
  `id_dispositivo` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `puerto` int(11) NOT NULL,
  `estado` enum('Activo','Inactivo','Error') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_inventario`
--

CREATE TABLE `historial_inventario` (
  `id_historial` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad_anterior` int(11) NOT NULL,
  `cantidad_nueva` int(11) NOT NULL,
  `tipo` enum('Ingreso','Salida','Sin cambio') NOT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ia_ejecuciones`
--

CREATE TABLE `ia_ejecuciones` (
  `id_ejecucion` int(11) NOT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` enum('Exitoso','Error') NOT NULL,
  `detalle` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ia_ejecuciones`
--

INSERT INTO `ia_ejecuciones` (`id_ejecucion`, `fecha_hora`, `estado`, `detalle`) VALUES
(2, '2026-07-18 18:51:06', 'Exitoso', NULL),
(3, '2026-07-18 18:51:56', 'Exitoso', NULL),
(4, '2026-07-18 18:52:16', 'Error', NULL),
(5, '2026-07-18 18:52:29', 'Exitoso', NULL),
(6, '2026-07-18 18:53:59', 'Exitoso', NULL),
(7, '2026-07-18 18:54:08', 'Exitoso', NULL),
(8, '2026-07-18 18:54:52', 'Exitoso', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id_inventario` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `fecha_de_actulizacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_ingreso` datetime NOT NULL,
  `vida_util_calculada` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id_inventario`, `id_producto`, `cantidad`, `fecha_de_actulizacion`, `fecha_ingreso`, `vida_util_calculada`) VALUES
(1, 1, 12, '2026-07-14 11:05:16', '2026-07-06 11:05:16', 10),
(2, 2, 20, '2026-07-14 11:05:16', '2026-07-09 11:05:00', 20),
(3, 3, 8, '2026-07-14 11:05:16', '2026-06-19 11:05:16', 30);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lecturas_ambientales`
--

CREATE TABLE `lecturas_ambientales` (
  `id_lectura` int(11) NOT NULL,
  `id_sensor` int(11) NOT NULL,
  `valor` decimal(6,2) NOT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lecturas_ambientales`
--

INSERT INTO `lecturas_ambientales` (`id_lectura`, `id_sensor`, `valor`, `fecha_hora`) VALUES
(21, 1, 3.80, '2026-07-14 10:27:43'),
(22, 1, 4.00, '2026-07-14 10:28:43'),
(23, 1, 4.10, '2026-07-14 10:29:43'),
(24, 1, 3.90, '2026-07-14 10:30:43'),
(25, 1, 4.20, '2026-07-14 10:31:43'),
(26, 1, 4.40, '2026-07-14 10:32:43'),
(27, 1, 4.30, '2026-07-14 10:33:43'),
(28, 1, 4.50, '2026-07-14 10:34:43'),
(29, 1, 4.20, '2026-07-14 10:35:43'),
(30, 1, 4.00, '2026-07-14 10:36:43'),
(31, 1, 3.80, '2026-07-14 10:37:43'),
(32, 1, 3.90, '2026-07-14 10:38:43'),
(33, 1, 4.10, '2026-07-14 10:39:43'),
(34, 1, 4.00, '2026-07-14 10:40:43'),
(35, 1, 4.20, '2026-07-14 10:41:43'),
(36, 1, 4.30, '2026-07-14 10:42:43'),
(37, 1, 4.40, '2026-07-14 10:43:43'),
(38, 1, 4.20, '2026-07-14 10:44:43'),
(39, 1, 4.10, '2026-07-14 10:45:43'),
(40, 1, 4.00, '2026-07-14 10:46:43'),
(41, 2, 82.00, '2026-07-14 10:27:53'),
(42, 2, 81.00, '2026-07-14 10:28:53'),
(43, 2, 83.00, '2026-07-14 10:29:53'),
(44, 2, 84.00, '2026-07-14 10:30:53'),
(45, 2, 82.00, '2026-07-14 10:31:53'),
(46, 2, 81.00, '2026-07-14 10:32:53'),
(47, 2, 80.00, '2026-07-14 10:33:53'),
(48, 2, 82.00, '2026-07-14 10:34:53'),
(49, 2, 83.00, '2026-07-14 10:35:53'),
(50, 2, 84.00, '2026-07-14 10:36:53'),
(51, 2, 83.00, '2026-07-14 10:37:53'),
(52, 2, 82.00, '2026-07-14 10:38:53'),
(53, 2, 81.00, '2026-07-14 10:39:53'),
(54, 2, 82.00, '2026-07-14 10:40:53'),
(55, 2, 83.00, '2026-07-14 10:41:53'),
(56, 2, 84.00, '2026-07-14 10:42:53'),
(57, 2, 85.00, '2026-07-14 10:43:53'),
(58, 2, 84.00, '2026-07-14 10:44:53'),
(59, 2, 83.00, '2026-07-14 10:45:53'),
(60, 2, 82.00, '2026-07-14 10:46:53'),
(61, 1, 2.00, '2026-07-18 18:46:32'),
(62, 2, 70.00, '2026-07-18 19:39:01'),
(63, 2, 70.00, '2026-07-18 19:49:16'),
(64, 1, 4.00, '2026-07-19 12:49:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `vida_util_dias` int(11) NOT NULL,
  `temperatura_min` decimal(4,2) NOT NULL,
  `temperatura_max` decimal(4,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre`, `vida_util_dias`, `temperatura_min`, `temperatura_max`) VALUES
(1, 'Leche', 10, 2.00, 5.00),
(2, 'Yogur', 20, 2.00, 6.00),
(3, 'Queso', 30, 2.00, 8.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sensores`
--

CREATE TABLE `sensores` (
  `id_sensor` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `tipo` enum('Temperatura','Humedad','Magnético','Corriente') NOT NULL,
  `estado` enum('Activo','Inactivo','Error') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sensores`
--

INSERT INTO `sensores` (`id_sensor`, `nombre`, `tipo`, `estado`) VALUES
(1, 'DHT22 Temperatura', 'Temperatura', 'Activo'),
(2, 'DHT22 Humedad', 'Humedad', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('Administrador','Operador') NOT NULL DEFAULT 'Operador',
  `activo` tinyint(1) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `correo`, `password_hash`, `rol`, `activo`, `fecha_creacion`, `ultimo_acceso`) VALUES
(1, 'Samuel Monsalve Lopez', 'smonsalvelopez3@gmail.com', '$2y$10$Xgo1yn0SNieIs08V1uKFEeMhbxlmGuZpjRcMnGkbZXb/btkC.xdF6', 'Administrador', 1, '2026-07-11 13:49:55', '2026-07-24 18:08:30'),
(2, 'Samuel Monsalve Lopez', 'moncho.secun2102@gmail.com', '$2y$10$fZSkTuy.2G2scUPdNpbzn.I8VSaBWJn.ueqjfV/sdQmmCzw.YdqXy', 'Operador', 1, '2026-07-11 15:03:54', '2026-07-24 18:09:03'),
(4, 'Juan Esteban Gutierrrez', 'benjizit01129@gmail.com', '$2y$10$82YythlfFVngBGrdvO6mw.JjlS0kazbOJ2XQaedSqrosKO2Lrz/8W', 'Operador', 1, '2026-07-14 12:25:34', '2026-07-14 07:26:12'),
(5, '1234', 'hola@gmail.com', '$2y$10$xKSKsTpKtpXG/6HqK56cjOjfE/H.yymZHExFYrvT4TM2MetrWN7da', 'Operador', 1, '2026-07-17 23:21:36', '2026-07-17 18:21:56'),
(6, 'pepito', 'pepito@gmail.com', '$2y$10$9Cj/TQgEzzxtbl3qoDpgTOsOVFlP5NUAQcxjnDTNWwTO.sPq0MYtG', 'Operador', 1, '2026-07-18 23:44:33', '2026-07-18 18:45:11'),
(9, '', '', '$2y$10$XYl6FPrin4WTEUd7xxXY8u/as6wMpfAYi8ok99o9wodmNvQiIQUZ6', 'Operador', 1, '2026-07-19 01:33:15', NULL),
(11, 'hola23', '23@gmail.com', '$2y$10$gF1K7grKOBP6nCDw9AdacuiMU.0vR8c/Pl7ZwNoMgHRYwEExMr5JO', 'Operador', 1, '2026-07-19 01:33:58', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alertas`
--
ALTER TABLE `alertas`
  ADD PRIMARY KEY (`id_alerta`);

--
-- Indices de la tabla `dispositivos`
--
ALTER TABLE `dispositivos`
  ADD PRIMARY KEY (`id_dispositivo`);

--
-- Indices de la tabla `historial_inventario`
--
ALTER TABLE `historial_inventario`
  ADD PRIMARY KEY (`id_historial`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `ia_ejecuciones`
--
ALTER TABLE `ia_ejecuciones`
  ADD PRIMARY KEY (`id_ejecucion`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id_inventario`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `lecturas_ambientales`
--
ALTER TABLE `lecturas_ambientales`
  ADD PRIMARY KEY (`id_lectura`),
  ADD KEY `id_sensor` (`id_sensor`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `sensores`
--
ALTER TABLE `sensores`
  ADD PRIMARY KEY (`id_sensor`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`) USING BTREE;

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alertas`
--
ALTER TABLE `alertas`
  MODIFY `id_alerta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `historial_inventario`
--
ALTER TABLE `historial_inventario`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ia_ejecuciones`
--
ALTER TABLE `ia_ejecuciones`
  MODIFY `id_ejecucion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id_inventario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `lecturas_ambientales`
--
ALTER TABLE `lecturas_ambientales`
  MODIFY `id_lectura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sensores`
--
ALTER TABLE `sensores`
  MODIFY `id_sensor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `historial_inventario`
--
ALTER TABLE `historial_inventario`
  ADD CONSTRAINT `historial_inventario_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`);

--
-- Filtros para la tabla `lecturas_ambientales`
--
ALTER TABLE `lecturas_ambientales`
  ADD CONSTRAINT `lecturas_ambientales_ibfk_1` FOREIGN KEY (`id_sensor`) REFERENCES `sensores` (`id_sensor`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
