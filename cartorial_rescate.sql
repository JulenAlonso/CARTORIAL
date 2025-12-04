-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: cartorial
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `gastos`
--

DROP TABLE IF EXISTS `gastos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gastos` (
  `id_gasto` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_vehiculo` int(10) unsigned NOT NULL,
  `id_usuario` int(10) unsigned DEFAULT NULL,
  `fecha_gasto` date NOT NULL,
  `tipo_gasto` varchar(50) NOT NULL,
  `importe` decimal(10,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `archivo_path` varchar(255) DEFAULT NULL,
  `archivo_nombre` varchar(255) DEFAULT NULL,
  `archivo_mime` varchar(100) DEFAULT NULL,
  `archivo_size` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_gasto`),
  KEY `idx_gastos_vehiculo` (`id_vehiculo`),
  KEY `idx_gastos_usuario` (`id_usuario`),
  KEY `ix_gastos_tipo_fecha` (`tipo_gasto`,`fecha_gasto`),
  CONSTRAINT `fk_gastos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_gastos_vehiculo` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gastos`
--

LOCK TABLES `gastos` WRITE;
/*!40000 ALTER TABLE `gastos` DISABLE KEYS */;
INSERT INTO `gastos` VALUES (9,4,2,'2025-11-28','seguro',200.00,'Seguro Mutua',NULL,NULL,NULL,NULL),(10,7,1,'2025-11-30','combustible',35.00,'Diesel',NULL,NULL,NULL,NULL),(11,7,1,'2025-11-30','mantenimiento',200.00,'Ruedas nuevas',NULL,NULL,NULL,NULL),(13,7,1,'2025-12-01','combustible',40.00,'Petroprix - Colmenar Viejo',NULL,NULL,NULL,NULL),(14,7,1,'2025-11-19','mantenimiento',284.35,'Taller Mecánico Rodríguez S.L.','gastos/sQqmOGA1btFVoLKYGdgQ9WdFpBot99yGUjpIeY8K.pdf',NULL,NULL,NULL),(15,7,1,'2025-12-01','seguro',356.40,'POLIZA DE SEGURO DE AUTOMÓVIL.','gastos/HN5DOfxmRqj5DLHBBisXPyaVixmxtliaupsRgxmr.pdf',NULL,NULL,NULL),(16,7,1,'2025-12-01','mantenimiento',2.00,'Limpiacristales.',NULL,NULL,NULL,NULL),(17,7,1,'2025-12-01','otros',10.00,'Prueba - Añadir gasto',NULL,NULL,NULL,NULL),(18,7,1,'2025-12-02','combustible',40.00,'Depósito',NULL,NULL,NULL,NULL),(19,12,7,'2025-12-03','combustible',50.00,'Gasolinera del infierno',NULL,NULL,NULL,NULL),(20,12,7,'2025-12-03','seguro',369.00,'Seguro - Mutua',NULL,NULL,NULL,NULL),(21,13,9,'2025-12-03','seguro',292.00,'Genesis',NULL,NULL,NULL,NULL),(22,13,9,'2025-12-03','combustible',45.00,'Deposito de vuelta Valencia',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `gastos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notas_calendario`
--

DROP TABLE IF EXISTS `notas_calendario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notas_calendario` (
  `id_nota` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `id_vehiculo` int(10) unsigned DEFAULT NULL,
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_evento` date DEFAULT NULL,
  `hora_evento` time DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_nota`),
  KEY `idx_notas_usuario` (`id_usuario`),
  KEY `idx_notas_vehiculo` (`id_vehiculo`),
  CONSTRAINT `fk_notas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_notas_vehiculo` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notas_calendario`
--

