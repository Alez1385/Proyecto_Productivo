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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asig_modulo`
--

LOCK TABLES `asig_modulo` WRITE;
/*!40000 ALTER TABLE `asig_modulo` DISABLE KEYS */;
INSERT INTO `asig_modulo` VALUES (11,2,37,NULL),(12,2,2,NULL),(13,2,1,NULL),(14,3,1,NULL),(15,3,2,NULL),(17,5,1,NULL),(18,6,1,NULL),(19,7,1,NULL),(20,8,1,NULL),(21,3,3,NULL),(22,9,1,NULL),(23,10,1,NULL),(24,10,3,NULL),(25,11,1,NULL),(26,11,3,NULL);
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
  CONSTRAINT `asignacion_curso_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`),
  CONSTRAINT `asignacion_curso_ibfk_2` FOREIGN KEY (`id_profesor`) REFERENCES `profesor` (`id_profesor`),
  CONSTRAINT `asignacion_curso_ibfk_3` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asignacion_curso`
--

LOCK TABLES `asignacion_curso` WRITE;
/*!40000 ALTER TABLE `asignacion_curso` DISABLE KEYS */;
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
  PRIMARY KEY (`id_asistencia`),
  KEY `id_estudiante` (`id_estudiante`),
  KEY `id_curso` (`id_curso`),
  CONSTRAINT `asistencia_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`),
  CONSTRAINT `asistencia_ibfk_2` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistencia`
--

LOCK TABLES `asistencia` WRITE;
/*!40000 ALTER TABLE `asistencia` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carousel`
--

LOCK TABLES `carousel` WRITE;
/*!40000 ALTER TABLE `carousel` DISABLE KEYS */;
INSERT INTO `carousel` VALUES (40,'Curso De Natacion','El mejor curso del mundo sin palabras, me encanta cuando no hablas y te quedas mirando.','chica-sola-en-la-ciudad-ilustracion_3840x2160_xtrafondos.com.jpg',0,'2024-08-14','2024-08-20'),(42,'Curso De Natacion','El mejor curso del mundo sin palabras, me encanta cuando no hablas y te quedas mirando.','chica-sola-en-la-ciudad-ilustracion_3840x2160_xtrafondos.com.jpg',0,'2024-08-14','2024-08-20'),(43,'Curso De Natacion','El mejor curso del mundo sin palabras, me encanta cuando no hablas y te quedas mirando.','chica-sola-en-la-ciudad-ilustracion_3840x2160_xtrafondos.com.jpg',0,'2024-08-14','2024-08-20'),(47,'Curso de Aliexpress','Sabemos que esto es una cosa de locos.','jake-lofi-hora-de-aventura_3840x2160_xtrafondos.com.jpg',0,'2024-08-23','2024-08-21'),(48,'TECNICA RELLENO EN QUIZ','ESTAN DISFRUTANDO DE UNA ACTIVIDAD ACADEMICA','habitacion-lofi_3840x2160_xtrafondos.com.jpg',0,'2024-08-29','2024-09-07'),(49,'Sapo Hp','Perro Mk','pexels-anastasiya-gepp-654466-1462637.jpg',0,'2024-09-11','2024-09-18');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria_curso`
--

LOCK TABLES `categoria_curso` WRITE;
/*!40000 ALTER TABLE `categoria_curso` DISABLE KEYS */;
INSERT INTO `categoria_curso` VALUES (1,'Danza'),(2,'Ajedrez'),(3,'Pastoral');
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
  PRIMARY KEY (`id_curso`),
  KEY `fk_categoria` (`id_categoria`),
  CONSTRAINT `fk_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria_curso` (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cursos`
--

