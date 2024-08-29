-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-08-2024 a las 23:38:43
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
(16, 4, 1, '2024-08-29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--

CREATE TABLE `asistencia` (
  `id_asistencia` int(11) NOT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `presente` tinyint(1) DEFAULT NULL,
  `justificacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id_curso`, `nombre_curso`, `descripcion`, `nivel_educativo`, `duracion`, `estado`) VALUES
(1, 'danzas', 'curso de danzas', 'primaria', 3, 'activo');

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
  `fecha_inscripcion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(2, 'Profesor', '../profesor/profesor.php', 'person'),
(3, 'Estudiante', '../estudiante/estudiante.php', 'account_circle\r\n'),
(4, 'Pagos', '', 'shopping_cart');

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
(3, 'estudiante');

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
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellido`, `tipo_doc`, `documento`, `fecha_nac`, `foto`, `mail`, `telefono`, `direccion`, `id_tipo_usuario`, `username`, `clave`, `fecha_registro`, `estado`) VALUES
(36, 'Santiago', 'Capon', 'ID', '12341235234', '2345-03-12', 'WhatsApp Image 2024-07-23 at 4.49.07 PM.jpeg', 'santiagocaponf@gmail.com', '32452345', 'CL 18 A NORTE 2 72', 1, 'alez', '$2y$10$cCIaWsXSHC1OWcRBpDQU.uhrDuxWP0j4IMUTxNNojuHeT3Sq.uYkq', '2024-08-25 17:43:54', 'activo'),
(37, 'camilo', 'florez', 'ID', '23452353245', '4253-04-23', 'Captura de pantalla 2024-04-17 a las 20.52.55.png', 'scapon@misena.edu.co', '3042134311', 'CL 18 A NORTE 2 72', 3, 'alez34', '$2y$10$LHNPJC8kdCT1FdoyVD7Ry.lIghhOnQN6V6RhjJRmSa3EMqHOipw2y', '2024-08-25 18:07:15', 'activo'),
(40, 'alejandra', 'sepulveda', 'ID', '342352345', '0005-04-23', 'Captura de pantalla 2024-04-17 a las 20.52.55.png', 'scflorez@corsaje.edu.co', '42352345', 'CL 18 A NORTE 2 72', 1, 'mientras', '$2y$10$aoefIKerqMzeAKAUn.k/B.ihYSLc3/vveKskxgvTKEOCGb5TbAbbK', '2024-08-29 09:54:39', 'activo'),
(41, 'juan', 'sepulveda', 'Passport', '5423452345', '4345-04-23', 'RobloxScreenShot20231202_191516430.png', 'scapon@misena.edu.co', '523564356345', 'CL 18 A NORTE 2 72', 3, 'alejandro', '$2y$10$D.32MmQZECgkex2h1exujed0mXifEkJpqkTiUQ0pWEe4nQ//peG0a', '2024-08-29 16:24:47', 'activo'),
(42, 'chad', 'sexteto', 'ID', '523456346', '3654-04-23', 'e34ac480-ce44-439a-b28b-1845bd195afe.webp', 'luisillo@gmail.com', '4563475674', 'CL 18 A NORTE 2 72', 1, 'alez23', '$2y$10$FrpZXvgI3WrL22y9MxNtfuQsyQSgCJ7Jm4VPUv3Aa4qEn2HCKdxsK', '2024-08-29 16:26:44', 'activo'),
(43, 'fasdfasdfasdf', 'asdfasdfasdf', 'ID', '4523452346', '3315-04-23', 'Rayquaza_Ilustracion.png', '534523452@523452345', '534563456345', 'CL 18 A NORTE 2 72', 2, 'alez123', '$2y$10$Z.6i8NTXu.DE6.sziN9Tl.e30MnpgRUCQJYWfMBWGNCvJLJu4Xas6', '2024-08-29 16:27:35', 'activo');

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
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id_curso`);

--
-- Indices de la tabla `estudiante`
--
ALTER TABLE `estudiante`
  ADD PRIMARY KEY (`id_estudiante`),
  ADD KEY `id_usuario` (`id_usuario`);

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
  ADD KEY `id_estudiante` (`id_estudiante`);

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
-- Indices de la tabla `profesor`
--
ALTER TABLE `profesor`
  ADD PRIMARY KEY (`id_profesor`),
  ADD KEY `id_usuario` (`id_usuario`);

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
  MODIFY `id_asig_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  MODIFY `id_asistencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `estudiante`
--
ALTER TABLE `estudiante`
  MODIFY `id_estudiante` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `id_inscripcion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `profesor`
--
ALTER TABLE `profesor`
  MODIFY `id_profesor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tipo_usuario`
--
ALTER TABLE `tipo_usuario`
  MODIFY `id_tipo_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

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
  ADD CONSTRAINT `asig_modulo_ibfk_1` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`);

--
-- Filtros para la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD CONSTRAINT `asistencia_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`),
  ADD CONSTRAINT `asistencia_ibfk_2` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`);

--
-- Filtros para la tabla `estudiante`
--
ALTER TABLE `estudiante`
  ADD CONSTRAINT `estudiante_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `tipo_usuario` (`id_tipo_usuario`),
  ADD CONSTRAINT `fk_estudiante_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

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
  ADD CONSTRAINT `inscripciones_ibfk_2` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiante` (`id_estudiante`);

--
-- Filtros para la tabla `profesor`
--
ALTER TABLE `profesor`
  ADD CONSTRAINT `fk_profesor_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `profesor_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `tipo_usuario` (`id_tipo_usuario`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_tipo_usuario`) REFERENCES `tipo_usuario` (`id_tipo_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
