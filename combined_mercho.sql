-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-07-2024 a las 21:36:01
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
-- Base de datos: `mercho`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asig_modulo`
--

CREATE TABLE `asig_modulo` (
  `id_asig_modulo` int(10) NOT NULL,
  `id_modulo` int(10) DEFAULT NULL,
  `id_usuario` int(10) DEFAULT NULL,
  `fecha_asig` date DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asig_modulo`
--

INSERT INTO `asig_modulo` (`id_asig_modulo`, `id_modulo`, `id_usuario`, `fecha_asig`) VALUES
(71, 1, 1, '2024-07-25'),
(72, 2, 1, '2024-07-25'),
(73, 1, 3, '2024-07-25'),
(74, 1, 2, '2024-07-25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id_modulo` int(10) NOT NULL,
  `nom_modulo` varchar(30) DEFAULT NULL,
  `desc_modulo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id_modulo`, `nom_modulo`, `desc_modulo`) VALUES
(1, 'Profesor', 'Modulo de los profes'),
(2, 'Estudiante', 'Modulo de los estudiantes'),
(3, 'Asignacion de curso', 'Modulo de asignaciones de curso'),
(4, 'Pagos', 'Modulo de pagos'),
(5, 'Cursos', 'Modulo de los cursos'),
(6, 'Inscripciones', 'Modulo de las inscipciones'),
(7, 'Asistencia', 'Modulo de la asistencia'),
(8, 'Usuarios', 'Modulo de los usuarios');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_usuario`
--

CREATE TABLE `tipo_usuario` (
  `id_tipo_usuario` int(10) NOT NULL,
  `descripcion` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_usuario`
--

INSERT INTO `tipo_usuario` (`id_tipo_usuario`, `descripcion`) VALUES
(1, 'Admin'),
(2, 'Auxiliar'),
(3, 'Gerente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(10) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `tipo_doc` varchar(10) DEFAULT NULL,
  `documento` int(12) DEFAULT NULL,
  `fecha_nac` date DEFAULT NULL,
  `foto` varchar(50) DEFAULT NULL,
  `mail` varchar(50) DEFAULT NULL,
  `telefono` varchar(25) DEFAULT NULL,
  `id_tipo_usuario` int(10) DEFAULT NULL,
  `login` varchar(50) DEFAULT NULL,
  `clave` int(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellido`, `tipo_doc`, `documento`, `fecha_nac`, `foto`, `mail`, `telefono`, `id_tipo_usuario`, `login`, `clave`) VALUES
(1, 'Juan', 'Perez', 'TI', 1093297500, '2024-07-18', '', 'santiagocaponf@gmail.com', '3042134311', 1, 'Alez1385', 0),
(2, 'Gerson', 'Rubio', 'TI', 109034231, '2024-07-31', NULL, 'gersonrubio@gmail.com', '3042154321', 2, 'Gerson', 1034235),
(3, 'Santiago', 'Capon', 'TI', 2147483647, '2024-07-24', '', 'santiagocaponf@gmail.com', '1234567890', 3, 'sadfasdf', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asig_modulo`
--
ALTER TABLE `asig_modulo`
  ADD PRIMARY KEY (`id_asig_modulo`),
  ADD KEY `id_modulo` (`id_modulo`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id_modulo`);

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
-- AUTO_INCREMENT de la tabla `asig_modulo`
--
ALTER TABLE `asig_modulo`
  MODIFY `id_asig_modulo` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulo` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tipo_usuario`
--
ALTER TABLE `tipo_usuario`
  MODIFY `id_tipo_usuario` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asig_modulo`
--
ALTER TABLE `asig_modulo`
  ADD CONSTRAINT `asig_modulo_ibfk_1` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`),
  ADD CONSTRAINT `asig_modulo_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_tipo_usuario`) REFERENCES `tipo_usuario` (`id_tipo_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


-- --------------------------------------------------------
-- Table structure for `profesor`
-- --------------------------------------------------------
CREATE TABLE `profesor` (
  `id_profesor` INT(10) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `apellido` VARCHAR(50) NOT NULL,
  `correo` VARCHAR(100) NOT NULL,
  `telefono` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`id_profesor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `estudiante`
-- --------------------------------------------------------
CREATE TABLE `estudiante` (
  `id_estudiante` INT(10) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `apellido` VARCHAR(50) NOT NULL,
  `correo` VARCHAR(100) NOT NULL,
  `telefono` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`id_estudiante`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `asignacion_curso`
-- --------------------------------------------------------
CREATE TABLE `asignacion_curso` (
  `id_asignacion` INT(10) NOT NULL AUTO_INCREMENT,
  `id_curso` INT(10) NOT NULL,
  `id_profesor` INT(10) NOT NULL,
  `id_estudiante` INT(10) NOT NULL,
  `fecha_asignacion` DATE NOT NULL,
  PRIMARY KEY (`id_asignacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `pagos`
-- --------------------------------------------------------
CREATE TABLE `pagos` (
  `id_pago` INT(10) NOT NULL AUTO_INCREMENT,
  `id_estudiante` INT(10) NOT NULL,
  `monto` DECIMAL(10, 2) NOT NULL,
  `fecha_pago` DATE NOT NULL,
  PRIMARY KEY (`id_pago`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `cursos`
-- --------------------------------------------------------
CREATE TABLE `cursos` (
  `id_curso` INT(10) NOT NULL AUTO_INCREMENT,
  `nombre_curso` VARCHAR(100) NOT NULL,
  `descripcion` TEXT NOT NULL,
  PRIMARY KEY (`id_curso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `inscripciones`
-- --------------------------------------------------------
CREATE TABLE `inscripciones` (
  `id_inscripcion` INT(10) NOT NULL AUTO_INCREMENT,
  `id_curso` INT(10) NOT NULL,
  `id_estudiante` INT(10) NOT NULL,
  `fecha_inscripcion` DATE NOT NULL,
  PRIMARY KEY (`id_inscripcion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `asistencia`
-- --------------------------------------------------------
CREATE TABLE `asistencia` (
  `id_asistencia` INT(10) NOT NULL AUTO_INCREMENT,
  `id_estudiante` INT(10) NOT NULL,
  `id_curso` INT(10) NOT NULL,
  `fecha` DATE NOT NULL,
  `presente` BOOLEAN NOT NULL,
  PRIMARY KEY (`id_asistencia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `usuarios`
-- --------------------------------------------------------
CREATE TABLE `usuarios` (
  `id_usuario` INT(10) NOT NULL AUTO_INCREMENT,
  `nombre_usuario` VARCHAR(50) NOT NULL,
  `password` VARCHAR(100) NOT NULL,
  `tipo_usuario` ENUM('administrador', 'profesor', 'estudiante') NOT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
