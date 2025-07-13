-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: db_gescursos
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
-- Table structure for table `asig_modulo`
--

DROP TABLE IF EXISTS `asig_modulo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asig_modulo` (
  `id_asig_modulo` int(11) NOT NULL AUTO_INCREMENT,
  `id_modulo` int(11) DEFAULT NULL,
  `id_tipo_usuario` int(11) DEFAULT NULL,
  `fecha_asig` date DEFAULT NULL,
  PRIMARY KEY (`id_asig_modulo`),
  KEY `id_modulo` (`id_modulo`),
  KEY `id_usuario` (`id_tipo_usuario`),
  CONSTRAINT `asig_modulo_ibfk_1` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asig_modulo`
--

LOCK TABLES `asig_modulo` WRITE;
/*!40000 ALTER TABLE `asig_modulo` DISABLE KEYS */;
INSERT INTO `asig_modulo` VALUES (11,2,37,NULL),(13,2,1,NULL),(14,3,1,NULL),(17,5,1,NULL),(18,6,1,NULL),(19,7,1,NULL),(20,8,1,NULL),(22,9,1,NULL),(23,10,1,NULL),(24,10,3,NULL),(25,11,1,NULL),(26,11,3,NULL),(27,12,1,NULL),(28,13,3,NULL),(29,13,2,NULL),(30,14,2,NULL),(31,15,3,NULL),(32,10,2,NULL),(33,11,2,NULL);
/*!40000 ALTER TABLE `asig_modulo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asignacion_curso`
--

DROP TABLE IF EXISTS `asignacion_curso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asignacion_curso` (
  `id_asignacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_curso` int(11) DEFAULT NULL,
  `id_profesor` int(11) DEFAULT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `fecha_asignacion` date DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  PRIMARY KEY (`id_asignacion`),
  KEY `id_curso` (`id_curso`),
  KEY `id_profesor` (`id_profesor`),
  KEY `id_estudiante` (`id_estudiante`),
  CONSTRAINT `asignacion_curso_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE CASCADE,
  CONSTRAINT `asignacion_curso_ibfk_2` FOREIGN KEY (`id_profesor`) REFERENCES `profesor` (`id_profesor`),
  CONSTRAINT `asignacion_curso_ibfk_3` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asignacion_curso`
--

LOCK TABLES `asignacion_curso` WRITE;
/*!40000 ALTER TABLE `asignacion_curso` DISABLE KEYS */;
INSERT INTO `asignacion_curso` VALUES (22,2,9,NULL,'2024-10-23',NULL,'activo'),(23,10,9,NULL,'2024-10-29',NULL,'activo');
/*!40000 ALTER TABLE `asignacion_curso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asistencia`
--

DROP TABLE IF EXISTS `asistencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asistencia` (
  `id_asistencia` int(11) NOT NULL AUTO_INCREMENT,
  `id_estudiante` int(11) DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `presente` enum('si','no','','') DEFAULT NULL,
  `justificacion` text DEFAULT NULL,
  `estado` enum('presente','ausente','retardo') NOT NULL DEFAULT 'ausente',
  PRIMARY KEY (`id_asistencia`),
  KEY `id_estudiante` (`id_estudiante`),
  KEY `id_curso` (`id_curso`),
  CONSTRAINT `asistencia_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`),
  CONSTRAINT `asistencia_ibfk_2` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistencia`
--

LOCK TABLES `asistencia` WRITE;
/*!40000 ALTER TABLE `asistencia` DISABLE KEYS */;
INSERT INTO `asistencia` VALUES (4,1,1,'2024-10-13','si',NULL,'ausente'),(5,1,1,'2024-10-13','no',NULL,'ausente'),(6,1,1,'2024-10-13',NULL,NULL,'presente'),(7,1,1,'2024-12-04',NULL,NULL,'ausente'),(8,1,10,'2024-10-30',NULL,NULL,'ausente');
/*!40000 ALTER TABLE `asistencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carousel`
--

DROP TABLE IF EXISTS `carousel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carousel` (
  `id_carrousel` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `order_index` int(11) NOT NULL,
  `fecha_curso_inicio` date NOT NULL,
  `fecha_curso_fin` date NOT NULL,
  PRIMARY KEY (`id_carrousel`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carousel`
--

LOCK TABLES `carousel` WRITE;
/*!40000 ALTER TABLE `carousel` DISABLE KEYS */;
INSERT INTO `carousel` VALUES (50,'Curso De Banda','Anímate a inscribirte a nuestro curso de banda, marcha con el corsaje','452083467_901651245322247_7937504907709867816_n.jpg',0,'2025-08-22','2026-08-22'),(51,'Curso de Pastoral','Anímate a incribirte a nuestro curso de pastoral y descubre tu potencial.','448172350_874612134692825_6660471295016287333_n.jpg',0,'2025-02-25','2025-02-15'),(52,'Sinfonica','Animate a inscribirte a nuestro curos de sinfónica, descubre tu nota musical','441954466_859522112868494_5875778135578260400_n.jpg',0,'2025-08-15','2025-08-15');
/*!40000 ALTER TABLE `carousel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categoria_curso`
--

DROP TABLE IF EXISTS `categoria_curso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categoria_curso` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(255) NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria_curso`
--

LOCK TABLES `categoria_curso` WRITE;
/*!40000 ALTER TABLE `categoria_curso` DISABLE KEYS */;
INSERT INTO `categoria_curso` VALUES (1,'Danza'),(2,'Ajedrez'),(3,'Pastoral'),(6,'Sin categoria');
/*!40000 ALTER TABLE `categoria_curso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cursos`
--

DROP TABLE IF EXISTS `cursos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cursos` (
  `id_curso` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_curso` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `nivel_educativo` enum('primaria','secundaria','terciaria') NOT NULL,
  `duracion` int(3) NOT NULL COMMENT 'Duración en semanas',
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `icono` varchar(255) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `id_profesor` int(11) DEFAULT NULL,
  `horarios` text DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_curso`),
  KEY `fk_categoria` (`id_categoria`),
  KEY `id_profesor` (`id_profesor`),
  CONSTRAINT `cursos_ibfk_1` FOREIGN KEY (`id_profesor`) REFERENCES `profesor` (`id_profesor`),
  CONSTRAINT `fk_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria_curso` (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cursos`
--

LOCK TABLES `cursos` WRITE;
/*!40000 ALTER TABLE `cursos` DISABLE KEYS */;
INSERT INTO `cursos` VALUES (1,'Danzas','Curso de danzas','primaria',3,'activo','icon_66df8b8983f491.65048104.jpg',1,NULL,NULL,NULL),(2,'Ajedrez','Curso de ajedrez','terciaria',3,'activo','icon_66df8ca659c689.92859720.jpg',2,NULL,NULL,NULL),(10,'Baloncesto','Participa y juega','secundaria',12,'activo','icon_671a4b78f2a112.67630683.jpg',1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `cursos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_gescursoslecturas_mensajes`
--

DROP TABLE IF EXISTS `db_gescursoslecturas_mensajes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_gescursoslecturas_mensajes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mensaje` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_lectura` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_mensaje` (`id_mensaje`,`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=761 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_gescursoslecturas_mensajes`
--

LOCK TABLES `db_gescursoslecturas_mensajes` WRITE;
/*!40000 ALTER TABLE `db_gescursoslecturas_mensajes` DISABLE KEYS */;
INSERT INTO `db_gescursoslecturas_mensajes` VALUES (1,1,56,'2024-10-06 23:44:10'),(2,4,56,'2024-10-09 19:16:08'),(3,5,56,'2024-10-09 19:16:08'),(4,6,56,'2024-10-09 19:16:07'),(5,7,56,'2024-10-09 19:16:06'),(6,9,56,'2024-10-06 18:46:24'),(14,10,56,'2024-10-07 00:06:58'),(15,12,56,'2024-10-07 00:07:01'),(23,13,56,'2024-10-06 18:34:53'),(28,14,56,'2024-10-06 18:22:01'),(30,15,56,'2024-10-06 18:36:18'),(31,16,56,'2024-10-06 18:22:03'),(32,17,56,'2024-10-06 18:22:03'),(45,11,56,'2024-10-06 18:37:59'),(101,18,56,'2024-10-06 18:34:24'),(162,19,56,'2024-10-07 00:07:00'),(165,20,56,'2024-10-06 23:51:51'),(227,22,56,'2024-10-07 00:07:00'),(418,26,56,'2024-10-07 00:05:24'),(419,26,53,'2024-10-06 23:28:43'),(420,24,53,'2024-10-06 23:28:43'),(421,22,53,'2024-10-06 23:28:25'),(426,20,53,'2024-10-06 23:27:43'),(427,6,53,'2024-10-06 23:27:47'),(428,4,53,'2024-10-06 23:27:48'),(429,5,53,'2024-10-06 23:27:39'),(434,19,53,'2024-10-06 23:27:43'),(435,7,53,'2024-10-06 23:27:47'),(436,12,53,'2024-10-06 23:27:44'),(437,10,53,'2024-10-06 23:28:26'),(441,1,53,'2024-10-06 23:27:48'),(463,37,56,'2024-10-09 19:16:05'),(466,38,56,'2024-10-09 19:16:04'),(467,38,53,'2024-10-08 04:36:41'),(469,39,53,'2024-10-08 19:56:46'),(471,40,56,'2024-10-06 23:37:19'),(474,39,56,'2024-10-09 19:16:04'),(481,40,53,'2024-10-08 19:56:47'),(484,41,56,'2024-10-09 19:16:03'),(495,42,53,'2024-10-06 23:37:41'),(496,42,56,'2024-10-09 19:16:09'),(501,24,56,'2024-10-07 00:05:25'),(514,8,56,'2024-10-09 19:16:07'),(679,41,53,'2024-10-08 19:56:47'),(685,37,53,'2024-10-07 19:57:02'),(687,41,36,'2024-10-07 19:57:48'),(688,42,36,'2024-10-07 19:57:49'),(732,49,56,'2024-10-17 20:33:27'),(742,50,56,'2024-10-17 20:33:27'),(754,59,36,'2024-10-24 15:02:32'),(755,61,36,'2024-10-24 15:23:38'),(756,61,56,'2024-10-30 03:25:45'),(757,60,56,'2024-10-30 03:25:50'),(758,58,56,'2024-10-30 03:25:51'),(760,57,56,'2024-10-30 03:25:52');
/*!40000 ALTER TABLE `db_gescursoslecturas_mensajes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estudiante`
--

DROP TABLE IF EXISTS `estudiante`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estudiante` (
  `id_estudiante` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `genero` varchar(10) DEFAULT NULL,
  `fecha_registro` date DEFAULT curdate(),
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `nivel_educativo` varchar(50) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id_estudiante`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `fk_estudiante_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estudiante`
--

LOCK TABLES `estudiante` WRITE;
/*!40000 ALTER TABLE `estudiante` DISABLE KEYS */;
INSERT INTO `estudiante` VALUES (1,53,'M','5234-05-31','activo','secundaria','Perro marica'),(3,69,NULL,'2024-10-24','activo',NULL,NULL),(4,70,NULL,'2024-10-24','activo',NULL,NULL);
/*!40000 ALTER TABLE `estudiante` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historial_inscripciones`
--

DROP TABLE IF EXISTS `historial_inscripciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historial_inscripciones` (
  `id_historial` int(11) NOT NULL AUTO_INCREMENT,
  `id_inscripcion` int(11) DEFAULT NULL,
  `estado_anterior` enum('pendiente','aprobada','rechazada','cancelada') DEFAULT NULL,
  `estado_nuevo` enum('pendiente','aprobada','rechazada','cancelada') DEFAULT NULL,
  `fecha_cambio` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_usuario_cambio` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_historial`),
  KEY `id_inscripcion` (`id_inscripcion`),
  KEY `historial_inscripciones_ibfk_2` (`id_usuario_cambio`),
  CONSTRAINT `historial_inscripciones_ibfk_1` FOREIGN KEY (`id_inscripcion`) REFERENCES `inscripciones` (`id_inscripcion`),
  CONSTRAINT `historial_inscripciones_ibfk_2` FOREIGN KEY (`id_usuario_cambio`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_inscripciones`
--

LOCK TABLES `historial_inscripciones` WRITE;
/*!40000 ALTER TABLE `historial_inscripciones` DISABLE KEYS */;
INSERT INTO `historial_inscripciones` VALUES (92,77,'pendiente','aprobada','2024-10-24 02:41:14',36),(93,77,'aprobada','pendiente','2024-10-24 15:00:26',36),(94,77,'pendiente','aprobada','2024-10-24 15:13:03',36),(95,77,'aprobada','rechazada','2024-10-24 15:18:58',36),(96,78,'pendiente','aprobada','2024-10-30 03:27:17',56);
/*!40000 ALTER TABLE `historial_inscripciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horarios`
--

DROP TABLE IF EXISTS `horarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `horarios` (
  `id_horario` int(11) NOT NULL AUTO_INCREMENT,
  `id_curso` int(11) NOT NULL,
  `id_profesor` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `dia_semana` enum('lunes','martes','miércoles','jueves','viernes','sábado') NOT NULL,
  `lunes` varchar(20) DEFAULT NULL,
  `martes` varchar(20) DEFAULT NULL,
  `miercoles` varchar(20) DEFAULT NULL,
  `jueves` varchar(20) DEFAULT NULL,
  `viernes` varchar(20) DEFAULT NULL,
  `sabado` varchar(20) DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  PRIMARY KEY (`id_horario`),
  KEY `id_curso` (`id_curso`),
  KEY `id_profesor` (`id_profesor`),
  CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE CASCADE,
  CONSTRAINT `horarios_ibfk_2` FOREIGN KEY (`id_profesor`) REFERENCES `profesor` (`id_profesor`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horarios`
--

LOCK TABLES `horarios` WRITE;
/*!40000 ALTER TABLE `horarios` DISABLE KEYS */;
INSERT INTO `horarios` VALUES (30,10,9,'2024-10-30 03:29:51','lunes','06:00 - 07:00','07:00 - 08:00','14:00 - 15:00','16:00 - 17:00','17:30 - 19:07','18:30 - 20:00',NULL,NULL);
/*!40000 ALTER TABLE `horarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inscripciones`
--

DROP TABLE IF EXISTS `inscripciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inscripciones` (
  `id_inscripcion` int(11) NOT NULL AUTO_INCREMENT,
  `id_curso` int(11) DEFAULT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `fecha_inscripcion` date DEFAULT NULL,
  `estado` enum('pendiente','aprobada','rechazada','cancelada') NOT NULL DEFAULT 'pendiente',
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `comprobante_pago` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_inscripcion`),
  KEY `id_curso` (`id_curso`),
  KEY `id_estudiante` (`id_estudiante`),
  KEY `idx_inscripciones_curso_estudiante` (`id_curso`,`id_estudiante`),
  CONSTRAINT `inscripciones_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`),
  CONSTRAINT `inscripciones_ibfk_2` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inscripciones`
--

LOCK TABLES `inscripciones` WRITE;
/*!40000 ALTER TABLE `inscripciones` DISABLE KEYS */;
INSERT INTO `inscripciones` VALUES (77,1,1,'2024-10-23','rechazada','2024-10-24 15:18:58','../../uploads/comprobantes/1729737647_coomadenort.jpg'),(78,10,1,'2024-10-29','aprobada','2024-10-30 03:27:17','../uploads/comprobantes/1730258810_BASKET11.jpg');
/*!40000 ALTER TABLE `inscripciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

LOCK TABLES `login_attempts` WRITE;
/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
INSERT INTO `login_attempts` VALUES (13,'127.0.0.1','2024-10-14 12:16:07'),(14,'127.0.0.1','2024-10-24 08:47:47'),(15,'127.0.0.1','2024-10-24 08:47:59'),(16,'127.0.0.1','2024-10-24 09:46:01');
/*!40000 ALTER TABLE `login_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mensajes`
--

DROP TABLE IF EXISTS `mensajes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mensajes` (
  `id_mensaje` int(11) NOT NULL AUTO_INCREMENT,
  `id_remitente` int(11) DEFAULT NULL,
  `tipo_remitente` int(11) DEFAULT NULL,
  `tipo_destinatario` varchar(20) NOT NULL,
  `id_destinatario` int(11) DEFAULT NULL,
  `asunto` varchar(255) DEFAULT NULL,
  `contenido` text DEFAULT NULL,
  `fecha_envio` datetime DEFAULT current_timestamp(),
  `id_tipo_usuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_mensaje`),
  KEY `id_remitente` (`id_remitente`),
  KEY `id_destinatario` (`id_destinatario`),
  CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`id_remitente`) REFERENCES `usuario` (`id_usuario`),
  CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`id_destinatario`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensajes`
--

LOCK TABLES `mensajes` WRITE;
/*!40000 ALTER TABLE `mensajes` DISABLE KEYS */;
INSERT INTO `mensajes` VALUES (49,56,NULL,'individual',56,'asdasdasdasdasda','asdasd','2024-10-17 21:50:05',NULL),(50,56,NULL,'todos',NULL,'jhjh','ljkjklj','2024-10-17 22:19:30',NULL),(51,56,NULL,'individual',36,'Sharif','adfadjfk','2024-10-17 23:08:39',NULL),(52,56,NULL,'todos',NULL,'gjjj','vvjvj','2024-10-17 23:09:49',NULL),(53,56,NULL,'todos',NULL,'ihhi','jhkj','2024-10-17 23:10:21',NULL),(54,56,NULL,'todos',NULL,'njlk','lnkmmn','2024-10-17 23:10:45',NULL),(55,56,NULL,'todos',NULL,'njnn','nklnkl','2024-10-17 23:10:58',NULL),(56,56,NULL,'todos',NULL,'nknln','lknnkllnk','2024-10-17 23:11:13',NULL),(57,56,NULL,'todos',NULL,'nknkl','llknlnk','2024-10-17 23:11:36',NULL),(58,56,NULL,'todos',NULL,'nnmnm','mbjkkbj','2024-10-17 23:11:59',NULL),(59,36,NULL,'individual',56,'perro hp','su papa','2024-10-24 10:02:30',NULL),(60,53,NULL,'individual',56,'perro hp','parea baiar la bamba','2024-10-24 10:18:20',NULL),(61,36,NULL,'individual',56,'TE COMPRO TU NOVIA','TE COMPRO TU NOVIA\r\n','2024-10-24 10:23:35',NULL);
/*!40000 ALTER TABLE `mensajes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mensajes_eliminados`
--

DROP TABLE IF EXISTS `mensajes_eliminados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mensajes_eliminados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_mensaje` int(11) NOT NULL,
  `fecha_eliminacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_usuario_mensaje` (`id_usuario`,`id_mensaje`),
  KEY `id_mensaje` (`id_mensaje`),
  CONSTRAINT `mensajes_eliminados_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  CONSTRAINT `mensajes_eliminados_ibfk_2` FOREIGN KEY (`id_mensaje`) REFERENCES `mensajes` (`id_mensaje`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensajes_eliminados`
--

LOCK TABLES `mensajes_eliminados` WRITE;
/*!40000 ALTER TABLE `mensajes_eliminados` DISABLE KEYS */;
/*!40000 ALTER TABLE `mensajes_eliminados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modulos`
--

DROP TABLE IF EXISTS `modulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modulos` (
  `id_modulo` int(11) NOT NULL AUTO_INCREMENT,
  `nom_modulo` varchar(30) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `icono` varchar(255) NOT NULL,
  `orden` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_modulo`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulos`
--

LOCK TABLES `modulos` WRITE;
/*!40000 ALTER TABLE `modulos` DISABLE KEYS */;
INSERT INTO `modulos` VALUES (2,'Profesor','../models/profesor/profesor.php','school',6),(3,'Estudiante','../models/estudiante/estudiante.php','face\r\n',7),(5,'Usuarios','../models/usuarios/users.php','person',1),(6,'Cursos','models/cursos/cursos.php','assignment',4),(7,'Modulos','models/modulos/modulos.php','event',8),(8,'Inscripciones','models/inscripciones/inscripciones.php','card_travel',5),(9,'Index','models/admin_index/admin_index.php','home',9),(10,'Mensajes','models/mensajeria/mensajeria.php','question_answer',3),(11,'Perfil','models/perfil/perfil.php','person',2),(12,'Asig_Horario','models/horario/horarios_asignados.php','event',NULL),(13,'Horario','models/horario/cursos_listado.php','event',NULL),(14,'Asistencia','models/asistencia/asistencia.php','assignment_turned_in',NULL),(15,'Mi Asistencia','models/asistencia/asistencia_estudiante.php','assignment_turned_in',NULL);
/*!40000 ALTER TABLE `modulos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagos`
--

DROP TABLE IF EXISTS `pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL AUTO_INCREMENT,
  `id_estudiante` int(11) DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `fecha_pago` date DEFAULT NULL,
  PRIMARY KEY (`id_pago`),
  KEY `id_estudiante` (`id_estudiante`),
  CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos`
--

LOCK TABLES `pagos` WRITE;
/*!40000 ALTER TABLE `pagos` DISABLE KEYS */;
INSERT INTO `pagos` VALUES (2,1,12000.00,'2024-08-30');
/*!40000 ALTER TABLE `pagos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `id_usuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_password_resets_user_id` (`id_usuario`),
  KEY `idx_password_resets_token` (`token`),
  CONSTRAINT `fk_password_resets_user` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
INSERT INTO `password_resets` VALUES (46,'santiagocaponf@gmail.com','ab62cf32778dfe063a7afd8c87081bebcccd15900ac0ffbb98f56b4fee6c8aca','2024-10-02 19:32:34',36),(47,'santiagocaponf@gmail.com','3a43fab6750869f508850169d66ff43bdfb0ca059381c17a99ec2f5c3a24f0e8','2024-10-02 19:40:44',36),(48,'santiagocaponf@gmail.com','bcad622b7b016e3a263de308641be51c29abc94d02709f462882eb2b0e44bb10','2024-10-02 19:40:47',36),(49,'santiagocaponf@gmail.com','451b28b392d2a9ae0bd37a79e04b4814194c8d6e556087a61c9c70f0e350763b','2024-10-23 21:06:24',36);
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `preinscripciones`
--

DROP TABLE IF EXISTS `preinscripciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `preinscripciones` (
  `id_preinscripcion` int(11) NOT NULL AUTO_INCREMENT,
  `id_curso` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `fecha_preinscripcion` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','completada','cancelada') NOT NULL DEFAULT 'pendiente',
  `token` varchar(255) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_preinscripcion`),
  KEY `id_curso` (`id_curso`),
  KEY `fk_preinscripciones_usuario` (`id_usuario`),
  CONSTRAINT `fk_preinscripciones_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  CONSTRAINT `preinscripciones_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preinscripciones`
--

LOCK TABLES `preinscripciones` WRITE;
/*!40000 ALTER TABLE `preinscripciones` DISABLE KEYS */;
INSERT INTO `preinscripciones` VALUES (46,2,'camilo  prato','albertocamiloprato@gmail.com','3043282464','2024-10-13 21:28:44','pendiente','1a8014822dcc49abfb76578342002b11',56),(70,1,'camilo  prato','albertocamiloprato@gmail.com','3043282464','2024-10-17 21:19:39','pendiente','17ed7989509a151b8ffd8fbc86ce223b',56),(72,2,'Juanito Alimaña','juanit@gmail.com','5233456345123','2024-10-24 02:40:35','pendiente','8160032ea22638cee0d04f9cd8ecbc0b',53),(73,1,'Sinfonica','michel.camilo.566@co.co','3222','2024-10-24 14:32:38','pendiente','4f0d6618d2a61b6ac4f2093ec894b4f5',67);
/*!40000 ALTER TABLE `preinscripciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profesor`
--

DROP TABLE IF EXISTS `profesor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profesor` (
  `id_profesor` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `especialidad` varchar(255) DEFAULT NULL,
  `experiencia` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id_profesor`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `profesor_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profesor`
--

LOCK TABLES `profesor` WRITE;
/*!40000 ALTER TABLE `profesor` DISABLE KEYS */;
INSERT INTO `profesor` VALUES (9,60,'Deportista',12,'Buen profesor que baila la bamba'),(10,64,'',0,'');
/*!40000 ALTER TABLE `profesor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `remember_tokens`
--

DROP TABLE IF EXISTS `remember_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_token` (`id_usuario`,`token`),
  CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `remember_tokens`
--

LOCK TABLES `remember_tokens` WRITE;
/*!40000 ALTER TABLE `remember_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `remember_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resume_cursos`
--

DROP TABLE IF EXISTS `resume_cursos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resume_cursos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dia` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `lugar` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resume_cursos`
--

LOCK TABLES `resume_cursos` WRITE;
/*!40000 ALTER TABLE `resume_cursos` DISABLE KEYS */;
INSERT INTO `resume_cursos` VALUES (7,'30','Sinfonica','Colegio Sagrado Corazon De Jesus','\"Este curso de Sinfónica te ofrece la oportunidad de conocer y profundizar en el mundo de la música orquestal. A través de la teoría y práctica, aprenderás sobre los instrumentos, la interpretación en conjunto y el repertorio sinfónico, desarrollando habilidades tanto técnicas como musicales para participar en una orquesta sinfónica.\"'),(8,'48','Ajedrez','Colegio Sagrado Corazon De Jesus','\"En este curso de Ajedrez aprenderás estrategias, tácticas y técnicas para mejorar tu juego, desde los movimientos básicos hasta jugadas avanzadas. Desarrollarás habilidades de pensamiento crítico, resolución de problemas y toma de decisiones, mientras exploras el fascinante mundo de este milenario deporte mental.\"'),(9,'96','Baloncesto','Colegio Sagrado Corazon De Jesus','\"Este curso de Baloncesto está diseñado para mejorar tus habilidades en el deporte, desde fundamentos como el manejo del balón y los tiros, hasta tácticas de equipo y estrategias de juego. A través de sesiones prácticas, aprenderás a desarrollar tu resistencia, coordinación y trabajo en equipo, perfeccionando tu desempeño en la cancha.\"'),(10,'125','Natación','Colegio Sagrado Corazon De Jesus','\"En este curso de Natación aprenderás las técnicas fundamentales de los diferentes estilos de nado, mejorando tu resistencia, coordinación y técnica en el agua. Con entrenamientos prácticos y progresivos, desarrollarás confianza y habilidades para nadar de manera eficiente y segura, ya sea a nivel recreativo o competitivo.\"');
/*!40000 ALTER TABLE `resume_cursos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_usuario`
--

DROP TABLE IF EXISTS `tipo_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipo_usuario` (
  `id_tipo_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_tipo_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_usuario`
--

LOCK TABLES `tipo_usuario` WRITE;
/*!40000 ALTER TABLE `tipo_usuario` DISABLE KEYS */;
INSERT INTO `tipo_usuario` VALUES (1,'admin'),(2,'profesor'),(3,'estudiante'),(4,'user');
/*!40000 ALTER TABLE `tipo_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_module_order`
--

DROP TABLE IF EXISTS `user_module_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_module_order` (
  `id_usuario` int(11) NOT NULL,
  `module_order` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`module_order`)),
  PRIMARY KEY (`id_usuario`),
  CONSTRAINT `user_module_order_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_module_order`
--

LOCK TABLES `user_module_order` WRITE;
/*!40000 ALTER TABLE `user_module_order` DISABLE KEYS */;
INSERT INTO `user_module_order` VALUES (36,'[\"12\",\"8\",\"7\",\"2\",\"3\",\"5\",\"6\",\"9\",\"10\",\"11\"]'),(53,'[\"13\",\"15\",\"10\",\"11\"]'),(56,'[\"12\",\"5\",\"11\",\"10\",\"7\",\"6\",\"8\",\"2\",\"3\",\"9\"]'),(64,'[\"13\",\"14\",\"10\",\"2\",\"11\",\"3\"]');
/*!40000 ALTER TABLE `user_module_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `tipo_doc` varchar(10) DEFAULT NULL,
  `documento` varchar(200) DEFAULT NULL,
  `fecha_nac` date DEFAULT NULL,
  `foto` varchar(50) DEFAULT NULL,
  `mail` varchar(50) DEFAULT NULL,
  `telefono` varchar(25) DEFAULT NULL,
  `direccion` varchar(100) NOT NULL,
  `id_tipo_usuario` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `clave` varchar(255) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `ultimo_acceso` datetime DEFAULT NULL,
  `is_locked` tinyint(1) DEFAULT 0,
  `lock_timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  KEY `id_tipo_usuario` (`id_tipo_usuario`),
  CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_tipo_usuario`) REFERENCES `tipo_usuario` (`id_tipo_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (36,'Santiago','Capone','ID','12341235234','2345-03-12','WhatsApp Image 2024-07-23 at 4.49.07 PM.jpeg','santiagocaponf@gmail.com','32452345','CL 18 A NORTE 2 72',1,'alez','$2y$10$64T2Qk8yptB8y8Rk6Kq26uhnbT3Ias.JH.EXcin2d1BPQCzAvHiM6','2024-08-25 17:43:54','activo','2024-10-24 10:22:21',0,NULL),(42,'chad','sexteto','ID','523456346','3654-04-23','66d273c33d474_Recurso 9europe.jpg','luisillo@gmail.com','4563475674','CL 18 A NORTE 2 72',2,'alez23','$2y$10$FrpZXvgI3WrL22y9MxNtfuQsyQSgCJ7Jm4VPUv3Aa4qEn2HCKdxsK','2024-08-29 16:26:44','activo',NULL,0,NULL),(51,'antonela','sepulveda','ID','342352345','0005-04-23','66d23c1021bab_f7c0528d915ec3b38dd89bf7beb2a194.jpg','scflorez@corsaje.edu.co','42352345','CL 18 A NORTE 2 72',1,'mientras','$2y$10$KJU2liHj854T1T9M.6/EK.xDYy4sfLf2XEwCldj230rdreZmC.3KC','2024-08-30 16:39:28','activo',NULL,0,NULL),(53,'Juanitos','Alimaña','ID','43523634','0634-06-02','66d2441faa705_pngwing.com.png','juanit@gmail.com','5233456345123','CL 18 A NORTE 2 72',3,'alez123123','$2y$10$p.bJhCL9d2VM1IjUCnC63.Edj5Pg87KZgKGTFyedUHPusUd.QSDAK','2024-08-30 17:13:51','activo','2024-10-29 22:26:36',0,NULL),(55,'Santiago','Capon','Passport','4234523456','5234-04-23','pngwing.com.png','scflorez@corsaje.edu.co3','53643563456','CL 18 A NORTE 2 72',1,'alez1234','$2y$10$pcvzMHIh1F53bR25oEpRfu5MbZB5FO6Kn3ceIKwNBtp9KWahjApMe','2024-09-03 12:35:04','activo',NULL,0,NULL),(56,'camilo ','prato','ID','1091357317','2024-09-17','67032f8d169dd_images.png','albertocamiloprato@gmail.com','3043282464','Sapo Marica',1,'camilo','$2y$10$pkH8Zi8gEArSclW4KlpcjOm0Tbx5fSF2o8f7Ukw8qUNWj8Bl7i2I.','2024-09-07 18:45:23','activo','2024-10-30 20:14:14',0,NULL),(58,'santiago',NULL,NULL,NULL,NULL,NULL,'edison_alberto@hotmail.com','52343456','',1,'edison_alberto','$2y$10$DLJSPUZnsBduhl5PFRtg6uP5aXma0xTP9FOSkKUN/g2l9MrcP7d3S','2024-09-19 12:02:34','activo',NULL,0,NULL),(59,'Santiago',NULL,NULL,NULL,NULL,NULL,'scapon@misena.edu.co','3034235435','',1,'scapon','$2y$10$yrrLCg7Fr85s6u9jOmiVMO14UhXLMOrHN6krm2bL4fCNqnnCqc4Oy','2024-09-20 20:09:22','activo',NULL,0,NULL),(60,'Alirio','Moncada','ID','3453453346346','5234-04-23','671a4e6e8c19d_jkjhh.png','albertocamiloprato@gmail.comw','563456346','klerklefjkjdvjkldfj',2,'alberto','$2y$10$PPmdLYLFpZqdPujTBjE46eMTV61llgeDljmfnqdVbiGQoj3XjgbTK','2024-09-20 20:50:39','activo','2024-10-29 22:23:50',0,NULL),(63,NULL,NULL,NULL,NULL,NULL,NULL,'santigao@gmail.com',NULL,'',1,'alez1233','$2y$10$DSX8990wWKG04J/82ENXo.xZAJyQn/flaX2ULl1gFLB3TuZKQpMZ6','2024-09-28 20:04:00','activo',NULL,0,NULL),(64,'camilo','prato profe','ID','13450735','2000-08-14','670c345acc2a8_fondos-de-pantalla-3d-paisaje.jpg','camiloprato234@gmail.com','3043282464','Brr Atalaya',2,'camilop','$2y$10$1Djh88ty26viA.IG41s4oOFrO5NU.mrAiw.6b3VnEVTPDj0qKeg2q','2024-10-13 09:06:40','activo','2024-10-23 00:15:02',0,NULL),(65,NULL,NULL,NULL,NULL,NULL,NULL,'alezio@gmail.com',NULL,'',4,'alezio','$2y$10$Aoi7mRPqFZgLA2/7VM8f0eelkVsEaH.HxAKI3IoZ/PcdARIb5koze','2024-10-23 21:43:42','activo','2024-10-23 21:44:17',0,NULL),(66,NULL,NULL,NULL,NULL,NULL,NULL,'abortopacamodda@gmail.com',NULL,'',3,'camilo peñaranda','$2y$10$PprcAWUiflXNx7PWvt5jKe8b1efpTANoZS3uiClCBOrCnbSDapCaC','2024-10-24 08:46:37','activo','2024-10-24 08:48:29',0,NULL),(67,'Sinfonica',NULL,NULL,NULL,NULL,NULL,'michel.camilo.566@co.co','3222','',4,'michel.camilo.566','$argon2id$v=19$m=65536,t=4,p=1$RThQc3BZRUpxWGNUMkFCcA$irxitnqgibgqolUHW8tbl4Sizq+HMMHgdqyn/TRhhJo','2024-10-24 09:32:38','activo',NULL,0,NULL),(68,NULL,NULL,NULL,NULL,NULL,NULL,'lklkalaf@gmail.com',NULL,'',3,'camilokis','$2y$10$Q5FEcR8qnPvWGuP2Izc0y.si/Fs8bQ6VPbAJgOEY2d2qGuirOxGhq','2024-10-24 09:34:45','activo','2024-10-24 09:35:39',0,NULL),(69,NULL,NULL,NULL,NULL,NULL,NULL,'ldasdfklkalaf@gmail.com',NULL,'',3,'camilokisa','$2y$10$3eU8k37qwB5pYAWFwAm/xusZUeRQ/9KNQSrcc64wNkU5.mQxv9Ic2','2024-10-24 09:43:40','activo',NULL,0,NULL),(70,NULL,NULL,NULL,NULL,NULL,NULL,'santiagoca@gmail.comd',NULL,'',3,'camilokisalo','$2y$10$91rsTlSGxc3LUeGwuj6xu.HEb0bcoJzkKP3L3kBBYKT4OCO3n/iX.','2024-10-24 09:51:43','activo','2024-10-24 09:51:52',0,NULL);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

-- Tabla para historial de notificaciones de usuario
CREATE TABLE IF NOT EXISTS historial_notificaciones_user (
    id_historial INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_notificacion INT NOT NULL,
    fecha_vista TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    leido TINYINT(1) DEFAULT 0,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_notificacion) REFERENCES notificaciones_user(id_notificacion) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-10-30 20:32:02
