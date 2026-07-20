<?php
require_once __DIR__ . '/db.php';

/** Escapa texto para salida segura en HTML */
function h($str): string
{
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

/** Convierte texto en slug URL-friendly */
function slugify(string $texto): string
{
    $texto = trim($texto);
    // transliteración básica de acentos
    $map = [
        'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n','ü'=>'u',
        'Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ú'=>'u','Ñ'=>'n','Ü'=>'u',
    ];
    $texto = strtr($texto, $map);
    $texto = strtolower($texto);
    $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
    $texto = trim($texto, '-');
    if ($texto === '') {
        $texto = 'noticia-' . time();
    }
    return $texto;
}

/**
 * Limpia el HTML que produce el editor de contenido (Quill) antes de guardarlo:
 * solo deja las etiquetas que el propio editor puede generar, y quita atributos
 * de evento (onclick, etc.) y enlaces "javascript:" por si se pegó HTML de otra parte.
 */
function sanitizarHtmlNoticia(string $html): string
{
    // Quita etiquetas peligrosas junto con su contenido interno (strip_tags por sí
    // solo borraría solo la etiqueta y dejaría el texto de adentro visible).
    $html = preg_replace('#<(script|style|iframe|object|embed)[^>]*>.*?</\1>#is', '', $html);

    $permitidas = '<p><br><strong><em><u><s><h2><h3><blockquote><ol><ul><li><a>';
    $html = strip_tags($html, $permitidas);
    $html = preg_replace('/\son\w+\s*=\s*(".*?"|\'.*?\')/i', '', $html);
    $html = preg_replace('/(href)\s*=\s*(["\'])\s*javascript:[^"\']*\2/i', '$1="#"', $html);
    return trim($html);
}

/**
 * Prepara el contenido de una noticia para mostrarse como HTML: si ya es HTML
 * (noticias creadas con el editor), lo deja igual. Si es texto plano de una
 * noticia antigua (sin etiquetas), lo convierte a párrafos automáticamente.
 */
function contenidoComoHtml(string $contenido): string
{
    if (strpos($contenido, '<') === false) {
        $parrafos = preg_split('/\n{2,}/', trim($contenido));
        $html = '';
        foreach ($parrafos as $parrafo) {
            $html .= '<p>' . nl2br(h(trim($parrafo))) . '</p>';
        }
        return $html;
    }
    return $contenido;
}

/** Genera un slug único para la tabla noticias, evitando choques */
function slugUnico(string $base, ?int $ignorarId = null): string
{
    $pdo = getPDO();
    $slug = slugify($base);
    $original = $slug;
    $i = 2;
    while (true) {
        if ($ignorarId) {
            $stmt = $pdo->prepare('SELECT id FROM noticias WHERE slug = ? AND id != ?');
            $stmt->execute([$slug, $ignorarId]);
        } else {
            $stmt = $pdo->prepare('SELECT id FROM noticias WHERE slug = ?');
            $stmt->execute([$slug]);
        }
        if (!$stmt->fetch()) {
            return $slug;
        }
        $slug = $original . '-' . $i;
        $i++;
    }
}

/** Obtiene noticias (con su categoría), más recientes primero. Filtra por categoría si se indica el slug. */
function getNoticias(?int $limite = null, bool $soloPublicadas = true, ?string $categoriaSlug = null): array
{
    $pdo = getPDO();
    $sql = 'SELECT n.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug, c.color AS categoria_color
            FROM noticias n
            LEFT JOIN categorias c ON c.id = n.categoria_id';
    $condiciones = [];
    $parametros = [];
    if ($soloPublicadas) {
        $condiciones[] = 'n.publicado = 1';
    }
    if ($categoriaSlug !== null) {
        $condiciones[] = 'c.slug = ?';
        $parametros[] = $categoriaSlug;
    }
    if ($condiciones) {
        $sql .= ' WHERE ' . implode(' AND ', $condiciones);
    }
    $sql .= ' ORDER BY n.creado_en DESC';
    if ($limite !== null) {
        $sql .= ' LIMIT ' . (int)$limite;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($parametros);
    return $stmt->fetchAll();
}

/** Obtiene todas las noticias (para el panel admin) */
function getTodasNoticias(): array
{
    return getNoticias(null, false);
}

function getNoticiaPorSlug(string $slug): ?array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT n.*, c.nombre AS categoria_nombre, c.slug AS categoria_slug, c.color AS categoria_color
                            FROM noticias n
                            LEFT JOIN categorias c ON c.id = n.categoria_id
                            WHERE n.slug = ? AND n.publicado = 1');
    $stmt->execute([$slug]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function getNoticiaPorId(int $id): ?array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM noticias WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/** Todas las categorías de noticias, ordenadas alfabéticamente */
function getCategorias(): array
{
    $pdo = getPDO();
    return $pdo->query('SELECT * FROM categorias ORDER BY nombre ASC')->fetchAll();
}

function getCategoriaPorSlug(string $slug): ?array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM categorias WHERE slug = ?');
    $stmt->execute([$slug]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function getCategoriaPorId(int $id): ?array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM categorias WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/** Genera un slug único para la tabla categorias, evitando choques */
function slugUnicoCategoria(string $base, ?int $ignorarId = null): string
{
    $pdo = getPDO();
    $slug = slugify($base);
    $original = $slug;
    $i = 2;
    while (true) {
        if ($ignorarId) {
            $stmt = $pdo->prepare('SELECT id FROM categorias WHERE slug = ? AND id != ?');
            $stmt->execute([$slug, $ignorarId]);
        } else {
            $stmt = $pdo->prepare('SELECT id FROM categorias WHERE slug = ?');
            $stmt->execute([$slug]);
        }
        if (!$stmt->fetch()) {
            return $slug;
        }
        $slug = $original . '-' . $i;
        $i++;
    }
}

/** Diapositivas del slider del home, activas y ordenadas */
function getSliders(bool $soloActivos = true): array
{
    $pdo = getPDO();
    $sql = 'SELECT * FROM sliders';
    if ($soloActivos) {
        $sql .= ' WHERE activo = 1';
    }
    $sql .= ' ORDER BY orden ASC, id ASC';
    return $pdo->query($sql)->fetchAll();
}

function getSliderPorId(int $id): ?array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM sliders WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/** Clases de degradado disponibles para el fondo del slider cuando no hay imagen */
const SLIDER_FONDOS = ['slide-bg-1', 'slide-bg-2', 'slide-bg-3', 'slide-bg-4'];

/** Formatea una fecha MySQL a formato legible en español */
function fechaLegible(string $fechaMysql): string
{
    $meses = [1=>'ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
    $ts = strtotime($fechaMysql);
    return date('d', $ts) . ' ' . $meses[(int)date('n', $ts)] . ' ' . date('Y', $ts);
}

/** Devuelve true si la petición actual es una carga AJAX de contenido parcial */
function esPeticionAjax(): bool
{
    return isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
}

/** Recorta un texto a $limite caracteres sin cortar palabras */
function recortarTexto(string $texto, int $limite = 160): string
{
    $texto = trim(strip_tags($texto));
    if (mb_strlen($texto) <= $limite) {
        return $texto;
    }
    $recortado = mb_substr($texto, 0, $limite);
    $recortado = mb_substr($recortado, 0, mb_strrpos($recortado, ' '));
    return $recortado . '…';
}

/** Nombres de los días de la semana, índice 1=Lunes..7=Domingo (igual que date('N')) */
const DIAS_SEMANA = [
    1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves',
    5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo',
];

/** Programación agrupada por día (1..7), solo franjas activas, ordenadas por hora */
function getProgramacionPorDia(): array
{
    $pdo = getPDO();
    $filas = $pdo->query('SELECT * FROM programacion WHERE activo = 1 ORDER BY dia_semana ASC, hora_inicio ASC')->fetchAll();

    $porDia = [];
    foreach (DIAS_SEMANA as $numero => $nombre) {
        $porDia[$numero] = [];
    }
    foreach ($filas as $fila) {
        $porDia[(int)$fila['dia_semana']][] = $fila;
    }
    return $porDia;
}

/** Todas las franjas de programación (activas e inactivas), para el panel admin */
function getTodaLaProgramacion(): array
{
    $pdo = getPDO();
    return $pdo->query('SELECT * FROM programacion ORDER BY dia_semana ASC, hora_inicio ASC')->fetchAll();
}

function getProgramaPorId(int $id): ?array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM programacion WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/** Formatea HH:MM:SS de MySQL a "6:00 a.m." */
function horaLegible(string $horaMysql): string
{
    $ts = strtotime($horaMysql);
    $formato = date('g:i', $ts) . ' ' . (date('a', $ts) === 'am' ? 'a.m.' : 'p.m.');
    return $formato;
}

/** Devuelve el id (1..7) del programa que está al aire ahora mismo, o null */
function programaAlAireAhora(array $franja): bool
{
    $hoy = (int)date('N');
    if ((int)$franja['dia_semana'] !== $hoy) {
        return false;
    }
    $ahora = date('H:i:s');
    return $ahora >= $franja['hora_inicio'] && $ahora <= $franja['hora_fin'];
}

/* ============================================================
 *  Estadísticas de visitas (para el dashboard del panel admin)
 * ============================================================ */

/** Identificador anónimo y estable para la sesión actual del visitante */
function idSesionVisitante(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['visita_sid'])) {
        $_SESSION['visita_sid'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['visita_sid'];
}

/** IP real del visitante, considerando que el sitio pueda estar detrás de un proxy/CDN */
function obtenerIpVisitante(): string
{
    $candidatas = [];
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $candidatas[] = $_SERVER['HTTP_CF_CONNECTING_IP']; // Cloudflare
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Puede traer una lista "cliente, proxy1, proxy2": la primera es la real.
        $partes = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $candidatas[] = trim($partes[0]);
    }
    if (!empty($_SERVER['REMOTE_ADDR'])) {
        $candidatas[] = $_SERVER['REMOTE_ADDR'];
    }
    foreach ($candidatas as $ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }
    return '0.0.0.0';
}

/**
 * Registra una visita a una página pública. Se llama una vez por carga
 * (incluidas las cargas AJAX de navegación interna) desde cada página pública.
 * Si falla (ej. base de datos caída) no debe interrumpir la carga del sitio.
 */
function registrarVisita(string $pagina, ?string $referencia = null): void
{
    try {
        $pdo = getPDO();
        $ipHash = hash('sha256', obtenerIpVisitante());
        $stmt = $pdo->prepare('INSERT INTO visitas (pagina, referencia, sesion_id, ip_hash) VALUES (?, ?, ?, ?)');
        $stmt->execute([$pagina, $referencia, idSesionVisitante(), $ipHash]);
    } catch (Throwable $e) {
        // Silencioso a propósito: una falla al registrar la visita no debe romper la página.
    }
}

/** Visitantes distintos (por IP) con actividad en los últimos $minutos (aproximación de "en vivo") */
function getVisitantesEnVivo(int $minutos = 5): int
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT COUNT(DISTINCT ip_hash) FROM visitas WHERE creado_en >= DATE_SUB(NOW(), INTERVAL ? MINUTE)');
    $stmt->execute([$minutos]);
    return (int)$stmt->fetchColumn();
}

/** Total de páginas vistas desde el inicio del día de hoy */
function getVisitasHoy(): int
{
    $pdo = getPDO();
    return (int)$pdo->query('SELECT COUNT(*) FROM visitas WHERE DATE(creado_en) = CURDATE()')->fetchColumn();
}

/** Total de páginas vistas en los últimos $dias días (incluye hoy) */
function getVisitasUltimosDias(int $dias = 7): int
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM visitas WHERE creado_en >= DATE_SUB(CURDATE(), INTERVAL ? DAY)');
    $stmt->execute([$dias - 1]);
    return (int)$stmt->fetchColumn();
}

/** Visitas por día en los últimos $dias días, para un mini gráfico. Devuelve [['fecha'=>'2026-07-14','total'=>12], ...] siempre con todos los días presentes. */
function getVisitasPorDia(int $dias = 7): array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT DATE(creado_en) AS fecha, COUNT(*) AS total
                            FROM visitas
                            WHERE creado_en >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                            GROUP BY DATE(creado_en)");
    $stmt->execute([$dias - 1]);
    $porFecha = [];
    foreach ($stmt->fetchAll() as $fila) {
        $porFecha[$fila['fecha']] = (int)$fila['total'];
    }

    $resultado = [];
    for ($i = $dias - 1; $i >= 0; $i--) {
        $fecha = date('Y-m-d', strtotime("-{$i} days"));
        $resultado[] = ['fecha' => $fecha, 'total' => $porFecha[$fecha] ?? 0];
    }
    return $resultado;
}

/** Noticias más vistas en los últimos $dias días, con su título y conteo de vistas */
function getNoticiasMasVistas(int $limite = 5, int $dias = 30): array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT n.titulo, n.slug, COUNT(v.id) AS vistas
                            FROM visitas v
                            INNER JOIN noticias n ON n.slug = v.referencia
                            WHERE v.pagina = 'noticia' AND v.creado_en >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                            GROUP BY n.id, n.titulo, n.slug
                            ORDER BY vistas DESC
                            LIMIT " . (int)$limite);
    $stmt->execute([$dias]);
    return $stmt->fetchAll();
}

/** Cuenta total de filas de una tabla (uso interno del dashboard) */
function contarFilas(string $tabla, string $condicion = ''): int
{
    $tablasPermitidas = ['noticias', 'programacion', 'categorias', 'visitas'];
    if (!in_array($tabla, $tablasPermitidas, true)) {
        return 0;
    }
    $pdo = getPDO();
    $sql = "SELECT COUNT(*) FROM {$tabla}";
    if ($condicion !== '') {
        $sql .= ' WHERE ' . $condicion;
    }
    return (int)$pdo->query($sql)->fetchColumn();
}
