-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-09-2024 a las 22:36:57
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
(12, 2, 2, NULL),
(13, 2, 1, NULL),
(14, 3, 1, NULL),
(15, 3, 2, NULL),
(17, 5, 1, NULL),
(18, 6, 1, NULL),
(19, 7, 1, NULL),
(20, 8, 1, NULL),
(21, 3, 3, NULL);

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
  `justificacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `id_categoria` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id_curso`, `nombre_curso`, `descripcion`, `nivel_educativo`, `duracion`, `estado`, `icono`, `id_categoria`) VALUES
(1, 'Danzas', 'Curso de danzas', 'primaria', 3, 'activo', 'icon_66df8b8983f491.65048104.jpg', 1),
(2, 'Ajedrez', 'Curso de ajedrez', 'terciaria', 3, 'activo', 'icon_66df8ca659c689.92859720.jpg', 2),
(8, 'Ajedrez', 'Curso de ajedrez para pequeños', 'primaria', 3, 'activo', 'icon_66df89588f54d8.68981697.jpg', 2);

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
(1, 53, 'M', '5234-05-31', 'activo', 'secundaria', '');

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `id_horario` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `dia_semana` enum('lunes','martes','miércoles','jueves','viernes','sábado') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--

CREATE TABLE `inscripciones` (
  `id_inscripcion` int(11) NOT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `id_preinscripcion` int(11) DEFAULT NULL,
  `fecha_inscripcion` date DEFAULT NULL,
  `estado` enum('pendiente','aprobada','rechazada','cancelada') NOT NULL DEFAULT 'pendiente',
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `comprobante_pago` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `ip`, `timestamp`) VALUES
(1, '127.0.0.1', '2024-09-29 15:21:03'),
(2, '127.0.0.1', '2024-09-29 15:21:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id_modulo` int(11) NOT NULL,
  `nom_modulo` varchar(30) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `icono` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id_modulo`, `nom_modulo`, `url`, `icono`) VALUES
(2, 'Profesor', '../models/profesor/profesor.php', 'school'),
(3, 'Estudiante', '../models/estudiante/estudiante.php', 'face\r\n'),
(5, 'Usuarios', '../models/usuarios/users.php', 'person'),
(6, 'Cursos', 'models/cursos/cursos.php', 'assignment'),
(7, 'Modulos', 'models/modulos/modulos.php', 'event'),
(8, 'Inscripciones', 'models/inscripciones/inscripciones.php', 'card_travel');

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
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `created_at`) VALUES
(1, 'santiagocaponf@gmail.com', '24d5af47a9cbf211580048522777fc6c0dc9ba7eb9148d28f63f00e34d3d680246bc5b431699b4bd72b45ff6320f4e0b8db8', '2024-08-30 17:23:46'),
(2, 'santiagocaponf@gmail.com', '0d2bb43ffe6f09878b18cc5771328f16d18370d774e42b8b74d3c06af833a27c7b7d581e1d0b67aa3f4a5e092b48ffc11d94', '2024-08-30 17:26:01'),
(3, 'santiagocaponf@gmail.com', 'ae0ab23eb81dd1e5c7373c74e5d65fb8f811a7fbba37986ea7b990ca3fd43fdfbed0b48dccb55cb2ab27c8cdd1d25ab8c4b2', '2024-08-30 17:27:44'),
(4, 'santiagocaponf@gmail.com', '50f29644c973f3a913459c25e27b762e9c8fe1572ae128886e613b325ea683607763fb2b3a42211643ff5e80785a7d211192', '2024-08-30 17:28:56'),
(5, 'santiagocaponf@gmail.com', '31e556b6ebbe026453840ade559dcf2884c2fbc841528315720b0ab2da57fdd24763fc3c3506683b4fca5cf852de2b51910c', '2024-08-30 17:30:52'),
(6, 'santiagocaponf@gmail.com', 'a3d6181dc3b303830466de3efa97046bcb5e374d2b0760da1d2eea03f5b613200ab5f3a99186bc36e739187776b0b1e35600', '2024-08-30 17:32:18'),
(7, 'santiagocaponf@gmail.com', 'b973fd8b50d8c07c020c95468bc5ff5e59e6b7c6007dd752553910a66a1503f19f74ddba46e81b84e7ce1bd3437037d935de', '2024-08-30 17:32:35'),
(8, 'santiagocaponf@gmail.com', '06587e4b6b06b2b94879084610077e6d2b31c2c81ac3dc10ac9fdb3bc6cba1eb5fe3c6b89c37ca650ac3d26f97f2294e000b', '2024-09-28 20:48:03'),
(9, 'santiagocaponf@gmail.com', '87e60f638aa31701ff7399c78f4e9feb65f787c42522063fd667b67e61c74c44d32203e27ea98a2e41e58fefebc784e243b6', '2024-09-29 11:33:44'),
(10, 'santiagocaponf@gmail.com', '7999390ad9446242f2fbd9a940b3b2e9b349c85694071cc5da7c66736200be01c60f30c43956bc2f9a8238007564232f0890', '2024-09-29 11:37:28'),
(11, 'santiagocaponf@gmail.com', '2d7407e3e304bd9dd6e0abad841a043a1652b8ed17605d409829b344a0ea347fe1142dbafc377fa0c8b8381516b62aae6b52', '2024-09-29 11:39:39'),
(12, 'santiagocaponf@gmail.com', '3efb96a9517e9ec7610375477f406a79f7d6f3eb279c42665d7479c4b77e7f60923ecbb40551c1a7b539dbf104c9cfb085a1', '2024-09-29 11:46:13'),
(13, 'santiagocaponf@gmail.com', 'f41da942fba666909b27d4690e9074c27752fc0c1a3ada9e6a191247df4fe11bccfb299d61e4dbf7032acb289f9c4cdcc790', '2024-09-29 11:47:13'),
(14, 'santiagocaponf@gmail.com', '0a53dda3183238259086af30cdc69b205a12556f5d5cdf2405749fdcf8d1878b', '2024-09-29 13:56:15'),
(15, 'santiagocaponf@gmail.com', '63c8ffda84571cf20c9538de4a91ec875cf076c1e3353cfcf319bbd9881fc33b', '2024-09-29 13:58:11'),
(16, 'santiagocaponf@gmail.com', '58e1c075bb65bda0548c54d05acf268ddcd18ab79f486abdabaf04c3b3acb60a', '2024-09-29 14:00:13'),
(17, 'santiagocaponf@gmail.com', '8f031421d060d541f5ad85bc6ebc90b591fbd4cdb6a6bd4032a62d10ad07862a', '2024-09-29 14:07:42'),
(19, 'juanit@gmail.com', '$2y$10$Aast626DJetMHS1x8sRnvuctb9yIG0g5Mov3kL8CoF3tcUQn2Ql36', '2024-09-29 15:32:25'),
(20, 'santiagocaponf@gmail.com', '$2y$10$VlOkhC59Kji4kvQOEQSBp.OCD46Xa3wFZzAZevdquOQZkXYodrWCS', '2024-09-29 15:32:51');

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
(26, 2, 'Juanito Alimaña', 'juanit@gmail.com', '5233456345', '2024-09-21 19:39:12', 'pendiente', '94b595118998e3e11995c4b83072064c', 53),
(27, 2, 'Juanito Alimaña', 'juanit@gmail.com', '5233456345', '2024-09-21 21:17:33', 'pendiente', '787ccefb8827a1c9344c9ef8b8042c81', 53),
(28, 2, 'Juanito Alimaña', 'juanit@gmail.com', '5233456345', '2024-09-22 01:47:58', 'pendiente', '85614afa65dad23bc45a5e694f47f43e', 53),
(29, 2, 'Juanito Alimaña', 'juanit@gmail.com', '5233456345', '2024-09-22 01:54:04', 'pendiente', '01d33114c1d5540208ad13a6667fa41b', 53),
(30, 2, 'Juanito Alimaña', 'juanit@gmail.com', '5233456345', '2024-09-22 01:58:06', 'pendiente', '39527d96fdf6cd60180736d8fa1acae1', 53),
(31, 2, 'Santiagoe Capon', 'santiagocaponf@gmail.com', '32452345', '2024-09-29 00:47:29', 'pendiente', '738011986a37cf5b380c163e1cff33da', 36),
(32, 2, 'Santiagoe Capon', 'santiagocaponf@gmail.com', '32452345', '2024-09-29 00:47:36', 'pendiente', 'e3ec8b702ae93b469f347d105ef10216', 36);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor`
--

CREATE TABLE `profesor` (
  `id_profesor` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `especialidad` varchar(255) DEFAULT NULL,
  `experiencia` int(11) DEFAULT NULL
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
(36, 'Santiago', 'Capon', 'ID', '12341235234', '2345-03-12', 'WhatsApp Image 2024-07-23 at 4.49.07 PM.jpeg', 'santiagocaponf@gmail.com', '32452345', 'CL 18 A NORTE 2 72', 1, 'alez', '$2y$10$64T2Qk8yptB8y8Rk6Kq26uhnbT3Ias.JH.EXcin2d1BPQCzAvHiM6', '2024-08-25 17:43:54', 'activo', '2024-09-14 21:59:17', 0, NULL),
(42, 'chad', 'sexteto', 'ID', '523456346', '3654-04-23', '66d273c33d474_Recurso 9europe.jpg', 'luisillo@gmail.com', '4563475674', 'CL 18 A NORTE 2 72', 1, 'alez23', '$2y$10$FrpZXvgI3WrL22y9MxNtfuQsyQSgCJ7Jm4VPUv3Aa4qEn2HCKdxsK', '2024-08-29 16:26:44', 'activo', NULL, 0, NULL),
(51, 'antonela', 'sepulveda', 'ID', '342352345', '0005-04-23', '66d23c1021bab_f7c0528d915ec3b38dd89bf7beb2a194.jpg', 'scflorez@corsaje.edu.co', '42352345', 'CL 18 A NORTE 2 72', 1, 'mientras', '$2y$10$KJU2liHj854T1T9M.6/EK.xDYy4sfLf2XEwCldj230rdreZmC.3KC', '2024-08-30 16:39:28', 'activo', NULL, 0, NULL),
(53, 'Juanito', 'Alimaña', 'ID', '43523634', '0634-06-02', '66d2441faa705_pngwing.com.png', 'juanit@gmail.com', '5233456345', 'CL 18 A NORTE 2 72', 3, 'alez123123', '$2y$10$p.bJhCL9d2VM1IjUCnC63.Edj5Pg87KZgKGTFyedUHPusUd.QSDAK', '2024-08-30 17:13:51', 'activo', NULL, 0, NULL),
(55, 'Santiago', 'Capon', 'Passport', '4234523456', '5234-04-23', 'pngwing.com.png', 'scflorez@corsaje.edu.co3', '53643563456', 'CL 18 A NORTE 2 72', 2, 'alez1234', '$2y$10$pcvzMHIh1F53bR25oEpRfu5MbZB5FO6Kn3ceIKwNBtp9KWahjApMe', '2024-09-03 12:35:04', 'activo', NULL, 0, NULL),
(56, 'camilo', 'prato', 'ID', '1091357317', '2024-09-17', 'salir guapo en fotos-605380757.webp', 'albertocamiloprato@gmail.com', '3043282464', 'Sapo Marica', 1, 'camilo', '$2y$10$pkH8Zi8gEArSclW4KlpcjOm0Tbx5fSF2o8f7Ukw8qUNWj8Bl7i2I.', '2024-09-07 18:45:23', 'activo', '2024-09-08 11:32:39', 0, NULL),
(58, 'santiago', NULL, NULL, NULL, NULL, NULL, 'edison_alberto@hotmail.com', '52343456', '', 4, 'edison_alberto', '$2y$10$DLJSPUZnsBduhl5PFRtg6uP5aXma0xTP9FOSkKUN/g2l9MrcP7d3S', '2024-09-19 12:02:34', 'activo', NULL, 0, NULL),
(59, 'Santiago', NULL, NULL, NULL, NULL, NULL, 'scapon@misena.edu.co', '3034235435', '', 4, 'scapon', '$2y$10$yrrLCg7Fr85s6u9jOmiVMO14UhXLMOrHN6krm2bL4fCNqnnCqc4Oy', '2024-09-20 20:09:22', 'activo', NULL, 0, NULL),
(60, 'Santiago', NULL, NULL, NULL, NULL, NULL, 'albertocamiloprato@gmail.comw', '563456346', '', 4, 'albertocamiloprato', '$2y$10$3u2mhrj1Ce6x8WCnljewFOuy20NifY7kKhXbWo0Y7NqPSXH89a9qe', '2024-09-20 20:50:39', 'activo', NULL, 0, NULL),
(63, NULL, NULL, NULL, NULL, NULL, NULL, 'santigao@gmail.com', NULL, '', 1, 'alez1233', '$2y$10$DSX8990wWKG04J/82ENXo.xZAJyQn/flaX2ULl1gFLB3TuZKQpMZ6', '2024-09-28 20:04:00', 'activo', NULL, 0, NULL);

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
  ADD KEY `fk_categoria` (`id_categoria`);

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
  ADD KEY `id_curso` (`id_curso`);

--
-- Indices de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD PRIMARY KEY (`id_inscripcion`),
  ADD KEY `id_curso` (`id_curso`),
  ADD KEY `id_estudiante` (`id_estudiante`),
  ADD KEY `idx_inscripciones_curso_estudiante` (`id_curso`,`id_estudiante`),
  ADD KEY `inscripciones_ibfk_3` (`id_preinscripcion`);

--
-- Indices de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id_asignacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `asig_modulo`
--
ALTER TABLE `asig_modulo`
  MODIFY `id_asig_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- AUTO_INCREMENT de la tabla `estudiante`
--
ALTER TABLE `estudiante`
  MODIFY `id_estudiante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `historial_inscripciones`
--
ALTER TABLE `historial_inscripciones`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `id_inscripcion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `preinscripciones`
--
ALTER TABLE `preinscripciones`
  MODIFY `id_preinscripcion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `profesor`
--
ALTER TABLE `profesor`
  MODIFY `id_profesor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

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
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`);

--
-- Filtros para la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD CONSTRAINT `inscripciones_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`),
  ADD CONSTRAINT `inscripciones_ibfk_2` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`),
  ADD CONSTRAINT `inscripciones_ibfk_3` FOREIGN KEY (`id_preinscripcion`) REFERENCES `preinscripciones` (`id_preinscripcion`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`);

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
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_tipo_usuario`) REFERENCES `tipo_usuario` (`id_tipo_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
