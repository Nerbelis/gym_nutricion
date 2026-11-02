-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3308
-- Tiempo de generación: 02-11-2026 a las 23:13:12
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
-- Base de datos: `gym_nutricion_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes_evaluacion`
--

CREATE TABLE `clientes_evaluacion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `edad` int(11) NOT NULL,
  `genero` enum('masculino','femenino') NOT NULL,
  `peso` decimal(5,2) NOT NULL,
  `altura` int(11) NOT NULL,
  `imc` decimal(4,2) NOT NULL,
  `estado_nutricional` varchar(100) NOT NULL,
  `porcentaje_grasa` decimal(4,2) NOT NULL,
  `tmb` int(11) NOT NULL,
  `tdee` int(11) NOT NULL,
  `notas_entrenador` text DEFAULT NULL,
  `fecha_evaluacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_suscripcion`
--

CREATE TABLE `pagos_suscripcion` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `metodo_pago` varchar(50) NOT NULL,
  `referencia_transaccion` varchar(150) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `moneda` varchar(10) NOT NULL,
  `estado_pago` varchar(20) DEFAULT 'aprobado',
  `fecha_pago` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rutinas_fuerza`
--

CREATE TABLE `rutinas_fuerza` (
  `id` int(11) NOT NULL,
  `tipo_perfil` varchar(100) NOT NULL,
  `nombre_rutina` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `frecuencia_semanal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes_evaluacion`
--
ALTER TABLE `clientes_evaluacion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pagos_suscripcion`
--
ALTER TABLE `pagos_suscripcion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Indices de la tabla `rutinas_fuerza`
--
ALTER TABLE `rutinas_fuerza`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes_evaluacion`
--
ALTER TABLE `clientes_evaluacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagos_suscripcion`
--
ALTER TABLE `pagos_suscripcion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rutinas_fuerza`
--
ALTER TABLE `rutinas_fuerza`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pagos_suscripcion`
--
ALTER TABLE `pagos_suscripcion`
  ADD CONSTRAINT `pagos_suscripcion_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes_evaluacion` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
