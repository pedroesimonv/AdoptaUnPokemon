-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-05-2026 a las 11:46:43
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `refugio_pokemon`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE `equipos` (
  `id` int(11) NOT NULL,
  `nombre_equipo` varchar(100) NOT NULL,
  `zona_asignada` varchar(100) DEFAULT NULL,
  `lider_id` int(11) DEFAULT NULL,
  `especialidad` enum('Incendios','Rescate Acuático','Escombros/Sismos','Logística','Médico') DEFAULT 'Logística'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`id`, `nombre_equipo`, `zona_asignada`, `lider_id`, `especialidad`) VALUES
(1, 'Escuadrón Squirtle', '', NULL, 'Incendios'),
(2, 'Escuadrón Sísmico', '', NULL, 'Escombros/Sismos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pokemon_adopcion`
--

CREATE TABLE `pokemon_adopcion` (
  `id` int(11) NOT NULL,
  `nombre_propio` varchar(100) NOT NULL,
  `especie_api_id` int(11) NOT NULL,
  `estado` enum('disponible','herido','adoptado','en_rescate') DEFAULT 'disponible',
  `descripcion` text DEFAULT NULL,
  `fecha_rescate` date NOT NULL,
  `equipo_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pokemon_adopcion`
--

INSERT INTO `pokemon_adopcion` (`id`, `nombre_propio`, `especie_api_id`, `estado`, `descripcion`, `fecha_rescate`, `equipo_id`) VALUES
(1, 'Sparky', 25, 'disponible', 'Encontrado cerca de la central eléctrica, parece un poco asustado.', '2026-04-26', NULL),
(2, 'Pibi', 45, 'herido', '', '2026-04-02', NULL),
(3, 'Bulby', 1, 'disponible', 'Encontrado en un jardín abandonado, es muy dócil pero necesita sol.', '2026-05-04', NULL),
(4, 'Brutus', 66, 'en_rescate', 'Ayudando actualmente en la remoción de escombros tras el último sismo.', '2026-05-04', NULL),
(5, 'Aqua', 7, 'herido', 'Rescatado de una red de pesca ilegal. Tiene una aleta lastimada.', '2026-05-04', NULL),
(6, 'Fénix', 4, 'disponible', 'Su anterior entrenador lo abandonó bajo la lluvia. Ya está recuperado.', '2026-05-04', NULL),
(7, 'Nieve', 131, 'disponible', 'Encontrado encallado en la costa. Es enorme y muy amigable.', '2026-05-04', 1),
(8, 'Colmillos', 58, 'herido', 'Rescatado de un incendio forestal. Inhaló mucho humo y está en tratamiento.', '2026-05-04', NULL),
(9, 'Roca', 74, 'disponible', 'Apareció en una cantera. Es perfecto para trabajos de fuerza.', '2026-05-04', 2),
(10, 'Viento', 17, 'en_rescate', 'Realizando labores de reconocimiento aéreo en la zona norte.', '2026-05-04', NULL),
(11, 'Mordisquitos', 158, 'disponible', 'Un Totodile rescatado de un pantano seco. Tiene mucha energía.', '2026-05-04', 1),
(12, 'Sombra', 197, 'en_rescate', 'Umbreon especialista en misiones nocturnas. Muy sigiloso.', '2026-05-04', NULL),
(13, 'Titán', 248, 'disponible', 'Tyranitar que fue desplazado de su montaña por una constructora.', '2026-05-04', NULL),
(14, 'Barro', 258, 'disponible', 'Mudkip muy tranquilo. Le encanta chapotear en charcos de lodo.', '2026-05-04', NULL),
(15, 'Diva', 282, 'herido', 'Gardevoir herida tras proteger a un grupo de Ralts de unos cazadores.', '2026-05-04', NULL),
(16, 'Presagio', 359, 'disponible', 'Absol que apareció antes de una tormenta. Es un incomprendido.', '2026-05-04', NULL),
(17, 'Chispas', 405, 'herido', 'Luxray con problemas en su visión tras un cortocircuito eléctrico.', '2026-05-04', NULL),
(18, 'Tiburón', 445, 'disponible', 'Garchomp rescatado de un desierto. Es extremadamente rápido.', '2026-05-04', NULL),
(19, 'Aura', 448, 'en_rescate', 'Lucario que ayuda a localizar personas perdidas mediante el aura.', '2026-05-04', NULL),
(20, 'Elegante', 495, 'disponible', 'Snivy con un carácter un poco difícil, pero muy noble.', '2026-05-04', NULL),
(21, 'Ilusión', 571, 'herido', 'Zoroark rescatado con una pata lastimada tras una caída.', '2026-05-04', NULL),
(22, 'Ninja', 658, 'en_rescate', 'Greninja experto en rescate acuático y movimiento rápido.', '2026-05-04', NULL),
(23, 'Lazos', 700, 'disponible', 'Sylveon muy cariñoso que ayuda a calmar a los Pokémon heridos.', '2026-05-04', NULL),
(24, 'Dormilón', 722, 'disponible', 'Rowlet que siempre se queda dormido en las reuniones. Muy tierno.', '2026-05-04', NULL),
(25, 'Cariño', 778, 'herido', 'Mimikyu cuyo disfraz se rompió. Está en reparación psicológica.', '2026-05-04', NULL),
(26, 'Ritmo', 810, 'disponible', 'Grookey que no deja de tamborilear. Alegra a todo el refugio.', '2026-05-04', NULL),
(27, 'Acero', 823, 'en_rescate', 'Corviknight que sirve como transporte aéreo de emergencia.', '2026-05-04', NULL),
(28, 'Menta', 906, 'disponible', 'Sprigatito rescatado de un callejón. Huele a hierba fresca.', '2026-05-04', NULL),
(29, 'Pimiento', 909, 'disponible', 'Fuecoco que se come hasta las piedras. Es muy despistado.', '2026-05-04', NULL),
(30, 'Gomina', 912, 'herido', 'Quaxly preocupado porque se le despeinó el flequillo en un rescate.', '2026-05-04', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','rescatista') DEFAULT 'rescatista',
  `equipo_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `equipo_id`) VALUES
(2, 'Administrador', 'admin@admin.com', '$2y$10$BBaoXiq3AwQg35d/X8YzOOSOGFC.M54oAvdU5W4GDQ/tu/JMQfdAa', 'admin', NULL),
(4, 'Benito', 'benito@benito.com', '$2y$10$Wt8zSmEp2NPQFv5r0obTd.YAMTpP1Im9VXOrW6O65Ef0LQ9N9uOvi', 'rescatista', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_equipo_lider` (`lider_id`);

--
-- Indices de la tabla `pokemon_adopcion`
--
ALTER TABLE `pokemon_adopcion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipo_id` (`equipo_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_usuario_equipo` (`equipo_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pokemon_adopcion`
--
ALTER TABLE `pokemon_adopcion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD CONSTRAINT `fk_equipo_lider` FOREIGN KEY (`lider_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `pokemon_adopcion`
--
ALTER TABLE `pokemon_adopcion`
  ADD CONSTRAINT `pokemon_adopcion_ibfk_1` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_equipo` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
