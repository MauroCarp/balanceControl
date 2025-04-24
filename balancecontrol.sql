-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 24-04-2025 a las 19:52:40
-- Versión del servidor: 8.3.0
-- Versión de PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `balancecontrol`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `barlovento_cereales`
--

DROP TABLE IF EXISTS `barlovento_cereales`;
CREATE TABLE IF NOT EXISTS `barlovento_cereales` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `establecimiento` date NOT NULL,
  `cereal` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cartaPorte` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendedor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pesoBruto` double(8,2) NOT NULL,
  `tara` double(8,2) NOT NULL,
  `humedad` int NOT NULL,
  `mermaHumedad` int NOT NULL,
  `calidad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `materiasExtraneas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tierra` tinyint(1) NOT NULL,
  `destino` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observaciones` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `barlovento_egresos`
--

DROP TABLE IF EXISTS `barlovento_egresos`;
CREATE TABLE IF NOT EXISTS `barlovento_egresos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `dte` int NOT NULL,
  `tipoDestino` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lugarDestino` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `frigorificoDestino` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tara` double(8,2) NOT NULL,
  `pesoBruto` double(8,2) NOT NULL,
  `categoria` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `barlovento_ingresos`
--

DROP TABLE IF EXISTS `barlovento_ingresos`;
CREATE TABLE IF NOT EXISTS `barlovento_ingresos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `consignatario` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comisionista` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dte` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `origen_cantidad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `origen_categoria` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `origen_distancia` int NOT NULL,
  `origen_pesoBruto` double(8,2) NOT NULL,
  `origen_pesoNeto` double(8,2) NOT NULL,
  `origen_desbaste` int NOT NULL,
  `destino_cantidad` int NOT NULL,
  `destino_categoria` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destino_pesoBruto` double(8,2) NOT NULL,
  `destino_tara` double(8,2) NOT NULL,
  `precioKg` double(8,2) NOT NULL,
  `precioFlete` double(8,2) NOT NULL,
  `precioOtrosGastos` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cereales`
--

DROP TABLE IF EXISTS `cereales`;
CREATE TABLE IF NOT EXISTS `cereales` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `establecimiento` date NOT NULL,
  `cereal` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cartaPorte` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendedor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pesoBruto` double(8,2) NOT NULL,
  `tara` double(8,2) NOT NULL,
  `humedad` int NOT NULL,
  `mermaHumedad` int NOT NULL,
  `calidad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `materiasExtraneas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tierra` tinyint(1) NOT NULL,
  `destino` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observaciones` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comisionistas`
--

DROP TABLE IF EXISTS `comisionistas`;
CREATE TABLE IF NOT EXISTS `comisionistas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `porcentajeComision` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `comisionistas`
--

INSERT INTO `comisionistas` (`id`, `nombre`, `porcentajeComision`, `created_at`, `updated_at`) VALUES
(1, 'Vagdaugna', 0, '2025-04-24 14:57:23', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consignatarios`
--

DROP TABLE IF EXISTS `consignatarios`;
CREATE TABLE IF NOT EXISTS `consignatarios` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `porcentajeComision` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `consignatarios`
--

INSERT INTO `consignatarios` (`id`, `nombre`, `porcentajeComision`, `created_at`, `updated_at`) VALUES
(1, 'Galarraga', 0, NULL, NULL),
(2, 'Trade food', 0, NULL, NULL),
(3, 'Colombo y colombo', 0, NULL, NULL),
(4, 'Pedro noel irey', 0, NULL, NULL),
(5, 'AFA', 0, NULL, NULL),
(6, 'Pizzichini mauricio', 0, NULL, NULL),
(7, 'REGGI & CIA', 0, NULL, NULL),
(8, 'Suc. De Porporato', 0, NULL, NULL),
(9, 'Chaina Eduardo', 0, NULL, NULL),
(10, 'Madelan', 0, NULL, NULL),
(11, 'Duarte & CIA', 0, NULL, NULL),
(12, 'Petinari', 0, NULL, NULL),
(13, 'Novarese Onelio Livio', 0, NULL, NULL),
(14, 'Suc. de Carlos M. Noetinger', 0, NULL, NULL),
(15, 'Soluciones agrop Pampa S.A.', 0, NULL, NULL),
(16, 'Wallace Hnos S.A.', 0, NULL, NULL),
(17, 'Est. Agropecuario Don Raul', 0, NULL, NULL),
(18, 'Bessone Eduardo', 0, NULL, NULL),
(19, 'Justo Peralta', 0, NULL, NULL),
(20, 'Ruben Leroux', 0, NULL, NULL),
(21, 'La Celina', 0, NULL, NULL),
(22, 'Rauch', 0, NULL, NULL),
(23, 'Charles y CIA', 0, NULL, NULL),
(24, 'FFI SRL', 0, NULL, NULL),
(25, 'Atreuco', 0, NULL, NULL),
(26, 'Hourcade Albelo y CIA S.A.', 0, NULL, NULL),
(27, 'Avant Pres', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb3_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_04_07_233219_create_barlovento_ingresos_table', 1),
(6, '2025_04_08_015001_create_consignatarios_table', 1),
(7, '2025_04_08_015253_create_barlovento_egresos_table', 1),
(8, '2025_04_08_020857_create_cereales_table', 1),
(9, '2025_04_23_124107_create_comisionistas_table', 2),
(10, '2025_04_23_224616_create_barlovento_cereales_table', 3),
(11, '2025_04_23_224616_create_paihuen_cereales_table', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paihuen_cereales`
--

DROP TABLE IF EXISTS `paihuen_cereales`;
CREATE TABLE IF NOT EXISTS `paihuen_cereales` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `establecimiento` date NOT NULL,
  `cereal` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cartaPorte` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vendedor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pesoBruto` double(8,2) NOT NULL,
  `tara` double(8,2) NOT NULL,
  `humedad` int NOT NULL,
  `mermaHumedad` int NOT NULL,
  `calidad` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `materiasExtraneas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tierra` tinyint(1) NOT NULL,
  `destino` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observaciones` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb3_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb3_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Mauro', 'mauro@mauro.com', NULL, '$2y$10$x82RIEYORfQma2IGm/R5KOagYM4XboynblIQSPD.TvsZzaIvJ20Ne', 'ig5KIeeaUZwU31jfmLrkRykmc7j0gOzqvomaVum36fVKjyxMJ9ZAeOHiZFOs', '2025-04-22 19:39:46', '2025-04-22 19:39:46');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