LOCK TABLES `notas_calendario` WRITE;
/*!40000 ALTER TABLE `notas_calendario` DISABLE KEYS */;
INSERT INTO `notas_calendario` VALUES (1,1,NULL,'Mirar aceite','Rellenar','2025-11-30','08:00:00','2025-11-28 07:40:16'),(2,1,7,'Aceite','Mirar aceite','2025-12-01','17:30:00','2025-11-30 20:23:58'),(3,1,7,'Viaje','Viaje a Donosti','2025-12-05','15:30:00','2025-11-30 20:27:45'),(4,6,11,'LLamada Mecánico','Cambio Airbag','2025-12-02','17:45:00','2025-12-02 18:10:45');
/*!40000 ALTER TABLE `notas_calendario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registros_km`
--

DROP TABLE IF EXISTS `registros_km`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registros_km` (
  `id_registro_km` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_vehiculo` int(10) unsigned NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `km_actual` int(10) unsigned NOT NULL,
  `comentario` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_registro_km`),
  KEY `idx_regkm_vehiculo` (`id_vehiculo`),
  KEY `ix_registroskm_fecha` (`fecha_registro`),
  CONSTRAINT `fk_regkm_vehiculo` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registros_km`
--

LOCK TABLES `registros_km` WRITE;
/*!40000 ALTER TABLE `registros_km` DISABLE KEYS */;
INSERT INTO `registros_km` VALUES (17,7,'2025-11-30 18:56:15',78000,'Kilometraje inicial al registrar el vehículo'),(20,7,'2025-11-30 19:23:04',162000,'Kilometraje actual.'),(21,7,'2025-11-30 20:22:41',162050,'Prueba'),(25,10,'2025-12-01 19:47:03',120000,'Kilometraje inicial al registrar el vehículo'),(26,7,'2025-12-01 19:03:36',162090,'Colmenar - Tres Cantos.'),(27,7,'2025-12-01 19:49:29',162092,'Prueba - Rueda Guardar KM'),(28,11,'2025-12-02 18:08:14',320000,'Kilometraje inicial al registrar el vehículo'),(29,11,'2025-12-02 18:09:31',320500,'Viaje a Duruelo'),(30,7,'2025-12-02 18:17:14',163000,'Colmenar - Tres Cantos (ida y vuelta).'),(31,12,'2025-12-03 10:46:21',150000,'Kilometraje inicial al registrar el vehículo'),(32,13,'2025-12-03 11:03:36',120000,'Kilometraje inicial al registrar el vehículo');
/*!40000 ALTER TABLE `registros_km` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id_usuario` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(150) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `user_name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_avatar` varchar(255) DEFAULT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Julen','Alonso Alvarez','julen.alonso@hotmail.com','623105133','julen0','$2y$12$OJYiMZ.ae3erjBF78eoJF.JleLA3XxOcbUG/gsbVh6qHQjmuxHcqG','avatars/7NxkHta26a1YIaokSH2LlYmNxlj3sJXHrb9Wp9Ks.jpg',1),(2,'lourdes','Pineros Gómez','lll@gmail.com',NULL,'lu1','$2y$12$Ez94geqgKdtNEQH28NfCWeqkl1rgV1cSzC/h.AVRkd3tQjspW3i5m','assets/images/user.png',0),(3,'Alvaro','Alonso Laquidain','alvaro@gmail.com',NULL,'alvaro0','$2y$12$0xFCStNCm.z8GlEn8xDHMemPdgqY2BSx3fhnOO0aqgZyEcEdooHfy','assets/images/user.png',0),(5,'prueba','prueba prueba','prueba@gmail.com','623105123','prueba','$2y$12$A0O7uoOTGnwpC.fV4mazEOoGCppqBBXM5zhhb5jY/PcWP/vqe1RDW','assets/images/user.png',0),(6,'Monica','Vicente Pascual','mvp@gmail.com','658710092','mvp0','$2y$12$c3T/cbFt.GohCL4VqyrRx.EDhEcb24mQbi2W3Nfs6mVsk.Ru9dqr2','assets/images/user.png',0),(7,'Javi','Gonzalez Varela','javivi@gmail.com','666000001','javivi69','$2y$12$GahOI50Cp06YJVR03HO1A.cYR7sZN3h6bEExLt7E7vREs.ZBJ66ju','avatars/U6wHa0NZgTPPwX7ETPjGLL7YtdP6eXlaCIoZyuID.jpg',0),(9,'Kevin','Caballero Cedillo','kevincc@gmail.com','600000002','Kenji','$2y$12$yawhsxcTbjcY58.M4frTP.mWjGfbOwOtB9PPc3H3EvNgh7HlA9xU2','assets/images/user.png',0);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehiculos`
--

DROP TABLE IF EXISTS `vehiculos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehiculos` (
  `id_vehiculo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `anio_fabricacion` smallint(5) unsigned DEFAULT NULL,
  `anio_matriculacion` smallint(5) unsigned DEFAULT NULL,
  `anio` smallint(5) unsigned DEFAULT NULL,
  `fecha_compra` date DEFAULT NULL,
  `km` int(10) unsigned DEFAULT 0,
  `cv` smallint(5) unsigned DEFAULT NULL,
  `combustible` varchar(30) DEFAULT NULL,
  `etiqueta` varchar(10) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `precio_segunda_mano` decimal(10,2) DEFAULT NULL,
  `car_avatar` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_vehiculo`),
  UNIQUE KEY `matricula` (`matricula`),
  KEY `idx_vehiculos_id_usuario` (`id_usuario`),
  KEY `ix_vehiculo_marca_modelo` (`marca`,`modelo`),
  CONSTRAINT `fk_vehiculos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehiculos`
--

LOCK TABLES `vehiculos` WRITE;
/*!40000 ALTER TABLE `vehiculos` DISABLE KEYS */;
INSERT INTO `vehiculos` VALUES (4,2,'3237hhp','Renault','Laguna',1994,2011,NULL,'2008-11-22',260035,135,'Diésel','B',16000.00,7000.00,'cars/2/1yCfDxmj8SXNNIMWEPgjVIju0MCRjGLXzIqmvBpg.jpg'),(7,1,'7397GBW','Kia','Ceed',2006,2008,NULL,'2022-08-30',163000,115,'Diésel','B',18000.00,3000.00,'cars/1/JIGVs1WMBubEejKwlgGBwkKeaqIZBGQqlQQA1RUb.jpg'),(10,1,'0474FSW','Opel','Vivaro Furgón',2007,2007,NULL,'2007-07-07',120000,135,'Diésel','B',22000.00,NULL,'cars/1/jIcYNzbLBR1fRVXEaTpt0iqRbjqAvenlC60fCJM5.jpg'),(11,6,'1779BTN','BMW','320d',1998,2002,NULL,'2017-01-06',320500,150,'Diésel','No tiene',26000.00,3700.00,'cars/6/1uT8Hl4Lti0IQtQ31JFzLjrm3euqXzzLsQcpUIjl.jpg'),(12,7,'4189GHP','BMW','318d',1998,2008,NULL,'2018-12-03',150000,150,'Diésel','B',23000.00,5500.00,'cars/7/TcaGnsv65x0urLbyFzvhcKuX3dJhtRnxVFnjeoDR.jpg'),(13,9,'0049fsz','Honda','Civic (8ª gen)',2005,2007,NULL,'2023-07-08',120000,150,'Diésel','B',23000.00,5500.00,'cars/9/BH6iSlwTg8scugi2YlxqsDtsJVwOUZajXAqHjL33.jpg');
/*!40000 ALTER TABLE `vehiculos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-04 10:49:15