LOCK TABLES `cursos` WRITE;
/*!40000 ALTER TABLE `cursos` DISABLE KEYS */;
INSERT INTO `cursos` VALUES (1,'Danzas','Curso de danzas','primaria',3,'activo','icon_66df8b8983f491.65048104.jpg',1),(2,'Ajedrez','Curso de ajedrez','terciaria',3,'activo','icon_66df8ca659c689.92859720.jpg',2),(8,'Ajedrez','Curso de ajedrez para pequeños','primaria',3,'activo','icon_66df89588f54d8.68981697.jpg',2);
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
) ENGINE=InnoDB AUTO_INCREMENT=691 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_gescursoslecturas_mensajes`
--

LOCK TABLES `db_gescursoslecturas_mensajes` WRITE;
/*!40000 ALTER TABLE `db_gescursoslecturas_mensajes` DISABLE KEYS */;
INSERT INTO `db_gescursoslecturas_mensajes` VALUES (1,1,56,'2024-10-06 23:44:10'),(2,4,56,'2024-10-06 23:44:10'),(3,5,56,'2024-10-06 23:52:03'),(4,6,56,'2024-10-07 00:07:02'),(5,7,56,'2024-10-07 00:06:58'),(6,9,56,'2024-10-06 18:46:24'),(14,10,56,'2024-10-07 00:06:58'),(15,12,56,'2024-10-07 00:07:01'),(23,13,56,'2024-10-06 18:34:53'),(28,14,56,'2024-10-06 18:22:01'),(30,15,56,'2024-10-06 18:36:18'),(31,16,56,'2024-10-06 18:22:03'),(32,17,56,'2024-10-06 18:22:03'),(45,11,56,'2024-10-06 18:37:59'),(101,18,56,'2024-10-06 18:34:24'),(162,19,56,'2024-10-07 00:07:00'),(165,20,56,'2024-10-06 23:51:51'),(227,22,56,'2024-10-07 00:07:00'),(418,26,56,'2024-10-07 00:05:24'),(419,26,53,'2024-10-06 23:28:43'),(420,24,53,'2024-10-06 23:28:43'),(421,22,53,'2024-10-06 23:28:25'),(426,20,53,'2024-10-06 23:27:43'),(427,6,53,'2024-10-06 23:27:47'),(428,4,53,'2024-10-06 23:27:48'),(429,5,53,'2024-10-06 23:27:39'),(434,19,53,'2024-10-06 23:27:43'),(435,7,53,'2024-10-06 23:27:47'),(436,12,53,'2024-10-06 23:27:44'),(437,10,53,'2024-10-06 23:28:26'),(441,1,53,'2024-10-06 23:27:48'),(463,37,56,'2024-10-07 00:06:57'),(466,38,56,'2024-10-07 00:53:09'),(467,38,53,'2024-10-07 19:57:01'),(469,39,53,'2024-10-07 19:57:00'),(471,40,56,'2024-10-06 23:37:19'),(474,39,56,'2024-10-07 00:53:08'),(481,40,53,'2024-10-07 19:56:59'),(484,41,56,'2024-10-07 00:57:50'),(495,42,53,'2024-10-06 23:37:41'),(496,42,56,'2024-10-07 01:07:58'),(501,24,56,'2024-10-07 00:05:25'),(514,8,56,'2024-10-07 00:07:02'),(679,41,53,'2024-10-07 19:57:03'),(685,37,53,'2024-10-07 19:57:02'),(687,41,36,'2024-10-07 19:57:48'),(688,42,36,'2024-10-07 19:57:49');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estudiante`
--

LOCK TABLES `estudiante` WRITE;
/*!40000 ALTER TABLE `estudiante` DISABLE KEYS */;
INSERT INTO `estudiante` VALUES (1,53,'M','5234-05-31','activo','secundaria','Perro marica');
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
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_inscripciones`
--

LOCK TABLES `historial_inscripciones` WRITE;
/*!40000 ALTER TABLE `historial_inscripciones` DISABLE KEYS */;
INSERT INTO `historial_inscripciones` VALUES (75,37,'pendiente','aprobada','2024-10-07 20:58:14',36);
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
  `dia_semana` enum('lunes','martes','miércoles','jueves','viernes','sábado') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  PRIMARY KEY (`id_horario`),
  KEY `id_curso` (`id_curso`),
  CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horarios`
--

