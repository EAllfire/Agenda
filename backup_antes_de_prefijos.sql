-- MySQL dump 10.13  Distrib 8.0.40, for macos11.7 (x86_64)
--
-- Host: localhost    Database: agenda_hospital
-- ------------------------------------------------------
-- Server version	8.0.40

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `citas`
--

DROP TABLE IF EXISTS `citas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `citas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `paciente_id` int NOT NULL,
  `profesional_id` int DEFAULT NULL,
  `servicio_id` int DEFAULT NULL,
  `modalidad_id` int DEFAULT NULL,
  `estado_id` int DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `nota_interna` text,
  `nota_paciente` text,
  PRIMARY KEY (`id`),
  KEY `paciente_id` (`paciente_id`),
  KEY `profesional_id` (`profesional_id`),
  KEY `servicio_id` (`servicio_id`),
  KEY `modalidad_id` (`modalidad_id`),
  KEY `estado_id` (`estado_id`),
  CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`),
  CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`profesional_id`) REFERENCES `profesionales` (`id`),
  CONSTRAINT `citas_ibfk_3` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id`),
  CONSTRAINT `citas_ibfk_4` FOREIGN KEY (`modalidad_id`) REFERENCES `modalidades` (`id`),
  CONSTRAINT `citas_ibfk_5` FOREIGN KEY (`estado_id`) REFERENCES `estado_cita` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `citas`
--

