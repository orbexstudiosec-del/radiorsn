-- Esquema de base de datos para Radio RSN
-- Impórtalo desde phpMyAdmin en tu cPanel, dentro de la base de datos
-- que hayas creado y que coincide con includes/config.php

CREATE TABLE IF NOT EXISTS admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL,
  slug VARCHAR(80) NOT NULL UNIQUE,
  color VARCHAR(7) NOT NULL DEFAULT '#f7941d', -- color hexadecimal para la insignia
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS noticias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  resumen TEXT,
  contenido LONGTEXT NOT NULL,
  imagen VARCHAR(255) DEFAULT NULL,
  categoria_id INT DEFAULT NULL,
  publicado TINYINT(1) NOT NULL DEFAULT 1,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_noticias_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL,
  INDEX idx_categoria (categoria_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS programacion (
  id INT AUTO_INCREMENT PRIMARY KEY,
  programa VARCHAR(150) NOT NULL,
  conductor VARCHAR(150) DEFAULT NULL,
  descripcion TEXT,
  dia_semana TINYINT NOT NULL, -- 1=Lunes ... 7=Domingo (ISO-8601, igual que date('N') en PHP)
  hora_inicio TIME NOT NULL,
  hora_fin TIME NOT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_dia (dia_semana)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS sliders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(150) NOT NULL,
  subtitulo VARCHAR(255) DEFAULT NULL,
  boton_texto VARCHAR(60) DEFAULT NULL,
  boton_enlace VARCHAR(255) DEFAULT NULL,
  imagen VARCHAR(255) DEFAULT NULL,          -- si está vacío se usa clase_fondo (degradado)
  clase_fondo VARCHAR(30) NOT NULL DEFAULT 'slide-bg-1',
  orden INT NOT NULL DEFAULT 0,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS visitas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pagina VARCHAR(50) NOT NULL,       -- inicio, noticias, noticia, programacion, quienes-somos
  referencia VARCHAR(255) DEFAULT NULL, -- ej: slug de la noticia vista (para "más leídas")
  sesion_id VARCHAR(64) NOT NULL,    -- identificador anónimo de la sesión del visitante (cookie)
  ip_hash CHAR(64) DEFAULT NULL,     -- hash de la IP del visitante (así se cuenta "en vivo" por IP, sin guardar la IP real)
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_creado (creado_en),
  INDEX idx_sesion (sesion_id),
  INDEX idx_ip (ip_hash),
  INDEX idx_pagina (pagina)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Datos: esto es una foto exacta del contenido tal como está
-- funcionando ahora mismo (noticias, categorías, programación,
-- slider e imágenes ya subidas). Al importarlo, tu sitio en cPanel
-- va a quedar igual a como lo ves en local.
--
-- Usuario del panel admin: admin
-- Si nunca cambiaste la contraseña de prueba, sigue siendo: admin123
-- (cámbiala desde "Mi perfil" apenas entres, por seguridad).
-- ============================================================

INSERT INTO `admin_users` (`id`, `username`, `password_hash`, `creado_en`) VALUES (1,'admin','$2y$12$Mi9Ko9SnqeTDQ0V9H0W/Ke1Ts.2l/dzZOjG.V.JoA0MvaGwCirRqC','2026-07-20 17:33:22');

INSERT INTO `categorias` (`id`, `nombre`, `slug`, `color`, `creado_en`) VALUES (1,'Deportes','deportes','#f7941d','2026-07-20 20:08:50'),(2,'Noticias','noticias','#e2231a','2026-07-20 20:08:50'),(3,'Entretenimiento','entretenimiento','#6f42c1','2026-07-20 20:08:50'),(4,'Comunidad','comunidad','#1d3557','2026-07-20 20:08:50');

INSERT INTO `noticias` (`id`, `titulo`, `slug`, `resumen`, `contenido`, `imagen`, `categoria_id`, `publicado`, `creado_en`, `actualizado_en`) VALUES (1,'¡Bienvenidos al nuevo sitio de Radio RSN!','bienvenidos-al-nuevo-sitio-de-radio-rsn','Estrenamos página web con reproductor en vivo, noticias y mucho más.','Estamos muy contentos de presentar el nuevo sitio web de Radio RSN. Aquí podrás escuchar nuestra señal en vivo en todo momento, mantenerte al día con las últimas noticias y seguirnos en todas nuestras redes sociales. ¡Gracias por acompañarnos!',NULL,2,1,'2026-07-20 19:17:12','2026-07-20 20:08:50'),(2,'Cómo escuchar Radio RSN desde cualquier lugar','como-escuchar-radio-rsn-desde-cualquier-lugar','Te contamos todas las formas en las que puedes sintonizarnos.','Puedes escucharnos directamente desde esta página web usando el reproductor que siempre está disponible en la parte inferior de la pantalla, sin importar en qué sección te encuentres navegando.',NULL,4,1,'2026-07-20 19:17:12','2026-07-20 21:58:25'),(3,'Nuestro equipo analiza la fecha del campeonato local','equipo-analiza-fecha-campeonato-local','Repasamos los resultados y lo que se viene en la liga local.','El equipo de Zona Deportiva analizó los resultados de la última fecha del campeonato local, con entrevistas a jugadores y cuerpo técnico. No te pierdas el resumen completo en nuestra programación deportiva.',NULL,1,1,'2026-07-20 20:08:50','2026-07-20 20:08:50'),(4,'Los estrenos musicales que estamos rotando esta semana','estrenos-musicales-de-la-semana','Descubre los lanzamientos que están sonando en Radio RSN.','Esta semana en Radio RSN te traemos una selección de los estrenos musicales más destacados. Sintonízanos para escucharlos en exclusiva durante toda la programación.',NULL,3,1,'2026-07-20 20:08:50','2026-07-20 20:08:50');

INSERT INTO `programacion` (`id`, `programa`, `conductor`, `descripcion`, `dia_semana`, `hora_inicio`, `hora_fin`, `activo`, `creado_en`) VALUES (1,'Arranque RSN','Equipo RSN','Las noticias más importantes para empezar el día.',1,'06:00:00','09:00:00',1,'2026-07-20 19:17:12'),(2,'Arranque RSN','Equipo RSN','Las noticias más importantes para empezar el día.',2,'06:00:00','09:00:00',1,'2026-07-20 19:17:12'),(3,'Arranque RSN','Equipo RSN','Las noticias más importantes para empezar el día.',3,'06:00:00','09:00:00',1,'2026-07-20 19:17:12'),(4,'Arranque RSN','Equipo RSN','Las noticias más importantes para empezar el día.',4,'06:00:00','09:00:00',1,'2026-07-20 19:17:12'),(5,'Arranque RSN','Equipo RSN','Las noticias más importantes para empezar el día.',5,'06:00:00','09:00:00',1,'2026-07-20 19:17:12'),(6,'Zona Deportiva','Carlos Vera','Análisis y resultados del mundo del deporte.',1,'15:00:00','17:00:00',1,'2026-07-20 19:17:12'),(7,'Zona Deportiva','Carlos Vera','Análisis y resultados del mundo del deporte.',3,'15:00:00','17:00:00',1,'2026-07-20 19:17:12'),(8,'Zona Deportiva','Carlos Vera','Análisis y resultados del mundo del deporte.',5,'15:00:00','17:00:00',1,'2026-07-20 19:17:12'),(9,'Fin de Semana RSN','Equipo RSN','Lo mejor de la semana en un solo programa.',6,'10:00:00','12:00:00',1,'2026-07-20 19:17:12'),(10,'Fin de Semana RSN','Equipo RSN','Lo mejor de la semana en un solo programa.',7,'10:00:00','12:00:00',1,'2026-07-20 19:17:12');

INSERT INTO `sliders` (`id`, `titulo`, `subtitulo`, `boton_texto`, `boton_enlace`, `imagen`, `clase_fondo`, `orden`, `activo`, `creado_en`) VALUES (1,'Bienvenido a Radio RSN','Deportes y noticias las 24 horas, en vivo','Escuchar en vivo','#radioPlayer','5803f662442bd2adec5c3024.png','slide-bg-1',1,1,'2026-07-20 21:06:43'),(2,'Mantente informado','Las últimas noticias de tu ciudad y del mundo','Ver noticias','noticias.php','4082b4888486ad93ae40b256.png','slide-bg-2',2,1,'2026-07-20 21:06:43'),(3,'Síguenos en redes','No te pierdas ningún contenido exclusivo','Nuestras redes','#redes-section','a2ac83271166c8062069e6db.png','slide-bg-3',3,1,'2026-07-20 21:06:43');
