-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-10-2024 a las 23:30:34
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
-- Base de datos: `db_gescursos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignacion_curso`
--

CREATE TABLE `asignacion_curso` (
  `id_asignacion` int(11) NOT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `id_profesor` int(11) DEFAULT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `fecha_asignacion` date DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asignacion_curso`
--

INSERT INTO `asignacion_curso` (`id_asignacion`, `id_curso`, `id_profesor`, `id_estudiante`, `fecha_asignacion`, `comentarios`, `estado`) VALUES
(18, 1, 7, NULL, '2024-10-13', NULL, 'activo'),
(19, 2, 7, NULL, '2024-10-13', NULL, 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asig_modulo`
--

CREATE TABLE `asig_modulo` (
  `id_asig_modulo` int(11) NOT NULL,
  `id_modulo` int(11) DEFAULT NULL,
  `id_tipo_usuario` int(11) DEFAULT NULL,
  `fecha_asig` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asig_modulo`
--

INSERT INTO `asig_modulo` (`id_asig_modulo`, `id_modulo`, `id_tipo_usuario`, `fecha_asig`) VALUES
(11, 2, 37, NULL),
(13, 2, 1, NULL),
(14, 3, 1, NULL),
(17, 5, 1, NULL),
(18, 6, 1, NULL),
(19, 7, 1, NULL),
(20, 8, 1, NULL),
(22, 9, 1, NULL),
(23, 10, 1, NULL),
(24, 10, 3, NULL),
(25, 11, 1, NULL),
(26, 11, 3, NULL),
(27, 12, 1, NULL),
(28, 13, 3, NULL),
(29, 13, 2, NULL),
(30, 14, 2, NULL),
(31, 15, 3, NULL),
(32, 10, 2, NULL),
(33, 11, 2, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--

CREATE TABLE `asistencia` (
  `id_asistencia` int(11) NOT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `presente` enum('si','no','','') DEFAULT NULL,
  `justificacion` text DEFAULT NULL,
  `estado` enum('presente','ausente','retardo') NOT NULL DEFAULT 'ausente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asistencia`
--

INSERT INTO `asistencia` (`id_asistencia`, `id_estudiante`, `id_curso`, `fecha`, `presente`, `justificacion`, `estado`) VALUES
(4, 1, 1, '2024-10-13', 'si', NULL, 'ausente'),
(5, 1, 1, '2024-10-13', 'no', NULL, 'ausente'),
(6, 1, 1, '2024-10-13', NULL, NULL, 'presente'),
(7, 1, 1, '2024-12-04', NULL, NULL, 'ausente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carousel`
--

CREATE TABLE `carousel` (
  `id_carrousel` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `order_index` int(11) NOT NULL,
  `fecha_curso_inicio` date NOT NULL,
  `fecha_curso_fin` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carousel`
--

INSERT INTO `carousel` (`id_carrousel`, `title`, `description`, `image`, `order_index`, `fecha_curso_inicio`, `fecha_curso_fin`) VALUES
(40, 'Curso De Natacion', 'El mejor curso del mundo sin palabras, me encanta cuando no hablas y te quedas mirando.', 'chica-sola-en-la-ciudad-ilustracion_3840x2160_xtrafondos.com.jpg', 0, '2024-08-14', '2024-08-20'),
(42, 'Curso De Natacion', 'El mejor curso del mundo sin palabras, me encanta cuando no hablas y te quedas mirando.', 'chica-sola-en-la-ciudad-ilustracion_3840x2160_xtrafondos.com.jpg', 0, '2024-08-14', '2024-08-20'),
(43, 'Curso De Natacion', 'El mejor curso del mundo sin palabras, me encanta cuando no hablas y te quedas mirando.', 'chica-sola-en-la-ciudad-ilustracion_3840x2160_xtrafondos.com.jpg', 0, '2024-08-14', '2024-08-20'),
(47, 'Curso de Aliexpress', 'Sabemos que esto es una cosa de locos.', 'jake-lofi-hora-de-aventura_3840x2160_xtrafondos.com.jpg', 0, '2024-08-23', '2024-08-21'),
(48, 'TECNICA RELLENO EN QUIZ', 'ESTAN DISFRUTANDO DE UNA ACTIVIDAD ACADEMICA', 'habitacion-lofi_3840x2160_xtrafondos.com.jpg', 0, '2024-08-29', '2024-09-07'),
(49, 'Sapo Hp', 'Perro Mk', 'pexels-anastasiya-gepp-654466-1462637.jpg', 0, '2024-09-11', '2024-09-18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria_curso`
--

CREATE TABLE `categoria_curso` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria_curso`
--

INSERT INTO `categoria_curso` (`id_categoria`, `nombre_categoria`) VALUES
(1, 'Danza'),
(2, 'Ajedrez'),
(3, 'Pastoral');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id_curso` int(11) NOT NULL,
  `nombre_curso` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `nivel_educativo` enum('primaria','secundaria','terciaria') NOT NULL,
  `duracion` int(3) NOT NULL COMMENT 'Duración en semanas',
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `icono` varchar(255) DEFAULT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `id_profesor` int(11) DEFAULT NULL,
  `horarios` text DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id_curso`, `nombre_curso`, `descripcion`, `nivel_educativo`, `duracion`, `estado`, `icono`, `id_categoria`, `id_profesor`, `horarios`, `id_usuario`) VALUES
(1, 'Danzas', 'Curso de danzas', 'primaria', 3, 'activo', 'icon_66df8b8983f491.65048104.jpg', 1, NULL, NULL, NULL),
(2, 'Ajedrez', 'Curso de ajedrez', 'terciaria', 3, 'activo', 'icon_66df8ca659c689.92859720.jpg', 2, NULL, NULL, NULL),
(8, 'Ajedrez', 'Curso de ajedrez para pequeños', 'primaria', 3, 'activo', 'icon_66df89588f54d8.68981697.jpg', 2, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `db_gescursoslecturas_mensajes`
--

CREATE TABLE `db_gescursoslecturas_mensajes` (
  `id` int(11) NOT NULL,
  `id_mensaje` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_lectura` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `db_gescursoslecturas_mensajes`
--

INSERT INTO `db_gescursoslecturas_mensajes` (`id`, `id_mensaje`, `id_usuario`, `fecha_lectura`) VALUES
(1, 1, 56, '2024-10-06 23:44:10'),
(2, 4, 56, '2024-10-09 19:16:08'),
(3, 5, 56, '2024-10-09 19:16:08'),
(4, 6, 56, '2024-10-09 19:16:07'),
(5, 7, 56, '2024-10-09 19:16:06'),
(6, 9, 56, '2024-10-06 18:46:24'),
(14, 10, 56, '2024-10-07 00:06:58'),
(15, 12, 56, '2024-10-07 00:07:01'),
(23, 13, 56, '2024-10-06 18:34:53'),
(28, 14, 56, '2024-10-06 18:22:01'),
(30, 15, 56, '2024-10-06 18:36:18'),
(31, 16, 56, '2024-10-06 18:22:03'),
(32, 17, 56, '2024-10-06 18:22:03'),
(45, 11, 56, '2024-10-06 18:37:59'),
(101, 18, 56, '2024-10-06 18:34:24'),
(162, 19, 56, '2024-10-07 00:07:00'),
(165, 20, 56, '2024-10-06 23:51:51'),
(227, 22, 56, '2024-10-07 00:07:00'),
(418, 26, 56, '2024-10-07 00:05:24'),
(419, 26, 53, '2024-10-06 23:28:43'),
(420, 24, 53, '2024-10-06 23:28:43'),
(421, 22, 53, '2024-10-06 23:28:25'),
(426, 20, 53, '2024-10-06 23:27:43'),
(427, 6, 53, '2024-10-06 23:27:47'),
(428, 4, 53, '2024-10-06 23:27:48'),
(429, 5, 53, '2024-10-06 23:27:39'),
(434, 19, 53, '2024-10-06 23:27:43'),
(435, 7, 53, '2024-10-06 23:27:47'),
(436, 12, 53, '2024-10-06 23:27:44'),
(437, 10, 53, '2024-10-06 23:28:26'),
(441, 1, 53, '2024-10-06 23:27:48'),
(463, 37, 56, '2024-10-09 19:16:05'),
(466, 38, 56, '2024-10-09 19:16:04'),
(467, 38, 53, '2024-10-08 04:36:41'),
(469, 39, 53, '2024-10-08 19:56:46'),
(471, 40, 56, '2024-10-06 23:37:19'),
(474, 39, 56, '2024-10-09 19:16:04'),
(481, 40, 53, '2024-10-08 19:56:47'),
(484, 41, 56, '2024-10-09 19:16:03'),
(495, 42, 53, '2024-10-06 23:37:41'),
(496, 42, 56, '2024-10-09 19:16:09'),
(501, 24, 56, '2024-10-07 00:05:25'),
(514, 8, 56, '2024-10-09 19:16:07'),
(679, 41, 53, '2024-10-08 19:56:47'),
(685, 37, 53, '2024-10-07 19:57:02'),
(687, 41, 36, '2024-10-07 19:57:48'),
(688, 42, 36, '2024-10-07 19:57:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiante`
--

CREATE TABLE `estudiante` (
  `id_estudiante` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `genero` varchar(10) DEFAULT NULL,
  `fecha_registro` date DEFAULT curdate(),
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `nivel_educativo` varchar(50) DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estudiante`
--

INSERT INTO `estudiante` (`id_estudiante`, `id_usuario`, `genero`, `fecha_registro`, `estado`, `nivel_educativo`, `observaciones`) VALUES
(1, 53, 'M', '5234-05-31', 'activo', 'secundaria', 'Perro marica');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_inscripciones`
--

CREATE TABLE `historial_inscripciones` (
  `id_historial` int(11) NOT NULL,
  `id_inscripcion` int(11) DEFAULT NULL,
  `estado_anterior` enum('pendiente','aprobada','rechazada','cancelada') DEFAULT NULL,
  `estado_nuevo` enum('pendiente','aprobada','rechazada','cancelada') DEFAULT NULL,
  `fecha_cambio` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_usuario_cambio` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_inscripciones`
--

INSERT INTO `historial_inscripciones` (`id_historial`, `id_inscripcion`, `estado_anterior`, `estado_nuevo`, `fecha_cambio`, `id_usuario_cambio`) VALUES
(78, 53, 'pendiente', 'aprobada', '2024-10-13 16:34:30', 56),
(79, 50, 'pendiente', 'aprobada', '2024-10-13 16:34:31', 56),
(80, 51, 'pendiente', 'aprobada', '2024-10-13 16:34:32', 56),
(81, 52, 'pendiente', 'aprobada', '2024-10-13 16:34:33', 56),
(82, 54, 'pendiente', 'aprobada', '2024-10-13 17:01:12', 56);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `id_horario` int(11) NOT NULL,
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
  `hora_fin` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `horarios`
--

INSERT INTO `horarios` (`id_horario`, `id_curso`, `id_profesor`, `fecha_creacion`, `dia_semana`, `lunes`, `martes`, `miercoles`, `jueves`, `viernes`, `sabado`, `hora_inicio`, `hora_fin`) VALUES
(26, 1, 5, '2024-10-08 06:07:46', 'lunes', '08:08 - 09:09', '12:02 - 17:05', '09:10 - 10:10', NULL, NULL, NULL, NULL, NULL),
(27, 2, 7, '2024-10-13 20:49:41', 'lunes', '08:08 - 09:10', '10:00 - 16:50', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--

CREATE TABLE `inscripciones` (
  `id_inscripcion` int(11) NOT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `fecha_inscripcion` date DEFAULT NULL,
  `estado` enum('pendiente','aprobada','rechazada','cancelada') NOT NULL DEFAULT 'pendiente',
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `comprobante_pago` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inscripciones`
--

INSERT INTO `inscripciones` (`id_inscripcion`, `id_curso`, `id_estudiante`, `fecha_inscripcion`, `estado`, `fecha_actualizacion`, `comprobante_pago`) VALUES
(50, 1, 1, '2024-10-13', 'aprobada', '2024-10-13 16:34:31', '../../uploads/comprobantes/1728837250_fondos-de-pantalla-3d-paisaje.jpg'),
(51, 1, 1, '2024-10-13', 'aprobada', '2024-10-13 16:34:32', '../../uploads/comprobantes/1728837250_fondos-de-pantalla-3d-paisaje.jpg'),
(52, 2, 1, '2024-10-13', 'aprobada', '2024-10-13 16:34:33', '../../uploads/comprobantes/1728837253_fondos-de-pantalla-3d-paisaje.jpg'),
(53, 2, 1, '2024-10-13', 'aprobada', '2024-10-13 16:34:30', '../../uploads/comprobantes/1728837253_fondos-de-pantalla-3d-paisaje.jpg'),
(54, 8, 1, '2024-10-13', 'aprobada', '2024-10-13 17:01:12', '../uploads/comprobantes/1728838857_fondos-de-pantalla-3d-paisaje.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id_mensaje` int(11) NOT NULL,
  `id_remitente` int(11) DEFAULT NULL,
  `tipo_remitente` int(11) DEFAULT NULL,
  `tipo_destinatario` enum('todos','estudiantes','profesores','users','individual') DEFAULT NULL,
  `id_destinatario` int(11) DEFAULT NULL,
  `asunto` varchar(255) DEFAULT NULL,
  `contenido` text DEFAULT NULL,
  `fecha_envio` datetime DEFAULT current_timestamp(),
  `id_tipo_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id_mensaje`, `id_remitente`, `tipo_remitente`, `tipo_destinatario`, `id_destinatario`, `asunto`, `contenido`, `fecha_envio`, `id_tipo_usuario`) VALUES
(1, 56, NULL, 'todos', NULL, '0', 'asdasdasd', '2024-10-06 11:34:57', NULL),
(4, 56, NULL, 'todos', NULL, 'asdas', 'dasd', '2024-10-06 11:41:14', NULL),
(5, 56, NULL, 'todos', NULL, 'asdasd', 'ad', '2024-10-06 11:43:44', NULL),
(6, 56, NULL, 'todos', NULL, 'dasd', 'asdasd', '2024-10-06 11:50:20', NULL),
(7, 56, NULL, 'todos', NULL, 'asd', 'asdasd', '2024-10-06 11:59:23', NULL),
(8, 56, NULL, 'individual', 36, 'asdas', 'dsad', '2024-10-06 12:01:55', NULL),
(10, 56, NULL, 'todos', NULL, 'asda', 'sdd', '2024-10-06 12:19:25', NULL),
(12, 56, NULL, 'todos', NULL, 'asd', 'asd', '2024-10-06 12:39:46', NULL),
(19, 56, NULL, 'todos', NULL, 'asd', 'asd', '2024-10-06 13:59:41', NULL),
(20, 56, NULL, 'todos', NULL, 'asda', 'sd', '2024-10-06 14:07:13', NULL),
(22, 56, NULL, 'todos', NULL, 'sapo', 'asdasdasasdasdasaasdasdasasdasdasasasasasasasasasasasasasasasasasasasasasdasdasasdasdasasasasasasasasasasasasasasasasasasasasasdasdasasdasdasasasasasasasasasasasasasasasasasasasasasdasdasasdasdasasasasasasasasasasasasasasasasasasasasasdasdasasdasdasasasasasasasasasasasasasasasasasasasasasdasdasasdasdasasasasasasasasasasasasasasasasasasasasasdasdasasdasdasasasasasasasasasasasasasasasasasasasasasdasdasasdasdasasasasasasasasasasasasasasasasasasasasasdasdasasdasdasasasasasasasasasasasasasasasasasasasasasdasdasasdasdasasasasasasasasasasasasasasasasasasasassasasasasasasasasasasasasasasasasasasasasasd', '2024-10-06 17:24:17', NULL),
(24, 56, NULL, 'todos', NULL, 'asdasd', 'asd', '2024-10-06 18:18:05', NULL),
(26, 56, NULL, 'todos', NULL, 'asd', 'asd', '2024-10-06 18:19:47', NULL),
(32, NULL, NULL, 'estudiantes', NULL, 'asdas', 'dasdasd', '2024-10-06 18:24:21', 3),
(33, NULL, NULL, 'estudiantes', NULL, 'asdasd', 'asdsa', '2024-10-06 18:24:30', 3),
(34, NULL, NULL, 'estudiantes', NULL, 'asd', 'asdasd', '2024-10-06 18:28:53', 3),
(35, NULL, NULL, 'todos', NULL, 'asdad', 'asd', '2024-10-06 18:29:00', NULL),
(36, NULL, NULL, 'todos', NULL, 'asdad', 'asd', '2024-10-06 18:29:07', NULL),
(37, 56, NULL, 'todos', NULL, 'asda', 'asdasd', '2024-10-06 18:31:29', NULL),
(38, 56, NULL, 'estudiantes', NULL, 'asdasd', 'asd', '2024-10-06 18:31:39', 3),
(39, 53, NULL, 'todos', NULL, 'asdas', 'asdas', '2024-10-06 18:32:17', NULL),
(40, 56, NULL, 'todos', NULL, 'asdasd', 'asdasd', '2024-10-06 18:32:40', NULL),
(41, 53, NULL, 'todos', NULL, 'Perro marica quien lo lea ', 'JEJE de pana que malo si lo vio :v ya no se que hacer con mi vida\r\n', '2024-10-06 18:34:48', NULL),
(42, 53, NULL, 'todos', NULL, 'asdas', 'dasda', '2024-10-06 18:37:39', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes_eliminados`
--

CREATE TABLE `mensajes_eliminados` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_mensaje` int(11) NOT NULL,
  `fecha_eliminacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes_eliminados`
--

INSERT INTO `mensajes_eliminados` (`id`, `id_usuario`, `id_mensaje`, `fecha_eliminacion`) VALUES
(1, 56, 40, '2024-10-06 23:37:22'),
(2, 53, 42, '2024-10-06 23:37:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id_modulo` int(11) NOT NULL,
  `nom_modulo` varchar(30) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `icono` varchar(255) NOT NULL,
  `orden` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id_modulo`, `nom_modulo`, `url`, `icono`, `orden`) VALUES
(2, 'Profesor', '../models/profesor/profesor.php', 'school', 6),
(3, 'Estudiante', '../models/estudiante/estudiante.php', 'face\r\n', 7),
(5, 'Usuarios', '../models/usuarios/users.php', 'person', 1),
(6, 'Cursos', 'models/cursos/cursos.php', 'assignment', 4),
(7, 'Modulos', 'models/modulos/modulos.php', 'event', 8),
(8, 'Inscripciones', 'models/inscripciones/inscripciones.php', 'card_travel', 5),
(9, 'Index', 'models/admin_index/admin_index.php', 'home', 9),
(10, 'Mensajes', 'models/mensajeria/mensajeria.php', 'question_answer', 3),
(11, 'Perfil', 'models/perfil/perfil.php', 'person', 2),
(12, 'Asig_Horario', 'models/horario/horarios_asignados.php', 'event', NULL),
(13, 'Horario', 'models/horario/cursos_listado.php', 'event', NULL),
(14, 'Asistencia', 'models/asistencia/asistencia.php', 'assignment_turned_in', NULL),
(15, 'Mi Asistencia', 'models/asistencia/asistencia_estudiante.php', 'assignment_turned_in', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id_pago` int(11) NOT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `fecha_pago` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id_pago`, `id_estudiante`, `monto`, `fecha_pago`) VALUES
(2, 1, 12000.00, '2024-08-30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `created_at`, `id_usuario`) VALUES
(46, 'santiagocaponf@gmail.com', 'ab62cf32778dfe063a7afd8c87081bebcccd15900ac0ffbb98f56b4fee6c8aca', '2024-10-02 19:32:34', 36),
(47, 'santiagocaponf@gmail.com', '3a43fab6750869f508850169d66ff43bdfb0ca059381c17a99ec2f5c3a24f0e8', '2024-10-02 19:40:44', 36),
(48, 'santiagocaponf@gmail.com', 'bcad622b7b016e3a263de308641be51c29abc94d02709f462882eb2b0e44bb10', '2024-10-02 19:40:47', 36);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preinscripciones`
--

CREATE TABLE `preinscripciones` (
  `id_preinscripcion` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `fecha_preinscripcion` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','completada','cancelada') NOT NULL DEFAULT 'pendiente',
  `token` varchar(255) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `preinscripciones`
--

INSERT INTO `preinscripciones` (`id_preinscripcion`, `id_curso`, `nombre`, `email`, `telefono`, `fecha_preinscripcion`, `estado`, `token`, `id_usuario`) VALUES
(46, 2, 'camilo  prato', 'albertocamiloprato@gmail.com', '3043282464', '2024-10-13 21:28:44', 'pendiente', '1a8014822dcc49abfb76578342002b11', 56);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor`
--

CREATE TABLE `profesor` (
  `id_profesor` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `especialidad` varchar(255) DEFAULT NULL,
  `experiencia` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profesor`
--

INSERT INTO `profesor` (`id_profesor`, `id_usuario`, `especialidad`, `experiencia`, `descripcion`) VALUES
(4, 58, 'perro marica', 12, 'El le gusto mucho el hecho de masturbarse en casa sapo marica'),
(5, 55, 'Salpiconero', 12, 'Un pajiso total'),
(7, 64, 'Artes', 12, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `resume_cursos`
--

CREATE TABLE `resume_cursos` (
  `id` int(11) NOT NULL,
  `dia` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `lugar` varchar(255) NOT NULL,
  `descripcion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `resume_cursos`
--

INSERT INTO `resume_cursos` (`id`, `dia`, `nombre`, `lugar`, `descripcion`) VALUES
(4, '24', 'Curso para gerson', 'En la casa de gerson', 'Para mover la pampa, hay que ser personas de bien señores '),
(5, 'asdas', 'asdasdasd', 'asdasdasdas', 'dasdas'),
(6, '33', 'Gersson', 'Su casa', 'Para bailar la bamba se necesita un poco de gracia');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_usuario`
--

CREATE TABLE `tipo_usuario` (
  `id_tipo_usuario` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_usuario`
--

INSERT INTO `tipo_usuario` (`id_tipo_usuario`, `nombre`) VALUES
(1, 'admin'),
(2, 'profesor'),
(3, 'estudiante'),
(4, 'user');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_module_order`
--

CREATE TABLE `user_module_order` (
  `id_usuario` int(11) NOT NULL,
  `module_order` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`module_order`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user_module_order`
--

INSERT INTO `user_module_order` (`id_usuario`, `module_order`) VALUES
(36, '[\"8\",\"12\",\"7\",\"2\",\"3\",\"5\",\"6\",\"10\",\"11\",\"9\"]'),
(53, '[\"13\",\"3\",\"10\",\"15\",\"11\"]'),
(56, '[\"12\",\"5\",\"11\",\"10\",\"7\",\"6\",\"8\",\"2\",\"3\",\"9\"]'),
(64, '[\"13\",\"14\",\"10\",\"2\",\"11\",\"3\"]');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
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
  `lock_timestamp` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellido`, `tipo_doc`, `documento`, `fecha_nac`, `foto`, `mail`, `telefono`, `direccion`, `id_tipo_usuario`, `username`, `clave`, `fecha_registro`, `estado`, `ultimo_acceso`, `is_locked`, `lock_timestamp`) VALUES
(36, 'Santiago', 'Capon', 'ID', '12341235234', '2345-03-12', 'WhatsApp Image 2024-07-23 at 4.49.07 PM.jpeg', 'santiagocaponf@gmail.com', '32452345', 'CL 18 A NORTE 2 72', 1, 'alez', '$2y$10$64T2Qk8yptB8y8Rk6Kq26uhnbT3Ias.JH.EXcin2d1BPQCzAvHiM6', '2024-08-25 17:43:54', 'activo', '2024-10-08 16:12:59', 0, NULL),
(42, 'chad', 'sexteto', 'ID', '523456346', '3654-04-23', '66d273c33d474_Recurso 9europe.jpg', 'luisillo@gmail.com', '4563475674', 'CL 18 A NORTE 2 72', 1, 'alez23', '$2y$10$FrpZXvgI3WrL22y9MxNtfuQsyQSgCJ7Jm4VPUv3Aa4qEn2HCKdxsK', '2024-08-29 16:26:44', 'activo', NULL, 0, NULL),
(51, 'antonela', 'sepulveda', 'ID', '342352345', '0005-04-23', '66d23c1021bab_f7c0528d915ec3b38dd89bf7beb2a194.jpg', 'scflorez@corsaje.edu.co', '42352345', 'CL 18 A NORTE 2 72', 1, 'mientras', '$2y$10$KJU2liHj854T1T9M.6/EK.xDYy4sfLf2XEwCldj230rdreZmC.3KC', '2024-08-30 16:39:28', 'activo', NULL, 0, NULL),
(53, 'Juanito', 'Alimaña', 'ID', '43523634', '0634-06-02', '66d2441faa705_pngwing.com.png', 'juanit@gmail.com', '5233456345123', 'CL 18 A NORTE 2 72', 3, 'alez123123', '$2y$10$p.bJhCL9d2VM1IjUCnC63.Edj5Pg87KZgKGTFyedUHPusUd.QSDAK', '2024-08-30 17:13:51', 'activo', '2024-10-13 14:38:47', 0, NULL),
(55, 'Santiago', 'Capon', 'Passport', '4234523456', '5234-04-23', 'pngwing.com.png', 'scflorez@corsaje.edu.co3', '53643563456', 'CL 18 A NORTE 2 72', 4, 'alez1234', '$2y$10$pcvzMHIh1F53bR25oEpRfu5MbZB5FO6Kn3ceIKwNBtp9KWahjApMe', '2024-09-03 12:35:04', 'activo', NULL, 0, NULL),
(56, 'camilo ', 'prato', 'ID', '1091357317', '2024-09-17', '67032f8d169dd_images.png', 'albertocamiloprato@gmail.com', '3043282464', 'Sapo Marica', 1, 'camilo', '$2y$10$pkH8Zi8gEArSclW4KlpcjOm0Tbx5fSF2o8f7Ukw8qUNWj8Bl7i2I.', '2024-09-07 18:45:23', 'activo', '2024-10-13 16:28:47', 0, NULL),
(58, 'santiago', NULL, NULL, NULL, NULL, NULL, 'edison_alberto@hotmail.com', '52343456', '', 4, 'edison_alberto', '$2y$10$DLJSPUZnsBduhl5PFRtg6uP5aXma0xTP9FOSkKUN/g2l9MrcP7d3S', '2024-09-19 12:02:34', 'activo', NULL, 0, NULL),
(59, 'Santiago', NULL, NULL, NULL, NULL, NULL, 'scapon@misena.edu.co', '3034235435', '', 4, 'scapon', '$2y$10$yrrLCg7Fr85s6u9jOmiVMO14UhXLMOrHN6krm2bL4fCNqnnCqc4Oy', '2024-09-20 20:09:22', 'activo', NULL, 0, NULL),
(60, 'Santiago', NULL, NULL, NULL, NULL, NULL, 'albertocamiloprato@gmail.comw', '563456346', '', 4, 'albertocamiloprato', '$2y$10$3u2mhrj1Ce6x8WCnljewFOuy20NifY7kKhXbWo0Y7NqPSXH89a9qe', '2024-09-20 20:50:39', 'activo', NULL, 0, NULL),
(63, NULL, NULL, NULL, NULL, NULL, NULL, 'santigao@gmail.com', NULL, '', 1, 'alez1233', '$2y$10$DSX8990wWKG04J/82ENXo.xZAJyQn/flaX2ULl1gFLB3TuZKQpMZ6', '2024-09-28 20:04:00', 'activo', NULL, 0, NULL),
(64, 'camilo', 'prato profe', 'ID', '13450735', '2000-08-14', '670c345acc2a8_fondos-de-pantalla-3d-paisaje.jpg', 'camiloprato234@gmail.com', '3043282464', 'Brr Atalaya', 2, 'camilop', '$2y$10$1Djh88ty26viA.IG41s4oOFrO5NU.mrAiw.6b3VnEVTPDj0qKeg2q', '2024-10-13 09:06:40', 'activo', '2024-10-13 15:18:07', 0, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asignacion_curso`
--
ALTER TABLE `asignacion_curso`
  ADD PRIMARY KEY (`id_asignacion`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_profesor` (`id_profesor`),
  ADD KEY `id_estudiante` (`id_estudiante`);

--
-- Indices de la tabla `asig_modulo`
--
ALTER TABLE `asig_modulo`
  ADD PRIMARY KEY (`id_asig_modulo`),
  ADD KEY `id_modulo` (`id_modulo`),
  ADD KEY `id_usuario` (`id_tipo_usuario`);

--
-- Indices de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD PRIMARY KEY (`id_asistencia`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `id_curso` (`id_curso`);

--
-- Indices de la tabla `carousel`
--
ALTER TABLE `carousel`
  ADD PRIMARY KEY (`id_carrousel`);

--
-- Indices de la tabla `categoria_curso`
--
ALTER TABLE `categoria_curso`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id_curso`),
  ADD KEY `fk_categoria` (`id_categoria`),
  ADD KEY `id_profesor` (`id_profesor`);

--
-- Indices de la tabla `db_gescursoslecturas_mensajes`
--
ALTER TABLE `db_gescursoslecturas_mensajes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_mensaje` (`id_mensaje`,`id_usuario`);

--
-- Indices de la tabla `estudiante`
--
ALTER TABLE `estudiante`
  ADD PRIMARY KEY (`id_estudiante`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `historial_inscripciones`
--
ALTER TABLE `historial_inscripciones`
  ADD PRIMARY KEY (`id_historial`),
  ADD KEY `id_inscripcion` (`id_inscripcion`),
  ADD KEY `historial_inscripciones_ibfk_2` (`id_usuario_cambio`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_profesor` (`id_profesor`);

--
-- Indices de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD PRIMARY KEY (`id_inscripcion`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `idx_inscripciones_curso_estudiante` (`id_curso`,`id_estudiante`);

--
-- Indices de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id_mensaje`),
  ADD KEY `id_remitente` (`id_remitente`),
  ADD KEY `id_destinatario` (`id_destinatario`);

--
-- Indices de la tabla `mensajes_eliminados`
--
ALTER TABLE `mensajes_eliminados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario_mensaje` (`id_usuario`,`id_mensaje`),
  ADD KEY `id_mensaje` (`id_mensaje`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id_modulo`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `id_estudiante` (`id_estudiante`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_password_resets_user_id` (`id_usuario`),
  ADD KEY `idx_password_resets_token` (`token`);

--
-- Indices de la tabla `preinscripciones`
--
ALTER TABLE `preinscripciones`
  ADD PRIMARY KEY (`id_preinscripcion`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `fk_preinscripciones_usuario` (`id_usuario`);

--
-- Indices de la tabla `profesor`
--
ALTER TABLE `profesor`
  ADD PRIMARY KEY (`id_profesor`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_token` (`id_usuario`,`token`);

--
-- Indices de la tabla `resume_cursos`
--
ALTER TABLE `resume_cursos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipo_usuario`
--
ALTER TABLE `tipo_usuario`
  ADD PRIMARY KEY (`id_tipo_usuario`);

--
-- Indices de la tabla `user_module_order`
--
ALTER TABLE `user_module_order`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_tipo_usuario` (`id_tipo_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asignacion_curso`
--
ALTER TABLE `asignacion_curso`
  MODIFY `id_asignacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `asig_modulo`
--
ALTER TABLE `asig_modulo`
  MODIFY `id_asig_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `carousel`
--
ALTER TABLE `carousel`
  MODIFY `id_carrousel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `categoria_curso`
--
ALTER TABLE `categoria_curso`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `db_gescursoslecturas_mensajes`
--
ALTER TABLE `db_gescursoslecturas_mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=732;

--
-- AUTO_INCREMENT de la tabla `estudiante`
--
ALTER TABLE `estudiante`
  MODIFY `id_estudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `historial_inscripciones`
--
ALTER TABLE `historial_inscripciones`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `id_inscripcion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id_mensaje` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `mensajes_eliminados`
--
ALTER TABLE `mensajes_eliminados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de la tabla `preinscripciones`
--
ALTER TABLE `preinscripciones`
  MODIFY `id_preinscripcion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `profesor`
--
ALTER TABLE `profesor`
  MODIFY `id_profesor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `resume_cursos`
--
ALTER TABLE `resume_cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tipo_usuario`
--
ALTER TABLE `tipo_usuario`
  MODIFY `id_tipo_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asignacion_curso`
--
ALTER TABLE `asignacion_curso`
  ADD CONSTRAINT `asignacion_curso_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`),
  ADD CONSTRAINT `asignacion_curso_ibfk_2` FOREIGN KEY (`id_profesor`) REFERENCES `profesor` (`id_profesor`),
  ADD CONSTRAINT `asignacion_curso_ibfk_3` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`);

--
-- Filtros para la tabla `asig_modulo`
--
ALTER TABLE `asig_modulo`
  ADD CONSTRAINT `asig_modulo_ibfk_1` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE;

--
-- Filtros para la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD CONSTRAINT `asistencia_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`),
  ADD CONSTRAINT `asistencia_ibfk_2` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`);

--
-- Filtros para la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD CONSTRAINT `cursos_ibfk_1` FOREIGN KEY (`id_profesor`) REFERENCES `profesor` (`id_profesor`),
  ADD CONSTRAINT `fk_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria_curso` (`id_categoria`);

--
-- Filtros para la tabla `estudiante`
--
ALTER TABLE `estudiante`
  ADD CONSTRAINT `fk_estudiante_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `historial_inscripciones`
--
ALTER TABLE `historial_inscripciones`
  ADD CONSTRAINT `historial_inscripciones_ibfk_1` FOREIGN KEY (`id_inscripcion`) REFERENCES `inscripciones` (`id_inscripcion`),
  ADD CONSTRAINT `historial_inscripciones_ibfk_2` FOREIGN KEY (`id_usuario_cambio`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`) ON DELETE CASCADE,
  ADD CONSTRAINT `horarios_ibfk_2` FOREIGN KEY (`id_profesor`) REFERENCES `profesor` (`id_profesor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD CONSTRAINT `inscripciones_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`),
  ADD CONSTRAINT `inscripciones_ibfk_2` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`);

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`id_remitente`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`id_destinatario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `mensajes_eliminados`
--
ALTER TABLE `mensajes_eliminados`
  ADD CONSTRAINT `mensajes_eliminados_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `mensajes_eliminados_ibfk_2` FOREIGN KEY (`id_mensaje`) REFERENCES `mensajes` (`id_mensaje`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`);

--
-- Filtros para la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_password_resets_user` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `preinscripciones`
--
ALTER TABLE `preinscripciones`
  ADD CONSTRAINT `fk_preinscripciones_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `preinscripciones_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`);

--
-- Filtros para la tabla `profesor`
--
ALTER TABLE `profesor`
  ADD CONSTRAINT `profesor_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `user_module_order`
--
ALTER TABLE `user_module_order`
  ADD CONSTRAINT `user_module_order_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_tipo_usuario`) REFERENCES `tipo_usuario` (`id_tipo_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