LOCK TABLES `citas` WRITE;
/*!40000 ALTER TABLE `citas` DISABLE KEYS */;
INSERT INTO `citas` VALUES (1,'2025-09-19','08:30:00','10:00:00',1,1,7,2,1,'','ohnjadefihqef','Recuerde llegar 10 minutos antes de su cita y traer sus estudios previos si los tiene.'),(2,'2025-09-19','07:30:00','09:30:00',2,1,6,1,1,'','Información interna adicional','Recuerde llegar 10 minutos antes de su cita y traer sus estudios previos si los tiene.'),(3,'2025-09-19','08:00:00','09:30:00',1,1,6,1,1,'','nadnjnjk','Recuerde llegar 10 minutos antes de su cita y traer sus estudios previos si los tiene.'),(4,'2025-09-20','12:00:00','12:30:00',2,1,6,1,1,'','Información interna adicional','Recuerde llegar 10 minutos antes de su cita y traer sus estudios previos si los tiene.'),(5,'2025-09-20','08:00:00','09:30:00',1,2,8,2,1,'','Información interna adicional','Recuerde llegar 10 minutos antes de su cita y traer sus estudios previos si los tiene.'),(6,'2025-09-23','08:00:00','09:30:00',1,1,1,1,1,'','Información interna adicional','Recuerde llegar 10 minutos antes de su cita y traer sus estudios previos si los tiene.'),(7,'2025-09-25','10:00:00','10:30:00',1,1,1,1,5,'','ekjhgjkqeeee','Recuerde llegar 10 minutos antes de su cita y traer sus estudios previos si los tiene.'),(8,'2025-09-25','08:30:00','09:30:00',2,2,8,2,1,'','qerfqefqef','Recuerde llegar 10 minutos antes de su cita y traer sus estudios previos si los tiene.'),(10,'2025-09-25','11:00:00','11:30:00',2,2,9,2,6,'','ewrfwe','Recuerde llegar 10 minutos antes de su cita y traer sus estudios previos si los tiene.'),(11,'2025-09-25','10:30:00','12:32:00',4,3,11,3,4,'','dfv','Recuerde llegar 10 minutos antes de su cita y traer sus estudios previos si los tiene.'),(12,'2025-09-25','12:00:00','14:00:00',1,1,1,1,1,'','csadc','Recuerde llegar 10 minutos antes de su cita y traer sus estudios previos si los tiene.'),(13,'2025-09-27','15:00:00','16:00:00',5,1,12,4,1,'individual','Reserva web - Cliente: Pedro Morales Perez | Email: pedro@gmail.com','rsfgwrtgwrtgwrgw'),(14,'2025-09-26','10:00:00','11:00:00',6,1,11,3,1,'individual','Reserva web - Cliente: Aron Perez Rodriguez | Email: aron@gmail.com','wEFAwefwaEF'),(15,'2025-09-29','08:00:00','08:30:00',3,2,9,2,2,'','aedfgaefg','Recuerde llegar 10 minutos antes de su cita y traer sus estudios previos si los tiene.'),(16,'2025-09-30','12:00:00','13:00:00',7,1,3,4,1,'individual','Reserva web - Cliente: Svein Flores | Email: svein@gmail.com','');
/*!40000 ALTER TABLE `citas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado_cita`
--

DROP TABLE IF EXISTS `estado_cita`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estado_cita` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado_cita`
--

LOCK TABLES `estado_cita` WRITE;
/*!40000 ALTER TABLE `estado_cita` DISABLE KEYS */;
INSERT INTO `estado_cita` VALUES (1,'reservado'),(2,'confirmado'),(3,'asistió'),(4,'no asistió'),(5,'pendiente'),(6,'en espera');
/*!40000 ALTER TABLE `estado_cita` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mensajes`
--

DROP TABLE IF EXISTS `mensajes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mensajes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cita_id` int NOT NULL,
  `fecha_envio` datetime DEFAULT CURRENT_TIMESTAMP,
  `mensaje` text,
  `estado_envio` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cita_id` (`cita_id`),
  CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensajes`
--

LOCK TABLES `mensajes` WRITE;
/*!40000 ALTER TABLE `mensajes` DISABLE KEYS */;
/*!40000 ALTER TABLE `mensajes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modalidades`
--

DROP TABLE IF EXISTS `modalidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modalidades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modalidades`
--

LOCK TABLES `modalidades` WRITE;
/*!40000 ALTER TABLE `modalidades` DISABLE KEYS */;
INSERT INTO `modalidades` VALUES (1,'Radiología'),(2,'Mastografía'),(3,'Ultrasonido'),(4,'Tomografía'),(5,'Resonancia Magnética'),(6,'Laboratorio');
/*!40000 ALTER TABLE `modalidades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pacientes`
--

DROP TABLE IF EXISTS `pacientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pacientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `diagnostico` varchar(255) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `origen` varchar(50) DEFAULT NULL,
  `comentarios` text,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pacientes`
--

LOCK TABLES `pacientes` WRITE;
/*!40000 ALTER TABLE `pacientes` DISABLE KEYS */;
INSERT INTO `pacientes` VALUES (1,'Tito','Pérez','625118881','titoperez@gmail.com','Fractura de tobillo','PENSIONES','urgencias','Paciente con antecedentes de fractura previa.','2025-09-19 16:06:35'),(2,'Fernando','Pérez','625118881','fersperez@gmail.com','Fractura de tobillo','ISSSTE','urgencias','Paciente con antecedentes de fractura previa.','2025-09-19 16:22:47'),(3,'Ricardo','Pérez','625118881','juanperez@gmail.com','Fractura de tobillo','PARTICULAR','urgencias','Paciente con antecedentes de fractura previa.','2025-09-23 19:18:37'),(4,'Abisasi','Ordonez','62525256356','abi@gmail.com','Homeopata','IMSS','externo','lkadfvhaej','2025-09-26 02:41:36'),(5,'Pedro','Morales Perez','6255255121761267','pedro@gmail.com',NULL,'cliente','web','Fecha nacimiento: 2025-09-03 | rsfgwrtgwrtgwrgw','2025-09-26 21:53:37'),(6,'Aron','Perez Rodriguez','6252242422','aron@gmail.com',NULL,'cliente','web','Fecha nacimiento: 1975-01-17 | wEFAwefwaEF','2025-09-26 22:08:02'),(7,'Svein','Flores','6251562675','svein@gmail.com',NULL,'cliente','web','Fecha nacimiento: 1996-12-31','2025-09-30 19:10:20');
/*!40000 ALTER TABLE `pacientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paquetes`
--

DROP TABLE IF EXISTS `paquetes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paquetes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) DEFAULT NULL,
  `servicios_incluidos` text,
  `vigencia_dias` int DEFAULT '365',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paquetes`
--

LOCK TABLES `paquetes` WRITE;
/*!40000 ALTER TABLE `paquetes` DISABLE KEYS */;
INSERT INTO `paquetes` VALUES (1,'Paquete Maternidad','Incluye consultas prenatales, ultrasonidos, laboratorios y atención del parto',15000.00,'[\"Consulta Prenatal\", \"Ultrasonido Obstétrico\", \"Laboratorios Maternales\", \"Atención del Parto\"]',270),(2,'Paquete Chequeo Ejecutivo','Chequeo médico completo con estudios de imagen y laboratorios',8500.00,'[\"Consulta Médica\", \"Radiografía de Tórax\", \"Electrocardiograma\", \"Perfil Completo de Laboratorios\"]',365),(3,'Paquete Cesárea','Procedimiento de cesárea con hospitalización y medicamentos',25000.00,'[\"Cesárea\", \"Hospitalización 3 días\", \"Medicamentos\", \"Consulta de seguimiento\"]',30),(4,'Paquete Cirugía General','Cirugía general ambulatoria con consultas de seguimiento',18000.00,'[\"Consulta Preoperatoria\", \"Cirugía\", \"Medicamentos\", \"Consultas de Seguimiento\"]',60),(5,'Paquete Cirugía Cardiovascular','Procedimiento cardiovascular con hospitalización especializada',45000.00,'[\"Estudios Preoperatorios\", \"Cirugía Cardiovascular\", \"Hospitalización\", \"Seguimiento Especializado\"]',90);
/*!40000 ALTER TABLE `paquetes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profesionales`
--

DROP TABLE IF EXISTS `profesionales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profesionales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profesionales`
--

LOCK TABLES `profesionales` WRITE;
/*!40000 ALTER TABLE `profesionales` DISABLE KEYS */;
INSERT INTO `profesionales` VALUES (1,'Dr. Ramírez'),(2,'Dra. González'),(3,'Dr. Pérez');
/*!40000 ALTER TABLE `profesionales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servicios`
--

DROP TABLE IF EXISTS `servicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `servicios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `modalidad_id` int NOT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `duracion_minutos` int DEFAULT '30' COMMENT 'Duración del servicio en minutos',
  PRIMARY KEY (`id`),
  KEY `modalidad_id` (`modalidad_id`),
  CONSTRAINT `servicios_ibfk_1` FOREIGN KEY (`modalidad_id`) REFERENCES `modalidades` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servicios`
--

LOCK TABLES `servicios` WRITE;
/*!40000 ALTER TABLE `servicios` DISABLE KEYS */;
INSERT INTO `servicios` VALUES (1,'Radiografía','Estudio radiográfico para evaluación de estructuras óseas y tejidos blandos',1,450.00,120),(2,'Resonancia Magnética','Estudio avanzado de resonancia magnética nuclear con imágenes de alta resolución',5,3500.00,60),(3,'Tomografía','Tomografía computarizada con contraste para diagnóstico detallado',4,2800.00,30),(6,'Rayos X','Radiografía simple para evaluación inicial de lesiones óseas',1,350.00,120),(7,'Mamografía','Mastografía digital para detección temprana de cáncer de mama',2,850.00,60),(8,'Hemograma','Biometría hemática completa con conteo diferencial de células',2,180.00,120),(9,'Glucosa','Determinación de glucosa sérica en ayunas',2,200.00,30),(10,'Ultrasonido abdominal','Ultrasonografía abdominal completa para evaluación de órganos internos',3,750.00,120),(11,'Ultrasonido pélvico','Ultrasonografía pélvica transvaginal y transabdominal',3,650.00,60),(12,'TAC de tórax','Tomografía computarizada de tórax con y sin contraste',4,3200.00,60),(13,'TAC de abdomen','Tomografía computarizada abdominal con contraste oral e intravenoso',4,3000.00,120),(14,'Resonancia cerebral','Resonancia magnética nuclear cerebral con secuencias especializadas',5,4500.00,60),(15,'Resonancia columna','Resonancia magnética de columna vertebral completa',5,4200.00,120),(16,'Hemograma','Biometría hemática completa con conteo diferencial de células',6,180.00,30),(17,'Perfil Lipidico','Análisis completo de lípidos: colesterol total, HDL, LDL y triglicéridos',6,380.00,30),(18,'Quimica Sanginea','Estudio especializado: Quimica Sanginea',6,320.00,120);
/*!40000 ALTER TABLE `servicios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tipo` enum('admin','caja','lectura') NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`),
  UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  UNIQUE KEY `nombre_usuario_2` (`nombre_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (5,'Administrador','admin','admin@hospital.com','$2y$10$3tDRa8FCBq77uIaBdTtwM.qsat/pdjvwDR4OF56Kd3RKdluBt8Gz6','admin',1),(6,'Sandra','sandra','sandra@gmail.com','$2y$10$2dgUZ/S3QBhYWUJSUEQdR.ZKpwopaMqZvizZRbkIRjJBMPxCnR/s6','admin',1),(7,'Eli','eli','eli@gmail.com','$2y$10$NuxX0FKhFh92OahazyuqVOyTpcVw3z1Y38JY/Itajn8NQ5ty5kk2u','caja',1);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ventas_servicios`
--

DROP TABLE IF EXISTS `ventas_servicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ventas_servicios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paciente_id` int DEFAULT NULL,
  `servicio_id` int DEFAULT NULL,
  `cita_id` int DEFAULT NULL,
  `fecha_venta` date NOT NULL,
  `precio_pagado` decimal(10,2) DEFAULT NULL,
  `descuento` decimal(5,2) DEFAULT '0.00',
  `estado` enum('pendiente','pagado','cancelado') DEFAULT 'pendiente',
  `metodo_pago` enum('efectivo','tarjeta','transferencia','cheque') DEFAULT 'efectivo',
  `notas` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `paciente_id` (`paciente_id`),
  KEY `servicio_id` (`servicio_id`),
  KEY `cita_id` (`cita_id`),
  CONSTRAINT `ventas_servicios_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`),
  CONSTRAINT `ventas_servicios_ibfk_2` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id`),
  CONSTRAINT `ventas_servicios_ibfk_3` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas_servicios`
--

LOCK TABLES `ventas_servicios` WRITE;
/*!40000 ALTER TABLE `ventas_servicios` DISABLE KEYS */;
/*!40000 ALTER TABLE `ventas_servicios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-01 22:47:32
