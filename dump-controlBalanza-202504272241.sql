-- MariaDB dump 10.19-11.2.3-MariaDB, for osx10.16 (x86_64)
--
-- Host: localhost    Database: controlBalanza
-- ------------------------------------------------------
-- Server version	11.2.3-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `barlovento_cereales`
--

DROP TABLE IF EXISTS `barlovento_cereales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `barlovento_cereales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `cereal` varchar(255) NOT NULL,
  `cartaPorte` varchar(255) NOT NULL,
  `vendedor` varchar(255) NOT NULL,
  `pesoBruto` int(11) NOT NULL,
  `pesoTara` int(11) NOT NULL,
  `humedad` int(11) NOT NULL,
  `mermaHumedad` int(11) NOT NULL,
  `calidad` varchar(255) NOT NULL,
  `materiasExtranas` varchar(255) NOT NULL,
  `tierra` tinyint(1) NOT NULL,
  `granosRotos` tinyint(1) NOT NULL,
  `granosQuebrados` tinyint(1) NOT NULL,
  `destino` varchar(255) NOT NULL,
  `observaciones` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `barlovento_cereales`
--

LOCK TABLES `barlovento_cereales` WRITE;
/*!40000 ALTER TABLE `barlovento_cereales` DISABLE KEYS */;
INSERT INTO `barlovento_cereales` VALUES
(1,'2025-04-26','Maiz','213123','Vendedor 1',120000,20000,16,0,'buena','0',0,0,0,'plantaSilo',NULL,'2025-04-26 16:05:23','2025-04-26 16:05:23');
/*!40000 ALTER TABLE `barlovento_cereales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `barlovento_egresos`
--

DROP TABLE IF EXISTS `barlovento_egresos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `barlovento_egresos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `dte` int(11) NOT NULL,
  `tipoDestino` varchar(255) NOT NULL,
  `lugarDestino` varchar(255) NOT NULL,
  `frigorificoDestino` varchar(255) NOT NULL,
  `tara` double(8,2) NOT NULL,
  `pesoBruto` double(8,2) NOT NULL,
  `categoria` varchar(255) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `barlovento_egresos`
--

LOCK TABLES `barlovento_egresos` WRITE;
/*!40000 ALTER TABLE `barlovento_egresos` DISABLE KEYS */;
/*!40000 ALTER TABLE `barlovento_egresos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `barlovento_ingresos`
--

DROP TABLE IF EXISTS `barlovento_ingresos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `barlovento_ingresos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `consignatario` varchar(255) NOT NULL,
  `comisionista` varchar(255) NOT NULL,
  `dte` varchar(255) NOT NULL,
  `origen_terneros` int(11) NOT NULL,
  `origen_terneras` int(11) NOT NULL,
  `origen_distancia` int(11) NOT NULL,
  `origen_pesoBruto` int(20) NOT NULL,
  `origen_pesoNeto` int(20) NOT NULL,
  `origen_desbaste` int(11) NOT NULL,
  `destino_terneros` int(11) NOT NULL,
  `destino_terneras` int(11) NOT NULL,
  `destino_pesoBruto` int(11) NOT NULL,
  `destino_tara` int(11) NOT NULL,
  `precioKg` double(8,2) DEFAULT NULL,
  `precioFlete` double(8,2) DEFAULT NULL,
  `precioOtrosGastos` double(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `barlovento_ingresos`
--

LOCK TABLES `barlovento_ingresos` WRITE;
/*!40000 ALTER TABLE `barlovento_ingresos` DISABLE KEYS */;
INSERT INTO `barlovento_ingresos` VALUES
(1,'2025-04-26','Galarraga','Vagdaugna','123123123',40,30,400,150000000,149990000,4,40,30,150000000,149900000,NULL,NULL,NULL,'2025-04-26 18:09:25','2025-04-26 18:09:25');
/*!40000 ALTER TABLE `barlovento_ingresos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cereales`
--

DROP TABLE IF EXISTS `cereales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cereales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `establecimiento` date NOT NULL,
  `cereal` varchar(255) NOT NULL,
  `cartaPorte` varchar(255) NOT NULL,
  `vendedor` varchar(255) NOT NULL,
  `pesoBruto` double(8,2) NOT NULL,
  `tara` double(8,2) NOT NULL,
  `humedad` int(11) NOT NULL,
  `mermaHumedad` int(11) NOT NULL,
  `calidad` varchar(255) NOT NULL,
  `materiasExtraneas` varchar(255) NOT NULL,
  `tierra` tinyint(1) NOT NULL,
  `destino` varchar(255) NOT NULL,
  `observaciones` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cereales`
--

LOCK TABLES `cereales` WRITE;
/*!40000 ALTER TABLE `cereales` DISABLE KEYS */;
/*!40000 ALTER TABLE `cereales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comisionistas`
--

DROP TABLE IF EXISTS `comisionistas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comisionistas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `porcentajeComision` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comisionistas`
--

LOCK TABLES `comisionistas` WRITE;
/*!40000 ALTER TABLE `comisionistas` DISABLE KEYS */;
INSERT INTO `comisionistas` VALUES
(1,'Vagdaugna',0,'2025-04-24 14:57:23',NULL);
/*!40000 ALTER TABLE `comisionistas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consignatarios`
--

DROP TABLE IF EXISTS `consignatarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consignatarios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `porcentajeComision` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consignatarios`
--

LOCK TABLES `consignatarios` WRITE;
/*!40000 ALTER TABLE `consignatarios` DISABLE KEYS */;
INSERT INTO `consignatarios` VALUES
(1,'Galarraga',0,NULL,NULL),
(2,'Trade food',0,NULL,NULL),
(3,'Colombo y colombo',0,NULL,NULL),
(4,'Pedro noel irey',0,NULL,NULL),
(5,'AFA',0,NULL,NULL),
(6,'Pizzichini mauricio',0,NULL,NULL),
(7,'REGGI & CIA',0,NULL,NULL),
(8,'Suc. De Porporato',0,NULL,NULL),
(9,'Chaina Eduardo',0,NULL,NULL),
(10,'Madelan',0,NULL,NULL),
(11,'Duarte & CIA',0,NULL,NULL),
(12,'Petinari',0,NULL,NULL),
(13,'Novarese Onelio Livio',0,NULL,NULL),
(14,'Suc. de Carlos M. Noetinger',0,NULL,NULL),
(15,'Soluciones agrop Pampa S.A.',0,NULL,NULL),
(16,'Wallace Hnos S.A.',0,NULL,NULL),
(17,'Est. Agropecuario Don Raul',0,NULL,NULL),
(18,'Bessone Eduardo',0,NULL,NULL),
(19,'Justo Peralta',0,NULL,NULL),
(20,'Ruben Leroux',0,NULL,NULL),
(21,'La Celina',0,NULL,NULL),
(22,'Rauch',0,NULL,NULL),
(23,'Charles y CIA',0,NULL,NULL),
(24,'FFI SRL',0,NULL,NULL),
(25,'Atreuco',0,NULL,NULL),
(26,'Hourcade Albelo y CIA S.A.',0,NULL,NULL),
(27,'Avant Pres',0,NULL,NULL);
/*!40000 ALTER TABLE `consignatarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `merma_humedad`
--

DROP TABLE IF EXISTS `merma_humedad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `merma_humedad` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cereal` varchar(20) DEFAULT NULL,
  `humedad` decimal(4,1) DEFAULT NULL,
  `merma` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=456 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `merma_humedad`
--

LOCK TABLES `merma_humedad` WRITE;
/*!40000 ALTER TABLE `merma_humedad` DISABLE KEYS */;
INSERT INTO `merma_humedad` VALUES
(1,'maiz',14.6,1.27),
(2,'maiz',14.7,1.39),
(3,'maiz',14.8,1.50),
(4,'maiz',14.9,1.62),
(5,'maiz',15.0,1.73),
(6,'maiz',15.1,1.85),
(7,'maiz',15.2,1.97),
(8,'maiz',15.3,2.08),
(9,'maiz',15.4,2.20),
(10,'maiz',15.5,2.31),
(11,'maiz',15.6,2.43),
(12,'maiz',15.7,2.54),
(13,'maiz',15.8,2.66),
(14,'maiz',15.9,2.77),
(15,'maiz',16.0,2.89),
(16,'maiz',16.1,3.01),
(17,'maiz',16.2,3.12),
(18,'maiz',16.3,3.24),
(19,'maiz',16.4,3.35),
(20,'maiz',16.5,3.47),
(21,'maiz',16.6,3.58),
(22,'maiz',16.7,3.70),
(23,'maiz',16.8,3.82),
(24,'maiz',16.9,3.93),
(25,'maiz',17.0,4.05),
(26,'maiz',17.1,4.16),
(27,'maiz',17.2,4.28),
(28,'maiz',17.3,4.39),
(29,'maiz',17.4,4.51),
(30,'maiz',17.5,4.62),
(31,'maiz',17.6,4.74),
(32,'maiz',17.7,4.86),
(33,'maiz',17.8,4.97),
(34,'maiz',17.9,5.09),
(35,'maiz',18.0,5.20),
(36,'maiz',18.1,5.32),
(37,'maiz',18.2,5.43),
(38,'maiz',18.3,5.55),
(39,'maiz',18.4,5.66),
(40,'maiz',18.5,5.78),
(41,'maiz',18.6,5.90),
(42,'maiz',18.7,6.01),
(43,'maiz',18.8,6.13),
(44,'maiz',18.9,6.24),
(45,'maiz',19.0,6.36),
(46,'maiz',19.1,6.47),
(47,'maiz',19.2,6.59),
(48,'maiz',19.3,6.71),
(49,'maiz',19.4,6.82),
(50,'maiz',19.5,6.94),
(51,'maiz',19.6,7.05),
(52,'maiz',19.7,7.17),
(53,'maiz',19.8,7.28),
(54,'maiz',19.9,7.40),
(55,'maiz',20.0,7.51),
(56,'maiz',20.1,7.63),
(57,'maiz',20.2,7.75),
(58,'maiz',20.3,7.86),
(59,'maiz',20.4,7.98),
(60,'maiz',20.5,8.09),
(61,'maiz',20.6,8.21),
(62,'maiz',20.7,8.32),
(63,'maiz',20.8,8.44),
(64,'maiz',20.9,8.55),
(65,'maiz',21.0,8.67),
(66,'maiz',21.1,8.79),
(67,'maiz',21.2,8.90),
(68,'maiz',21.3,9.02),
(69,'maiz',21.4,9.13),
(70,'maiz',21.5,9.25),
(71,'maiz',21.6,9.36),
(72,'maiz',21.7,9.48),
(73,'maiz',21.8,9.60),
(74,'maiz',21.9,9.71),
(75,'maiz',22.0,9.83),
(76,'maiz',22.1,9.94),
(77,'maiz',22.2,10.06),
(78,'maiz',22.3,10.17),
(79,'maiz',22.4,10.29),
(80,'maiz',22.5,10.40),
(81,'maiz',22.6,10.52),
(82,'maiz',22.7,10.64),
(83,'maiz',22.8,10.75),
(84,'maiz',22.9,10.87),
(85,'maiz',23.0,10.98),
(86,'maiz',23.1,11.10),
(87,'maiz',23.2,11.21),
(88,'maiz',23.3,11.33),
(89,'maiz',23.4,11.45),
(90,'maiz',23.5,11.56),
(91,'maiz',23.6,11.68),
(92,'maiz',23.7,11.79),
(93,'maiz',23.8,11.91),
(94,'maiz',23.9,12.02),
(95,'maiz',24.0,12.14),
(96,'maiz',24.1,12.25),
(97,'maiz',24.2,12.37),
(98,'maiz',24.3,12.49),
(99,'maiz',24.4,12.60),
(100,'maiz',24.5,12.72),
(101,'maiz',24.6,12.83),
(102,'maiz',24.7,12.95),
(103,'maiz',24.8,13.06),
(104,'maiz',24.9,13.18),
(105,'maiz',25.0,13.29),
(106,'maiz',14.6,1.27),
(107,'maiz',14.7,1.39),
(108,'maiz',14.8,1.50),
(109,'maiz',14.9,1.62),
(110,'maiz',15.0,1.73),
(111,'maiz',15.1,1.85),
(112,'maiz',15.2,1.97),
(113,'maiz',15.3,2.08),
(114,'maiz',15.4,2.20),
(115,'maiz',15.5,2.31),
(116,'maiz',15.6,2.43),
(117,'maiz',15.7,2.54),
(118,'maiz',15.8,2.66),
(119,'maiz',15.9,2.77),
(120,'maiz',16.0,2.89),
(121,'maiz',16.1,3.01),
(122,'maiz',16.2,3.12),
(123,'maiz',16.3,3.24),
(124,'maiz',16.4,3.35),
(125,'maiz',16.5,3.47),
(126,'maiz',16.6,3.58),
(127,'maiz',16.7,3.70),
(128,'maiz',16.8,3.82),
(129,'maiz',16.9,3.93),
(130,'maiz',17.0,4.05),
(131,'maiz',17.1,4.16),
(132,'maiz',17.2,4.28),
(133,'maiz',17.3,4.39),
(134,'maiz',17.4,4.51),
(135,'maiz',17.5,4.62),
(136,'maiz',17.6,4.74),
(137,'maiz',17.7,4.86),
(138,'maiz',17.8,4.97),
(139,'maiz',17.9,5.09),
(140,'maiz',18.0,5.20),
(141,'maiz',18.1,5.32),
(142,'maiz',18.2,5.43),
(143,'maiz',18.3,5.55),
(144,'maiz',18.4,5.66),
(145,'maiz',18.5,5.78),
(146,'maiz',18.6,5.90),
(147,'maiz',18.7,6.01),
(148,'maiz',18.8,6.13),
(149,'maiz',18.9,6.24),
(150,'maiz',19.0,6.36),
(151,'maiz',19.1,6.47),
(152,'maiz',19.2,6.59),
(153,'maiz',19.3,6.71),
(154,'maiz',19.4,6.82),
(155,'maiz',19.5,6.94),
(156,'maiz',19.6,7.05),
(157,'maiz',19.7,7.17),
(158,'maiz',19.8,7.28),
(159,'maiz',19.9,7.40),
(160,'maiz',20.0,7.51),
(161,'maiz',20.1,7.63),
(162,'maiz',20.2,7.75),
(163,'maiz',20.3,7.86),
(164,'maiz',20.4,7.98),
(165,'maiz',20.5,8.09),
(166,'maiz',20.6,8.21),
(167,'maiz',20.7,8.32),
(168,'maiz',20.8,8.44),
(169,'maiz',20.9,8.55),
(170,'maiz',21.0,8.67),
(171,'maiz',21.1,8.79),
(172,'maiz',21.2,8.90),
(173,'maiz',21.3,9.02),
(174,'maiz',21.4,9.13),
(175,'maiz',21.5,9.25),
(176,'maiz',21.6,9.36),
(177,'maiz',21.7,9.48),
(178,'maiz',21.8,9.60),
(179,'maiz',21.9,9.71),
(180,'maiz',22.0,9.83),
(181,'maiz',22.1,9.94),
(182,'maiz',22.2,10.06),
(183,'maiz',22.3,10.17),
(184,'maiz',22.4,10.29),
(185,'maiz',22.5,10.40),
(186,'maiz',22.6,10.52),
(187,'maiz',22.7,10.64),
(188,'maiz',22.8,10.75),
(189,'maiz',22.9,10.87),
(190,'maiz',23.0,10.98),
(191,'maiz',23.1,11.10),
(192,'maiz',23.2,11.21),
(193,'maiz',23.3,11.33),
(194,'maiz',23.4,11.45),
(195,'maiz',23.5,11.56),
(196,'maiz',23.6,11.68),
(197,'maiz',23.7,11.79),
(198,'maiz',23.8,11.91),
(199,'maiz',23.9,12.02),
(200,'maiz',24.0,12.14),
(201,'maiz',24.1,12.25),
(202,'maiz',24.2,12.37),
(203,'maiz',24.3,12.49),
(204,'maiz',24.4,12.60),
(205,'maiz',24.5,12.72),
(206,'maiz',24.6,12.83),
(207,'maiz',24.7,12.95),
(208,'maiz',24.8,13.06),
(209,'maiz',24.9,13.18),
(210,'maiz',25.0,13.29),
(211,'Soja',13.6,0.69),
(212,'Soja',13.7,0.80),
(213,'Soja',13.8,0.92),
(214,'Soja',13.9,1.03),
(215,'Soja',14.0,1.15),
(216,'Soja',14.1,1.26),
(217,'Soja',14.2,1.38),
(218,'Soja',14.3,1.49),
(219,'Soja',14.4,1.61),
(220,'Soja',14.5,1.72),
(221,'Soja',14.6,1.84),
(222,'Soja',14.7,1.95),
(223,'Soja',14.8,2.07),
(224,'Soja',14.9,2.18),
(225,'Soja',15.0,2.30),
(226,'Soja',15.1,2.41),
(227,'Soja',15.2,2.53),
(228,'Soja',15.3,2.64),
(229,'Soja',15.4,2.76),
(230,'Soja',15.5,2.87),
(231,'Soja',15.6,2.99),
(232,'Soja',15.7,3.10),
(233,'Soja',15.8,3.22),
(234,'Soja',15.9,3.33),
(235,'Soja',16.0,3.45),
(236,'Soja',16.1,3.56),
(237,'Soja',16.2,3.68),
(238,'Soja',16.3,3.79),
(239,'Soja',16.4,3.91),
(240,'Soja',16.5,4.02),
(241,'Soja',16.6,4.14),
(242,'Soja',16.7,4.25),
(243,'Soja',16.8,4.37),
(244,'Soja',16.9,4.48),
(245,'Soja',17.0,4.60),
(246,'Soja',17.1,4.71),
(247,'Soja',17.2,4.83),
(248,'Soja',17.3,4.94),
(249,'Soja',17.4,5.06),
(250,'Soja',17.5,5.17),
(251,'Soja',17.6,5.29),
(252,'Soja',17.7,5.40),
(253,'Soja',17.8,5.52),
(254,'Soja',17.9,5.63),
(255,'Soja',18.0,5.75),
(256,'Soja',18.1,5.86),
(257,'Soja',18.2,5.98),
(258,'Soja',18.3,6.09),
(259,'Soja',18.4,6.21),
(260,'Soja',18.5,6.32),
(261,'Soja',18.6,6.44),
(262,'Soja',18.7,6.55),
(263,'Soja',18.8,6.67),
(264,'Soja',18.9,6.78),
(265,'Soja',19.0,6.90),
(266,'Soja',19.1,7.01),
(267,'Soja',19.2,7.13),
(268,'Soja',19.3,7.24),
(269,'Soja',19.4,7.36),
(270,'Soja',19.5,7.47),
(271,'Soja',19.6,7.59),
(272,'Soja',19.7,7.70),
(273,'Soja',19.8,7.82),
(274,'Soja',19.9,7.93),
(275,'Soja',20.0,8.05),
(276,'Soja',20.1,8.16),
(277,'Soja',20.2,8.28),
(278,'Soja',20.3,8.39),
(279,'Soja',20.4,8.51),
(280,'Soja',20.5,8.62),
(281,'Soja',20.6,8.74),
(282,'Soja',20.7,8.85),
(283,'Soja',20.8,8.97),
(284,'Soja',20.9,9.08),
(285,'Soja',21.0,9.20),
(286,'Soja',21.1,9.31),
(287,'Soja',21.2,9.43),
(288,'Soja',21.3,9.54),
(289,'Soja',21.4,9.66),
(290,'Soja',21.5,9.77),
(291,'Soja',21.6,9.89),
(292,'Soja',21.7,10.00),
(293,'Soja',21.8,10.12),
(294,'Soja',21.9,10.23),
(295,'Soja',22.0,10.35),
(296,'Soja',22.1,10.46),
(297,'Soja',22.2,10.58),
(298,'Soja',22.3,10.69),
(299,'Soja',22.4,10.81),
(300,'Soja',22.5,10.92),
(301,'Soja',22.6,11.04),
(302,'Soja',22.7,11.15),
(303,'Soja',22.8,11.27),
(304,'Soja',22.9,11.38),
(305,'Soja',23.0,11.50),
(306,'Soja',23.1,11.61),
(307,'Soja',23.2,11.73),
(308,'Soja',23.3,11.84),
(309,'Soja',23.4,11.96),
(310,'Soja',23.5,12.07),
(311,'Soja',23.6,12.19),
(312,'Soja',23.7,12.30),
(313,'Soja',23.8,12.42),
(314,'Soja',23.9,12.53),
(315,'Soja',24.0,12.65),
(316,'Soja',24.1,12.76),
(317,'Soja',24.2,12.88),
(318,'Soja',24.3,12.99),
(319,'Soja',24.4,13.11),
(320,'Soja',24.5,13.22),
(321,'Soja',24.6,13.34),
(322,'Soja',24.7,13.45),
(323,'Soja',24.8,13.57),
(324,'Soja',24.9,13.68),
(325,'Soja',25.0,13.80),
(326,'Trigo',13.6,0.70),
(327,'Trigo',13.7,0.81),
(328,'Trigo',13.8,0.93),
(329,'Trigo',13.9,1.04),
(330,'Trigo',14.0,1.16),
(331,'Trigo',14.1,1.27),
(332,'Trigo',14.2,1.39),
(333,'Trigo',14.3,1.50),
(334,'Trigo',14.4,1.62),
(335,'Trigo',14.5,1.73),
(336,'Trigo',14.6,1.85),
(337,'Trigo',14.7,1.96),
(338,'Trigo',14.8,2.08),
(339,'Trigo',14.9,2.19),
(340,'Trigo',15.0,2.31),
(341,'Trigo',15.1,2.42),
(342,'Trigo',15.2,2.54),
(343,'Trigo',15.3,2.65),
(344,'Trigo',15.4,2.77),
(345,'Trigo',15.5,2.88),
(346,'Trigo',15.6,3.00),
(347,'Trigo',15.7,3.11),
(348,'Trigo',15.8,3.23),
(349,'Trigo',15.9,3.34),
(350,'Trigo',16.0,3.46),
(351,'Trigo',16.1,3.57),
(352,'Trigo',16.2,3.69),
(353,'Trigo',16.3,3.80),
(354,'Trigo',16.4,3.92),
(355,'Trigo',16.5,4.03),
(356,'Trigo',16.6,4.15),
(357,'Trigo',16.7,4.26),
(358,'Trigo',16.8,4.38),
(359,'Trigo',16.9,4.49),
(360,'Trigo',17.0,4.61),
(361,'Trigo',17.1,4.72),
(362,'Trigo',17.2,4.84),
(363,'Trigo',17.3,4.95),
(364,'Trigo',17.4,5.07),
(365,'Trigo',17.5,5.18),
(366,'Trigo',17.6,5.30),
(367,'Trigo',17.7,5.41),
(368,'Trigo',17.8,5.53),
(369,'Trigo',17.9,5.64),
(370,'Trigo',18.0,5.76),
(371,'Trigo',18.1,5.87),
(372,'Trigo',18.2,5.99),
(373,'Trigo',18.3,6.10),
(374,'Trigo',18.4,6.22),
(375,'Trigo',18.5,6.33),
(376,'Trigo',18.6,6.45),
(377,'Trigo',18.7,6.56),
(378,'Trigo',18.8,6.68),
(379,'Trigo',18.9,6.79),
(380,'Trigo',19.0,6.91),
(381,'Trigo',19.1,7.02),
(382,'Trigo',19.2,7.14),
(383,'Trigo',19.3,7.25),
(384,'Trigo',19.4,7.37),
(385,'Trigo',19.5,7.48),
(386,'Trigo',19.6,7.60),
(387,'Trigo',19.7,7.71),
(388,'Trigo',19.8,7.83),
(389,'Trigo',19.9,7.94),
(390,'Trigo',20.0,8.06),
(391,'Sorgo',13.6,0.70),
(392,'Sorgo',13.7,0.81),
(393,'Sorgo',13.8,0.93),
(394,'Sorgo',13.9,1.04),
(395,'Sorgo',14.0,1.16),
(396,'Sorgo',14.1,1.27),
(397,'Sorgo',14.2,1.39),
(398,'Sorgo',14.3,1.50),
(399,'Sorgo',14.4,1.62),
(400,'Sorgo',14.5,1.73),
(401,'Sorgo',14.6,1.85),
(402,'Sorgo',14.7,1.96),
(403,'Sorgo',14.8,2.08),
(404,'Sorgo',14.9,2.19),
(405,'Sorgo',15.0,2.31),
(406,'Sorgo',15.1,2.42),
(407,'Sorgo',15.2,2.54),
(408,'Sorgo',15.3,2.65),
(409,'Sorgo',15.4,2.77),
(410,'Sorgo',15.5,2.88),
(411,'Sorgo',15.6,3.00),
(412,'Sorgo',15.7,3.11),
(413,'Sorgo',15.8,3.23),
(414,'Sorgo',15.9,3.34),
(415,'Sorgo',16.0,3.46),
(416,'Sorgo',16.1,3.57),
(417,'Sorgo',16.2,3.69),
(418,'Sorgo',16.3,3.80),
(419,'Sorgo',16.4,3.92),
(420,'Sorgo',16.5,4.03),
(421,'Sorgo',16.6,4.15),
(422,'Sorgo',16.7,4.26),
(423,'Sorgo',16.8,4.38),
(424,'Sorgo',16.9,4.49),
(425,'Sorgo',17.0,4.61),
(426,'Sorgo',17.1,4.72),
(427,'Sorgo',17.2,4.84),
(428,'Sorgo',17.3,4.95),
(429,'Sorgo',17.4,5.07),
(430,'Sorgo',17.5,5.18),
(431,'Sorgo',17.6,5.30),
(432,'Sorgo',17.7,5.41),
(433,'Sorgo',17.8,5.53),
(434,'Sorgo',17.9,5.64),
(435,'Sorgo',18.0,5.76),
(436,'Sorgo',18.1,5.87),
(437,'Sorgo',18.2,5.99),
(438,'Sorgo',18.3,6.10),
(439,'Sorgo',18.4,6.22),
(440,'Sorgo',18.5,6.33),
(441,'Sorgo',18.6,6.45),
(442,'Sorgo',18.7,6.56),
(443,'Sorgo',18.8,6.68),
(444,'Sorgo',18.9,6.79),
(445,'Sorgo',19.0,6.91),
(446,'Sorgo',19.1,7.02),
(447,'Sorgo',19.2,7.14),
(448,'Sorgo',19.3,7.25),
(449,'Sorgo',19.4,7.37),
(450,'Sorgo',19.5,7.48),
(451,'Sorgo',19.6,7.60),
(452,'Sorgo',19.7,7.71),
(453,'Sorgo',19.8,7.83),
(454,'Sorgo',19.9,7.94),
(455,'Sorgo',20.0,8.06);
/*!40000 ALTER TABLE `merma_humedad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'2014_10_12_000000_create_users_table',1),
(2,'2014_10_12_100000_create_password_reset_tokens_table',1),
(3,'2019_08_19_000000_create_failed_jobs_table',1),
(4,'2019_12_14_000001_create_personal_access_tokens_table',1),
(5,'2025_04_07_233219_create_barlovento_ingresos_table',1),
(6,'2025_04_08_015001_create_consignatarios_table',1),
(7,'2025_04_08_015253_create_barlovento_egresos_table',1),
(8,'2025_04_08_020857_create_cereales_table',1),
(9,'2025_04_23_124107_create_comisionistas_table',2),
(10,'2025_04_23_224616_create_barlovento_cereales_table',3),
(11,'2025_04_23_224616_create_paihuen_cereales_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paihuen_cereales`
--

DROP TABLE IF EXISTS `paihuen_cereales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paihuen_cereales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `cereal` varchar(255) NOT NULL,
  `cartaPorte` varchar(255) NOT NULL,
  `vendedor` varchar(255) NOT NULL,
  `pesoBruto` int(11) NOT NULL,
  `pesoTara` int(11) NOT NULL,
  `humedad` int(11) NOT NULL,
  `mermaHumedad` int(11) NOT NULL,
  `calidad` varchar(255) NOT NULL,
  `materiasExtranas` varchar(255) NOT NULL,
  `tierra` tinyint(1) NOT NULL,
  `granosRotos` tinyint(1) NOT NULL,
  `granosQuebrados` tinyint(1) NOT NULL,
  `destino` varchar(255) NOT NULL,
  `observaciones` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paihuen_cereales`
--

LOCK TABLES `paihuen_cereales` WRITE;
/*!40000 ALTER TABLE `paihuen_cereales` DISABLE KEYS */;
INSERT INTO `paihuen_cereales` VALUES
(1,'2025-04-26','Maiz','5134134','Vendedor 1',2000000,100000,18,0,'muyBuena','0',0,0,0,'siloBolsa',NULL,'2025-04-26 17:42:19','2025-04-26 17:42:19');
/*!40000 ALTER TABLE `paihuen_cereales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Mauro','mauro@mauro.com',NULL,'$2y$10$x82RIEYORfQma2IGm/R5KOagYM4XboynblIQSPD.TvsZzaIvJ20Ne','ig5KIeeaUZwU31jfmLrkRykmc7j0gOzqvomaVum36fVKjyxMJ9ZAeOHiZFOs','2025-04-22 19:39:46','2025-04-22 19:39:46');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'controlBalanza'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-27 22:41:33
