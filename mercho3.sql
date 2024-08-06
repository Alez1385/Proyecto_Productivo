-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-08-2024 a las 00:58:31
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
-- Base de datos: `mercho3`
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
  `fecha_asignacion` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asig_modulo`
--

CREATE TABLE `asig_modulo` (
  `id_asig_modulo` int(11) NOT NULL,
  `id_modulo` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_asig` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--

CREATE TABLE `asistencia` (
  `id_asistencia` int(11) NOT NULL,
  `id_estudiante` int(11) DEFAULT NULL,
  `id_curso` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `presente` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id_curso` int(11) NOT NULL,
  `nombre_curso` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiante`
--

CREATE TABLE `estudiante` (
  `id_estudiante` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL
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
  `desc_modulo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Estructura de tabla para la tabla `profesor`
--

CREATE TABLE `profesor` (
  `id_profesor` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `tipo_doc` varchar(10) DEFAULT NULL,
  `documento` int(12) DEFAULT NULL,
  `fecha_nac` date DEFAULT NULL,
  `foto` varchar(50) DEFAULT NULL,
  `mail` varchar(50) DEFAULT NULL,
  `telefono` varchar(25) DEFAULT NULL,
  `direccion` varchar(100) NOT NULL,
  `id_tipo_usuario` int(11) DEFAULT NULL,
  `login` varchar(50) DEFAULT NULL,
  `clave` int(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `tipo_usuario` enum('administrador','profesor','estudiante','gerente','auxiliar') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  ADD KEY `id_usuario` (`id_usuario`);

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
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_tipo_usuario` (`id_tipo_usuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

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
  ADD CONSTRAINT `asig_modulo_ibfk_1` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`),
  ADD CONSTRAINT `asig_modulo_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

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
  ADD CONSTRAINT `estudiante_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

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
  ADD CONSTRAINT `profesor_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_tipo_usuario`) REFERENCES `usuarios` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