LOCK TABLES `horarios` WRITE;
/*!40000 ALTER TABLE `horarios` DISABLE KEYS */;
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
  `id_preinscripcion` int(11) DEFAULT NULL,
  `fecha_inscripcion` date DEFAULT NULL,
  `estado` enum('pendiente','aprobada','rechazada','cancelada') NOT NULL DEFAULT 'pendiente',
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `comprobante_pago` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_inscripcion`),
  KEY `id_curso` (`id_curso`),
  KEY `id_estudiante` (`id_estudiante`),
  KEY `idx_inscripciones_curso_estudiante` (`id_curso`,`id_estudiante`),
  KEY `inscripciones_ibfk_3` (`id_preinscripcion`),
  CONSTRAINT `inscripciones_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`),
  CONSTRAINT `inscripciones_ibfk_2` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`),
  CONSTRAINT `inscripciones_ibfk_3` FOREIGN KEY (`id_preinscripcion`) REFERENCES `preinscripciones` (`id_preinscripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inscripciones`
--

LOCK TABLES `inscripciones` WRITE;
/*!40000 ALTER TABLE `inscripciones` DISABLE KEYS */;
INSERT INTO `inscripciones` VALUES (37,2,1,34,'2024-10-07','aprobada','2024-10-07 20:58:14','../uploads/comprobantes/1728332197_Imagen de WhatsApp 2024-09-18 a las 08.41.25_cda83234.jpg');
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

LOCK TABLES `login_attempts` WRITE;
/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
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
  `tipo_destinatario` enum('todos','estudiantes','profesores','users','individual') DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensajes`
--

LOCK TABLES `mensajes` WRITE;
/*!40000 ALTER TABLE `mensajes` DISABLE KEYS */;
INSERT INTO `mensajes` VALUES (1,56,NULL,'todos',NULL,'0','asdasdasd','2024-10-06 11:34:57',NULL),(4,56,NULL,'todos',NULL,'asdas','dasd','2024-10-06 11:41:14',NULL),(5,56,NULL,'todos',NULL,'asdasd','ad','2024-10-06 11:43:44',NULL),(6,56,NULL,'todos',NULL,'dasd','asdasd','2024-10-06 11:50:20',NULL),(7,56,NULL,'todos',NULL,'asd','asdasd','2024-10-06 11:59:23',NULL),(8,56,NULL,'individual',36,'asdas','dsad','2024-10-06 12:01:55',NULL),(10,56,NULL,'todos',NULL,'asda','sdd','2024-10-06 12:19:25',NULL),(12,56,NULL,'todos',NULL,'asd','asd','2024-10-06 12:39:46',NULL),(19,56,NULL,'todos',NULL,'asd','asd','2024-10-06 13:59:41',NULL),(20,56,NULL,'todos',NULL,'asda','sd','2024-10-06 14:07:13',NULL),(22,56,NULL,'todos',NULL,'sapo','asdasdasasdasdasaasdasdasasdasdasasasasasasasasasasasasasasasasasasasasasdasdasasdasdasasasasasasasasasasasasasasasasasasasasasdasdasasdasdasasasasasasasasasasasasasasasasasasasasasdasdasasdasdasasasasasasasasasasasasasasasasasasasasasdasdasasdasdasasasasasasasasasasasasasasasasasasasasasdasdasasdasdasasasasasasasasasasasasasasasasasasasasasdasdasasdasdasasasasasasasasasasasasasasasasasasasasasdasdasasdasdasasasasasasasasasasasasasasasasasasasasasdasdasasdasdasasasasasasasasasasasasasasasasasasasasasdasdasasdasdasasasasasasasasasasasasasasasasasasasassasasasasasasasasasasasasasasasasasasasasasd','2024-10-06 17:24:17',NULL),(24,56,NULL,'todos',NULL,'asdasd','asd','2024-10-06 18:18:05',NULL),(26,56,NULL,'todos',NULL,'asd','asd','2024-10-06 18:19:47',NULL),(32,NULL,NULL,'estudiantes',NULL,'asdas','dasdasd','2024-10-06 18:24:21',3),(33,NULL,NULL,'estudiantes',NULL,'asdasd','asdsa','2024-10-06 18:24:30',3),(34,NULL,NULL,'estudiantes',NULL,'asd','asdasd','2024-10-06 18:28:53',3),(35,NULL,NULL,'todos',NULL,'asdad','asd','2024-10-06 18:29:00',NULL),(36,NULL,NULL,'todos',NULL,'asdad','asd','2024-10-06 18:29:07',NULL),(37,56,NULL,'todos',NULL,'asda','asdasd','2024-10-06 18:31:29',NULL),(38,56,NULL,'estudiantes',NULL,'asdasd','asd','2024-10-06 18:31:39',3),(39,53,NULL,'todos',NULL,'asdas','asdas','2024-10-06 18:32:17',NULL),(40,56,NULL,'todos',NULL,'asdasd','asdasd','2024-10-06 18:32:40',NULL),(41,53,NULL,'todos',NULL,'Perro marica quien lo lea ','JEJE de pana que malo si lo vio :v ya no se que hacer con mi vida\r\n','2024-10-06 18:34:48',NULL),(42,53,NULL,'todos',NULL,'asdas','dasda','2024-10-06 18:37:39',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensajes_eliminados`
--

LOCK TABLES `mensajes_eliminados` WRITE;
/*!40000 ALTER TABLE `mensajes_eliminados` DISABLE KEYS */;
INSERT INTO `mensajes_eliminados` VALUES (1,56,40,'2024-10-06 23:37:22'),(2,53,42,'2024-10-06 23:37:42');
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulos`
--

LOCK TABLES `modulos` WRITE;
/*!40000 ALTER TABLE `modulos` DISABLE KEYS */;
INSERT INTO `modulos` VALUES (2,'Profesor','../models/profesor/profesor.php','school',6),(3,'Estudiante','../models/estudiante/estudiante.php','face\r\n',7),(5,'Usuarios','../models/usuarios/users.php','person',1),(6,'Cursos','models/cursos/cursos.php','assignment',4),(7,'Modulos','models/modulos/modulos.php','event',8),(8,'Inscripciones','models/inscripciones/inscripciones.php','card_travel',5),(9,'Index','models/admin_index/admin_index.php','home',9),(10,'Mensajes','models/mensajeria/mensajeria.php','question_answer',3),(11,'Perfil','models/perfil/perfil.php','person',2);
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
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
INSERT INTO `password_resets` VALUES (46,'santiagocaponf@gmail.com','ab62cf32778dfe063a7afd8c87081bebcccd15900ac0ffbb98f56b4fee6c8aca','2024-10-02 19:32:34',36),(47,'santiagocaponf@gmail.com','3a43fab6750869f508850169d66ff43bdfb0ca059381c17a99ec2f5c3a24f0e8','2024-10-02 19:40:44',36),(48,'santiagocaponf@gmail.com','bcad622b7b016e3a263de308641be51c29abc94d02709f462882eb2b0e44bb10','2024-10-02 19:40:47',36);
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
  CONSTRAINT `preinscripciones_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preinscripciones`
--

LOCK TABLES `preinscripciones` WRITE;
/*!40000 ALTER TABLE `preinscripciones` DISABLE KEYS */;
INSERT INTO `preinscripciones` VALUES (34,2,'Juanito Alimaña','juanit@gmail.com','5233456345','2024-10-03 01:30:13','pendiente','a8ce2bd9dcd09a1ce82513e6b9610484',53),(35,8,'Juanito Alimaña','juanit@gmail.com','5233456345','2024-10-03 01:31:40','pendiente','e79afefdcf21ed88d8c6c39b591e1f88',53),(36,1,'Juanito Alimaña','juanit@gmail.com','5233456345','2024-10-03 01:33:02','pendiente','0a4d64b82e29e156e6730eb3bf5ede59',53);
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profesor`
--

LOCK TABLES `profesor` WRITE;
/*!40000 ALTER TABLE `profesor` DISABLE KEYS */;
INSERT INTO `profesor` VALUES (4,58,'perro marica',12,'El le gusto mucho el hecho de masturbarse en casa sapo marica'),(5,55,'Salpiconero',12,'Un pajiso total');
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resume_cursos`
--

LOCK TABLES `resume_cursos` WRITE;
/*!40000 ALTER TABLE `resume_cursos` DISABLE KEYS */;
INSERT INTO `resume_cursos` VALUES (4,'24','Curso para gerson','En la casa de gerson','Para mover la pampa, hay que ser personas de bien señores '),(5,'asdas','asdasdasd','asdasdasdas','dasdas'),(6,'33','Gersson','Su casa','Para bailar la bamba se necesita un poco de gracia');
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
INSERT INTO `user_module_order` VALUES (36,'[\"11\",\"2\",\"5\",\"10\",\"6\",\"8\",\"3\",\"7\",\"9\"]'),(53,'[\"3\",\"10\",\"11\"]');
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
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (36,'Santiago','Capon','ID','12341235234','2345-03-12','WhatsApp Image 2024-07-23 at 4.49.07 PM.jpeg','santiagocaponf@gmail.com','32452345','CL 18 A NORTE 2 72',1,'alez','$2y$10$64T2Qk8yptB8y8Rk6Kq26uhnbT3Ias.JH.EXcin2d1BPQCzAvHiM6','2024-08-25 17:43:54','activo','2024-10-02 18:35:00',0,NULL),(42,'chad','sexteto','ID','523456346','3654-04-23','66d273c33d474_Recurso 9europe.jpg','luisillo@gmail.com','4563475674','CL 18 A NORTE 2 72',1,'alez23','$2y$10$FrpZXvgI3WrL22y9MxNtfuQsyQSgCJ7Jm4VPUv3Aa4qEn2HCKdxsK','2024-08-29 16:26:44','activo',NULL,0,NULL),(51,'antonela','sepulveda','ID','342352345','0005-04-23','66d23c1021bab_f7c0528d915ec3b38dd89bf7beb2a194.jpg','scflorez@corsaje.edu.co','42352345','CL 18 A NORTE 2 72',1,'mientras','$2y$10$KJU2liHj854T1T9M.6/EK.xDYy4sfLf2XEwCldj230rdreZmC.3KC','2024-08-30 16:39:28','activo',NULL,0,NULL),(53,'Juanitoaa','Alimaña','ID','43523634','0634-06-02','66d2441faa705_pngwing.com.png','juanit@gmail.com','5233456345123','CL 18 A NORTE 2 72',3,'alez123123','$2y$10$p.bJhCL9d2VM1IjUCnC63.Edj5Pg87KZgKGTFyedUHPusUd.QSDAK','2024-08-30 17:13:51','activo',NULL,0,NULL),(55,'Santiago','Capon','Passport','4234523456','5234-04-23','pngwing.com.png','scflorez@corsaje.edu.co3','53643563456','CL 18 A NORTE 2 72',4,'alez1234','$2y$10$pcvzMHIh1F53bR25oEpRfu5MbZB5FO6Kn3ceIKwNBtp9KWahjApMe','2024-09-03 12:35:04','activo',NULL,0,NULL),(56,'camilo ','prato','ID','1091357317','2024-09-17','67032f8d169dd_images.png','albertocamiloprato@gmail.com','3043282464','Sapo Marica',1,'camilo','$2y$10$pkH8Zi8gEArSclW4KlpcjOm0Tbx5fSF2o8f7Ukw8qUNWj8Bl7i2I.','2024-09-07 18:45:23','activo','2024-09-08 11:32:39',0,NULL),(58,'santiago',NULL,NULL,NULL,NULL,NULL,'edison_alberto@hotmail.com','52343456','',4,'edison_alberto','$2y$10$DLJSPUZnsBduhl5PFRtg6uP5aXma0xTP9FOSkKUN/g2l9MrcP7d3S','2024-09-19 12:02:34','activo',NULL,0,NULL),(59,'Santiago',NULL,NULL,NULL,NULL,NULL,'scapon@misena.edu.co','3034235435','',4,'scapon','$2y$10$yrrLCg7Fr85s6u9jOmiVMO14UhXLMOrHN6krm2bL4fCNqnnCqc4Oy','2024-09-20 20:09:22','activo',NULL,0,NULL),(60,'Santiago',NULL,NULL,NULL,NULL,NULL,'albertocamiloprato@gmail.comw','563456346','',4,'albertocamiloprato','$2y$10$3u2mhrj1Ce6x8WCnljewFOuy20NifY7kKhXbWo0Y7NqPSXH89a9qe','2024-09-20 20:50:39','activo',NULL,0,NULL),(63,NULL,NULL,NULL,NULL,NULL,NULL,'santigao@gmail.com',NULL,'',1,'alez1233','$2y$10$DSX8990wWKG04J/82ENXo.xZAJyQn/flaX2ULl1gFLB3TuZKQpMZ6','2024-09-28 20:04:00','activo',NULL,0,NULL);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-10-07 18:36:45
