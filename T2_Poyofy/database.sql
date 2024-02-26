-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-08-2020 a las 02:05:41
-- Versión del servidor: 10.4.13-MariaDB
-- Versión de PHP: 7.4.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `poyofy`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `albums`
--

CREATE TABLE `albums` (
  `id_album` int(11) NOT NULL,
  `nombre_album` varchar(50) NOT NULL,
  `autor` varchar(50) NOT NULL,
  `canciones` int(11) DEFAULT 0,
  `anno_publicacion` year(4) DEFAULT 2020
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Disparadores `albums`
--
DELIMITER $$
CREATE TRIGGER `restar_album_creado` AFTER DELETE ON `albums` FOR EACH ROW UPDATE artistas SET artistas.albums_publicados = artistas.albums_publicados - 1
WHERE artistas.username = old.autor
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `sumar_album_creado` AFTER INSERT ON `albums` FOR EACH ROW UPDATE artistas SET artistas.albums_publicados = artistas.albums_publicados + 1
WHERE artistas.username = new.autor
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `albums_incluyendo_canciones`
--

CREATE TABLE `albums_incluyendo_canciones` (
  `id_album` int(11) NOT NULL,
  `id_cancion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Disparadores `albums_incluyendo_canciones`
--
DELIMITER $$
CREATE TRIGGER `restar_cancion_album` AFTER DELETE ON `albums_incluyendo_canciones` FOR EACH ROW UPDATE albums SET albums.canciones = albums.canciones  -1
WHERE albums.id_album = old.id_album
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `sumar_cancion_album` AFTER INSERT ON `albums_incluyendo_canciones` FOR EACH ROW UPDATE albums SET albums.canciones = albums.canciones + 1
WHERE albums.id_album = new.id_album
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `artistas`
--

CREATE TABLE `artistas` (
  `username` varchar(50) NOT NULL,
  `canciones_publicadas` int(11) DEFAULT 0,
  `albums_publicados` int(11) DEFAULT 0,
  `playlists_seguidas` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `canciones`
--

CREATE TABLE `canciones` (
  `id_cancion` int(11) NOT NULL,
  `nombre_cancion` varchar(50) NOT NULL,
  `autor` varchar(50) NOT NULL,
  `genero` varchar(50) DEFAULT NULL,
  `duracion` varchar(5) DEFAULT '00:00',
  `cantidad_likes` int(11) DEFAULT 0,
  `anno_publicacion` year(4) DEFAULT 2020
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Disparadores `canciones`
--
DELIMITER $$
CREATE TRIGGER `restar_cancion_creada` AFTER DELETE ON `canciones` FOR EACH ROW UPDATE artistas SET artistas.canciones_publicadas = artistas.canciones_publicadas - 1
WHERE artistas.username = old.autor
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `sumar_cancion_creada` AFTER INSERT ON `canciones` FOR EACH ROW UPDATE artistas SET artistas.canciones_publicadas = artistas.canciones_publicadas + 1
WHERE artistas.username = new.autor
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personas`
--

CREATE TABLE `personas` (
  `username` varchar(50) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `contrasena` longtext NOT NULL,
  `seguidores` int(11) DEFAULT 0,
  `seguidos` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personas_siguiendo_personas`
--

CREATE TABLE `personas_siguiendo_personas` (
  `username_seguido` varchar(50) NOT NULL,
  `username_seguidor` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Disparadores `personas_siguiendo_personas`
--
DELIMITER $$
CREATE TRIGGER `restar_seguido` AFTER DELETE ON `personas_siguiendo_personas` FOR EACH ROW UPDATE personas SET personas.seguidos = personas.seguidos - 1
WHERE personas.username = old.username_seguidor
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `restar_seguidor` AFTER DELETE ON `personas_siguiendo_personas` FOR EACH ROW UPDATE personas SET personas.seguidores = personas.seguidores - 1
WHERE personas.username = old.username_seguido
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `sumar_seguido` AFTER INSERT ON `personas_siguiendo_personas` FOR EACH ROW UPDATE personas SET personas.seguidos = personas.seguidos + 1
WHERE personas.username = new.username_seguidor
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `sumar_seguidor` AFTER INSERT ON `personas_siguiendo_personas` FOR EACH ROW UPDATE personas SET personas.seguidores = personas.seguidores + 1
WHERE personas.username = new.username_seguido
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personas_siguiendo_playlists`
--

CREATE TABLE `personas_siguiendo_playlists` (
  `username` varchar(50) NOT NULL,
  `id_playlist` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Disparadores `personas_siguiendo_playlists`
--
DELIMITER $$
CREATE TRIGGER `restar_playlists_seguidas_artista` AFTER DELETE ON `personas_siguiendo_playlists` FOR EACH ROW UPDATE artistas SET artistas.playlists_seguidas = artistas.playlists_seguidas - 1
WHERE artistas.username = old.username
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `restar_playlists_seguidas_usuario` AFTER DELETE ON `personas_siguiendo_playlists` FOR EACH ROW UPDATE usuarios SET usuarios.playlists_seguidas = usuarios.playlists_seguidas - 1
WHERE usuarios.username = old.username
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `restar_seguidor_playlist` AFTER DELETE ON `personas_siguiendo_playlists` FOR EACH ROW UPDATE playlists SET playlists.seguidores = playlists.seguidores  -1
WHERE playlists.id_playlist = old.id_playlist
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `sumar_playlists_seguidas_artista` AFTER INSERT ON `personas_siguiendo_playlists` FOR EACH ROW UPDATE artistas SET artistas.playlists_seguidas = artistas.playlists_seguidas + 1
WHERE artistas.username = new.username
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `sumar_playlists_seguidas_usuario` AFTER INSERT ON `personas_siguiendo_playlists` FOR EACH ROW UPDATE usuarios SET usuarios.playlists_seguidas = usuarios.playlists_seguidas + 1
WHERE usuarios.username = new.username
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `sumar_seguidor_playlist` AFTER INSERT ON `personas_siguiendo_playlists` FOR EACH ROW UPDATE playlists SET playlists.seguidores = playlists.seguidores + 1
WHERE playlists.id_playlist = new.id_playlist
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `playlists`
--

CREATE TABLE `playlists` (
  `id_playlist` int(11) NOT NULL,
  `nombre_playlist` varchar(50) NOT NULL,
  `creador` varchar(50) NOT NULL,
  `canciones` int(11) DEFAULT 0,
  `seguidores` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Disparadores `playlists`
--
DELIMITER $$
CREATE TRIGGER `restar_playlist_creada` AFTER DELETE ON `playlists` FOR EACH ROW UPDATE usuarios SET usuarios.playlists_creadas = usuarios.playlists_creadas - 1
WHERE usuarios.username = old.creador
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `sumar_playlist_creada` AFTER INSERT ON `playlists` FOR EACH ROW UPDATE usuarios SET usuarios.playlists_creadas = usuarios.playlists_creadas + 1
WHERE usuarios.username = new.creador
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `playlists_conteniendo_canciones`
--

CREATE TABLE `playlists_conteniendo_canciones` (
  `id_playlist` int(11) NOT NULL,
  `id_cancion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Disparadores `playlists_conteniendo_canciones`
--
DELIMITER $$
CREATE TRIGGER `restar_cancion_playlist` AFTER DELETE ON `playlists_conteniendo_canciones` FOR EACH ROW UPDATE playlists SET playlists.canciones = playlists.canciones  -1
WHERE playlists.id_playlist = old.id_playlist
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `sumar_cancion_playlist` AFTER INSERT ON `playlists_conteniendo_canciones` FOR EACH ROW UPDATE playlists SET playlists.canciones = playlists.canciones + 1
WHERE playlists.id_playlist = new.id_playlist
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `username` varchar(50) NOT NULL,
  `playlists_creadas` int(11) DEFAULT 0,
  `playlists_seguidas` int(11) DEFAULT 0,
  `genero_favorito` varchar(50) DEFAULT 'Ninguno'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_gustando_canciones`
--

CREATE TABLE `usuarios_gustando_canciones` (
  `username` varchar(50) NOT NULL,
  `id_cancion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Disparadores `usuarios_gustando_canciones`
--
DELIMITER $$
CREATE TRIGGER `restar_like_cancion` AFTER DELETE ON `usuarios_gustando_canciones` FOR EACH ROW UPDATE canciones SET canciones.cantidad_likes = canciones.cantidad_likes - 1
WHERE canciones.id_cancion = old.id_cancion
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `sumar_like_cancion` AFTER INSERT ON `usuarios_gustando_canciones` FOR EACH ROW UPDATE canciones SET canciones.cantidad_likes = canciones.cantidad_likes + 1
WHERE canciones.id_cancion = new.id_cancion
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `view_artistas`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `view_artistas` (
`username` varchar(50)
,`seguidores` int(11)
,`seguidos` int(11)
,`canciones_publicadas` int(11)
,`albums_publicados` int(11)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `view_usuarios`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `view_usuarios` (
`username` varchar(50)
,`seguidores` int(11)
,`seguidos` int(11)
,`playlists_creadas` int(11)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `view_artistas`
--
DROP TABLE IF EXISTS `view_artistas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_artistas`  AS  select `p`.`username` AS `username`,`p`.`seguidores` AS `seguidores`,`p`.`seguidos` AS `seguidos`,`a`.`canciones_publicadas` AS `canciones_publicadas`,`a`.`albums_publicados` AS `albums_publicados` from (`personas` `p` join `artistas` `a` on(`p`.`username` = `a`.`username`)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `view_usuarios`
--
DROP TABLE IF EXISTS `view_usuarios`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_usuarios`  AS  select `p`.`username` AS `username`,`p`.`seguidores` AS `seguidores`,`p`.`seguidos` AS `seguidos`,`u`.`playlists_creadas` AS `playlists_creadas` from (`personas` `p` join `usuarios` `u` on(`p`.`username` = `u`.`username`)) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `albums`
--
ALTER TABLE `albums`
  ADD PRIMARY KEY (`id_album`),
  ADD KEY `autor` (`autor`);

--
-- Indices de la tabla `albums_incluyendo_canciones`
--
ALTER TABLE `albums_incluyendo_canciones`
  ADD PRIMARY KEY (`id_album`,`id_cancion`),
  ADD KEY `id_cancion` (`id_cancion`);

--
-- Indices de la tabla `artistas`
--
ALTER TABLE `artistas`
  ADD PRIMARY KEY (`username`);

--
-- Indices de la tabla `canciones`
--
ALTER TABLE `canciones`
  ADD PRIMARY KEY (`id_cancion`),
  ADD KEY `autor` (`autor`);

--
-- Indices de la tabla `personas`
--
ALTER TABLE `personas`
  ADD PRIMARY KEY (`username`);

--
-- Indices de la tabla `personas_siguiendo_personas`
--
ALTER TABLE `personas_siguiendo_personas`
  ADD PRIMARY KEY (`username_seguido`,`username_seguidor`),
  ADD KEY `username_seguidor` (`username_seguidor`);

--
-- Indices de la tabla `personas_siguiendo_playlists`
--
ALTER TABLE `personas_siguiendo_playlists`
  ADD PRIMARY KEY (`username`,`id_playlist`),
  ADD KEY `id_playlist` (`id_playlist`);

--
-- Indices de la tabla `playlists`
--
ALTER TABLE `playlists`
  ADD PRIMARY KEY (`id_playlist`),
  ADD KEY `creador` (`creador`);

--
-- Indices de la tabla `playlists_conteniendo_canciones`
--
ALTER TABLE `playlists_conteniendo_canciones`
  ADD PRIMARY KEY (`id_playlist`,`id_cancion`),
  ADD KEY `id_cancion` (`id_cancion`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`username`);

--
-- Indices de la tabla `usuarios_gustando_canciones`
--
ALTER TABLE `usuarios_gustando_canciones`
  ADD PRIMARY KEY (`username`,`id_cancion`),
  ADD KEY `id_cancion` (`id_cancion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `albums`
--
ALTER TABLE `albums`
  MODIFY `id_album` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `canciones`
--
ALTER TABLE `canciones`
  MODIFY `id_cancion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `playlists`
--
ALTER TABLE `playlists`
  MODIFY `id_playlist` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `albums`
--
ALTER TABLE `albums`
  ADD CONSTRAINT `albums_ibfk_1` FOREIGN KEY (`autor`) REFERENCES `artistas` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `albums_incluyendo_canciones`
--
ALTER TABLE `albums_incluyendo_canciones`
  ADD CONSTRAINT `albums_incluyendo_canciones_ibfk_1` FOREIGN KEY (`id_album`) REFERENCES `albums` (`id_album`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `albums_incluyendo_canciones_ibfk_2` FOREIGN KEY (`id_cancion`) REFERENCES `canciones` (`id_cancion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `artistas`
--
ALTER TABLE `artistas`
  ADD CONSTRAINT `artistas_ibfk_1` FOREIGN KEY (`username`) REFERENCES `personas` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `canciones`
--
ALTER TABLE `canciones`
  ADD CONSTRAINT `canciones_ibfk_1` FOREIGN KEY (`autor`) REFERENCES `artistas` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `personas_siguiendo_personas`
--
ALTER TABLE `personas_siguiendo_personas`
  ADD CONSTRAINT `personas_siguiendo_personas_ibfk_1` FOREIGN KEY (`username_seguido`) REFERENCES `personas` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `personas_siguiendo_personas_ibfk_2` FOREIGN KEY (`username_seguidor`) REFERENCES `personas` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `personas_siguiendo_playlists`
--
ALTER TABLE `personas_siguiendo_playlists`
  ADD CONSTRAINT `personas_siguiendo_playlists_ibfk_1` FOREIGN KEY (`username`) REFERENCES `personas` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `personas_siguiendo_playlists_ibfk_2` FOREIGN KEY (`id_playlist`) REFERENCES `playlists` (`id_playlist`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `playlists`
--
ALTER TABLE `playlists`
  ADD CONSTRAINT `playlists_ibfk_1` FOREIGN KEY (`creador`) REFERENCES `usuarios` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `playlists_conteniendo_canciones`
--
ALTER TABLE `playlists_conteniendo_canciones`
  ADD CONSTRAINT `playlists_conteniendo_canciones_ibfk_1` FOREIGN KEY (`id_playlist`) REFERENCES `playlists` (`id_playlist`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `playlists_conteniendo_canciones_ibfk_2` FOREIGN KEY (`id_cancion`) REFERENCES `canciones` (`id_cancion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`username`) REFERENCES `personas` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios_gustando_canciones`
--
ALTER TABLE `usuarios_gustando_canciones`
  ADD CONSTRAINT `usuarios_gustando_canciones_ibfk_1` FOREIGN KEY (`username`) REFERENCES `usuarios` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuarios_gustando_canciones_ibfk_2` FOREIGN KEY (`id_cancion`) REFERENCES `canciones` (`id_cancion`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
