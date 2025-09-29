-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.32-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para inventory_mvc
CREATE DATABASE IF NOT EXISTS `inventory_mvc` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `inventory_mvc`;

-- Volcando estructura para tabla inventory_mvc.inventario
CREATE TABLE IF NOT EXISTS `inventario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `serie` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `oficina_id` int(11) DEFAULT NULL,
  `estado` enum('DISPONIBLE','AGOTADO') NOT NULL DEFAULT 'DISPONIBLE',
  `estado_2` enum('BUENO','MALO','REGULAR','BAJA','NUEVO') NOT NULL DEFAULT 'BUENO',
  `estante` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `fk_inv_oficina` (`oficina_id`),
  CONSTRAINT `fk_inv_oficina` FOREIGN KEY (`oficina_id`) REFERENCES `oficinas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla inventory_mvc.inventario: ~32 rows (aproximadamente)
INSERT INTO `inventario` (`id`, `codigo`, `nombre`, `serie`, `descripcion`, `cantidad`, `oficina_id`, `estado`, `estado_2`, `estante`, `created_at`, `updated_at`) VALUES
	(8, 'SOP-MON-0001', 'MONITOR LG 15.5"', 'MODIFICAR', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:11:01', '2025-09-29 13:11:01'),
	(9, 'SOP-MON-0002', 'MONITOR LG 15.5"', 'MODIFICAR', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:11:34', '2025-09-29 13:11:34'),
	(10, 'SOP-MON-0003', 'MONITOR LG 15.5"', 'MODIFICAR', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:11:54', '2025-09-29 13:11:54'),
	(11, 'SOP-MON-0004', 'MONITOR LG 15.5"', 'MODIFICAR', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:12:08', '2025-09-29 13:12:08'),
	(12, 'SOP-MON-0005', 'MONITOR LG 15.5"', 'MODIFICAR', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:12:22', '2025-09-29 13:12:22'),
	(13, 'SOP-MON-0006', 'MONITOR LG 15.5"', 'MODIFICAR', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:12:39', '2025-09-29 13:12:39'),
	(14, 'SOP-MON-0007', 'MONITOR SAMSUNG 15.5"', 'MODIFICAR', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:13:26', '2025-09-29 13:13:26'),
	(15, 'SOP-MON-0008', 'MONITOR SAMSUNG 15.5"', 'MODIFICAR', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:13:46', '2025-09-29 13:13:46'),
	(16, 'SOP-MON-0009', 'MONITOR SAMSUNG 15.5"', 'MODIFICAR', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:14:03', '2025-09-29 13:14:03'),
	(17, 'SOP-MON-0010', 'MONITOR SAMSUNG 15.5"', 'MODIFICAR', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:14:26', '2025-09-29 13:14:26'),
	(18, 'SOP-MON-0011', 'MONITOR LG 15.5"', 'MODIFICAR', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:14:43', '2025-09-29 13:14:43'),
	(19, 'SOP-MON-0012', 'MONITOR SAMSUNG 15.5"', 'MODIFICAR', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:14:59', '2025-09-29 13:14:59'),
	(20, 'SOP-MON-0013', 'MONITOR SAMSUNG 15.5"', 'MODIFICAR', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:15:24', '2025-09-29 13:15:24'),
	(21, 'SOP-MON-0014', 'MONITOR LG 15.5"', 'MODIFICAR', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:15:37', '2025-09-29 13:15:37'),
	(22, 'SOP-MON-0015', 'MONITOR LG 15.5"', '204RKWZ06705', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:16:48', '2025-09-29 13:16:48'),
	(23, 'SOP-MON-0016', 'MONITOR LG 15.5"', '205RKZH0L541', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:17:16', '2025-09-29 13:17:16'),
	(24, 'SOP-MON-0017', 'MONITOR SAMSUNG 15.5"', 'V8BBH9ZA01773J', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:17:48', '2025-09-29 13:17:48'),
	(25, 'SOP-MON-0018', 'MONITOR VIEWSONIC 14"', 'Q85063524204', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:18:21', '2025-09-29 13:18:21'),
	(26, 'SOP-AWU-0001', 'ADAPTADOR WIFI USB', '22190X5006708', '', 1, 4, 'DISPONIBLE', 'NUEVO', 'ESTANTE 2', '2025-09-29 13:18:47', '2025-09-29 13:18:47'),
	(27, 'SOP-ADA-0001', 'ADAPTADOR USB', '221C633009070', '', 1, 4, 'DISPONIBLE', '', 'ESTANTE 2', '2025-09-29 13:19:57', '2025-09-29 13:19:57'),
	(28, 'SOP-AWU-0002', 'ADAPTADOR WIFI USB', '221C633002172', '', 1, 4, 'DISPONIBLE', 'NUEVO', 'ESTANTE 2', '2025-09-29 13:20:35', '2025-09-29 13:20:35'),
	(29, 'SOP-AWU-0003', 'ADAPTADOR WIFI USB', '22130Q7001355', '', 1, 4, 'DISPONIBLE', 'NUEVO', 'ESTANTE 2', '2025-09-29 13:21:01', '2025-09-29 13:21:01'),
	(30, 'SOP-AWU-0004', 'ADAPTADOR WIFI USB', '221C1S9001651', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:21:31', '2025-09-29 13:21:31'),
	(31, 'SOP-AWU-0005', 'ADAPTADOR WIFI USB', '2219324002684', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:22:04', '2025-09-29 13:22:04'),
	(32, 'SOP-AWU-0006', 'ADAPTADOR WIFI USB', '221C1S9001663', '', 1, 4, 'DISPONIBLE', 'NUEVO', 'ESTANTE 2', '2025-09-29 13:22:29', '2025-09-29 13:22:29'),
	(33, 'SOP-AWU-0007', 'ADAPTADOR WIFI USB', '221C633009047', '', 1, 4, 'DISPONIBLE', 'NUEVO', 'ESTANTE 2', '2025-09-29 13:23:03', '2025-09-29 13:23:03'),
	(34, 'SOP-APA-0001', 'ACCESS POINT AX1800', '22273N5000934', '', 1, 4, 'DISPONIBLE', 'NUEVO', 'ESTANTE 2', '2025-09-29 13:23:40', '2025-09-29 13:23:40'),
	(35, 'SOP-AWU-0008', 'ADAPTADOR WIFI USB', '22134W2009191', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:24:10', '2025-09-29 13:24:10'),
	(36, 'SOP-AWU-0009', 'ADAPTADOR WIFI USB', '2219494000624', '', 1, 4, 'DISPONIBLE', 'BUENO', 'ESTANTE 2', '2025-09-29 13:24:39', '2025-09-29 13:24:39'),
	(37, 'SOP-APA-0002', 'ACCESS POINT AX1800', '22272R0000987', '', 1, 4, 'DISPONIBLE', 'REGULAR', 'ESTANTE 2', '2025-09-29 13:25:06', '2025-09-29 13:25:06'),
	(38, 'SOP-CON-0001', 'CONTROL DE PROYECTOR', '217358900', '', 1, 4, 'DISPONIBLE', 'REGULAR', 'ESTANTE 2', '2025-09-29 13:25:47', '2025-09-29 13:25:47'),
	(39, 'SOP-CON-0002', 'CONTROL DE PROYECTOR', '159917600', '', 1, 4, 'DISPONIBLE', 'REGULAR', 'ESTANTE 2', '2025-09-29 13:26:17', '2025-09-29 13:26:17');

-- Volcando estructura para tabla inventory_mvc.movimientos
CREATE TABLE IF NOT EXISTS `movimientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventario_id` int(11) NOT NULL,
  `tipo` enum('ENTRADA','SALIDA') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `motivo` text DEFAULT NULL,
  `oficina_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_mov_inv` (`inventario_id`),
  KEY `fk_mov_ofi` (`oficina_id`),
  KEY `fk_mov_user` (`user_id`),
  CONSTRAINT `fk_mov_inv` FOREIGN KEY (`inventario_id`) REFERENCES `inventario` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mov_ofi` FOREIGN KEY (`oficina_id`) REFERENCES `oficinas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mov_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla inventory_mvc.movimientos: ~32 rows (aproximadamente)
INSERT INTO `movimientos` (`id`, `inventario_id`, `tipo`, `cantidad`, `motivo`, `oficina_id`, `user_id`, `created_at`) VALUES
	(9, 8, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:11:01'),
	(10, 9, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:11:34'),
	(11, 10, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:11:54'),
	(12, 11, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:12:08'),
	(13, 12, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:12:22'),
	(14, 13, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:12:39'),
	(15, 14, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:13:26'),
	(16, 15, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:13:46'),
	(17, 16, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:14:03'),
	(18, 17, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:14:26'),
	(19, 18, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:14:43'),
	(20, 19, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:14:59'),
	(21, 20, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:15:24'),
	(22, 21, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:15:37'),
	(23, 22, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:16:48'),
	(24, 23, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:17:16'),
	(25, 24, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:17:48'),
	(26, 25, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:18:21'),
	(27, 26, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:18:47'),
	(28, 27, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:19:57'),
	(29, 28, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:20:35'),
	(30, 29, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:21:01'),
	(31, 30, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:21:31'),
	(32, 31, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:22:04'),
	(33, 32, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:22:29'),
	(34, 33, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:23:03'),
	(35, 34, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:23:40'),
	(36, 35, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:24:10'),
	(37, 36, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:24:39'),
	(38, 37, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:25:06'),
	(39, 38, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:25:47'),
	(40, 39, 'ENTRADA', 1, 'Registro inicial', 4, 3, '2025-09-29 13:26:17');

-- Volcando estructura para tabla inventory_mvc.oficinas
CREATE TABLE IF NOT EXISTS `oficinas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla inventory_mvc.oficinas: ~5 rows (aproximadamente)
INSERT INTO `oficinas` (`id`, `nombre`, `descripcion`, `estado`, `created_at`) VALUES
	(4, 'SOPORTE INFORMÁTICO', '', 1, '2025-09-29 13:05:43'),
	(5, 'LABORATORIO I', '', 1, '2025-09-29 13:05:53'),
	(6, 'LABORATORIO II', '', 1, '2025-09-29 13:05:58'),
	(7, 'CENTRAL DE DATOS', '', 1, '2025-09-29 13:06:07'),
	(8, 'SALA DE VIDEOCONFERENCIA', '', 1, '2025-09-29 13:06:17');

-- Volcando estructura para tabla inventory_mvc.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla inventory_mvc.users: ~2 rows (aproximadamente)
INSERT INTO `users` (`id`, `username`, `password_hash`, `nombre`, `estado`, `created_at`) VALUES
	(3, 'admin', '$2y$10$cSt/Z1VHfKeMtFmbO9lq7Oo.AANCe6FwKnC7T3afn1Y4h.nKtmli.', 'Administrador', 1, '2025-09-05 14:56:01'),
	(4, 'jefe', '$2y$10$Wq6b1v3kQeT0aVYJ4v5xseKgh3GQ5rj8k4QvH1uBWk9bH3l8M3n1K', 'Jefe de Inventario', 1, '2025-09-05 14:59:49');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
