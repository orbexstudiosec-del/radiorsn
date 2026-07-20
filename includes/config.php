<?php
/**
 * Configuración general del sitio.
 * Edita estos valores según tu servidor cPanel y tu emisora.
 *
 * Para desarrollo en tu propia máquina, en vez de editar los valores de
 * abajo crea includes/config.local.php (ese archivo NO debe subirse a
 * cPanel) con los define() que quieras sobreescribir, por ejemplo la
 * base de datos local. includes/.htaccess ya bloquea el acceso web a
 * esta carpeta completa.
 */
if (file_exists(__DIR__ . '/config.local.php')) {
    require __DIR__ . '/config.local.php';
}

// ---- Base de datos (datos que te da cPanel al crear la BD MySQL) ----
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'cpaneluser_radiorsn');
if (!defined('DB_USER')) define('DB_USER', 'cpaneluser_radiorsn');
if (!defined('DB_PASS')) define('DB_PASS', 'CAMBIA_ESTA_CONTRASENA');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

// ---- Datos de la emisora ----
if (!defined('SITE_NAME')) define('SITE_NAME', 'Radio RSN');
if (!defined('SITE_SLOGAN')) define('SITE_SLOGAN', 'Radio Señal del Norte — Deportes y Noticias las 24 horas');
if (!defined('SITE_URL')) define('SITE_URL', 'https://www.radiorsn.com'); // cambia por tu dominio real

// ---- Stream de audio ----
// Reemplaza por la URL real de tu stream Shoutcast/Icecast cuando la tengas.
if (!defined('STREAM_URL')) define('STREAM_URL', 'https://stream.zeno.fm/f3wvbbqmdg8uv');
if (!defined('STREAM_TITLE_URL')) define('STREAM_TITLE_URL', ''); // opcional: endpoint que devuelve el título de la canción actual (JSON), déjalo vacío si no aplica

// ---- Quiénes somos ----
if (!defined('SITE_QUIENES_SOMOS')) define('SITE_QUIENES_SOMOS',
    'Radio RSN (Señal del Norte) es una emisora dedicada al deporte y a la noticia, ' .
    'acompañando a nuestra audiencia las 24 horas del día con información veraz, ' .
    'análisis deportivo y el mejor ambiente radial. Desde nuestros estudios llevamos ' .
    'la voz de la comunidad a cada rincón, combinando periodismo responsable con la ' .
    'pasión por el deporte que nos caracteriza.'
);
if (!defined('SITE_DESCRIPCION_FOOTER'))
    define('SITE_DESCRIPCION_FOOTER', 'Tu emisora de deportes y noticias las 24 horas. Información veraz, análisis deportivo y la mejor compañía, en vivo y desde cualquier lugar.');
if (!defined('SITE_MISION'))
    define('SITE_MISION', 'Informar y entretener a nuestra audiencia con contenido deportivo y noticioso veraz, oportuno y de calidad, las 24 horas del día, siendo un puente confiable entre la comunidad y la actualidad.');
if (!defined('SITE_VISION'))
    define('SITE_VISION', 'Consolidarnos como la radio de deportes y noticias líder de la región, referente de credibilidad e innovación, y un espacio donde la comunidad encuentre siempre información confiable y contenido de calidad.');

// ---- Redes sociales ----
if (!defined('SOCIAL_FACEBOOK')) define('SOCIAL_FACEBOOK', 'https://facebook.com/radiorsn');
if (!defined('SOCIAL_INSTAGRAM')) define('SOCIAL_INSTAGRAM', 'https://instagram.com/radiorsn');
if (!defined('SOCIAL_WHATSAPP')) define('SOCIAL_WHATSAPP', 'https://wa.me/593999999999');
if (!defined('SOCIAL_YOUTUBE')) define('SOCIAL_YOUTUBE', 'https://youtube.com/@radiorsn');
if (!defined('SOCIAL_TIKTOK')) define('SOCIAL_TIKTOK', 'https://tiktok.com/@radiorsn');
if (!defined('SOCIAL_X')) define('SOCIAL_X', 'https://x.com/radiorsn');

// ---- Zona horaria ----
date_default_timezone_set('America/Guayaquil');

// ---- Errores (ponlo en false cuando el sitio esté en producción) ----
if (!defined('APP_DEBUG')) define('APP_DEBUG', false);
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
}
