<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: sliders.php');
    exit;
}

verificarCsrf();

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$titulo = trim($_POST['titulo'] ?? '');
$subtitulo = trim($_POST['subtitulo'] ?? '');
$botonTexto = trim($_POST['boton_texto'] ?? '');
$botonEnlace = trim($_POST['boton_enlace'] ?? '');
$claseFondo = $_POST['clase_fondo'] ?? 'slide-bg-1';
$orden = (int)($_POST['orden'] ?? 0);
$activo = isset($_POST['activo']) ? 1 : 0;

$volverA = 'slider_form.php' . ($id ? '?id=' . $id : '');

if ($titulo === '') {
    $_SESSION['flash'] = 'El título es obligatorio.';
    header('Location: ' . $volverA);
    exit;
}

if (!in_array($claseFondo, SLIDER_FONDOS, true)) {
    $claseFondo = 'slide-bg-1';
}

// El botón necesita texto Y enlace juntos, o ninguno de los dos.
if (($botonTexto === '') !== ($botonEnlace === '')) {
    $_SESSION['flash'] = 'Si agregas un botón, necesita texto y enlace. Si no, deja ambos vacíos.';
    header('Location: ' . $volverA);
    exit;
}

$pdo = getPDO();
$imagenNombre = null;

if ($id) {
    $existente = getSliderPorId($id);
    if (!$existente) {
        header('Location: sliders.php');
        exit;
    }
    $imagenNombre = $existente['imagen'];
}

// ---- Subida de imagen (opcional) ----
if (!empty($_FILES['imagen']['name']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $permitidas = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
    $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
    $tamanoMax = 4 * 1024 * 1024; // 4MB

    if (!array_key_exists($ext, $permitidas)) {
        $_SESSION['flash'] = 'Formato de imagen no permitido. Usa JPG, PNG o WEBP.';
        header('Location: ' . $volverA);
        exit;
    }
    if ($_FILES['imagen']['size'] > $tamanoMax) {
        $_SESSION['flash'] = 'La imagen supera el tamaño máximo de 4MB.';
        header('Location: ' . $volverA);
        exit;
    }

    $tipoReal = mime_content_type($_FILES['imagen']['tmp_name']);
    if ($tipoReal !== $permitidas[$ext]) {
        $_SESSION['flash'] = 'El archivo no parece ser una imagen válida.';
        header('Location: ' . $volverA);
        exit;
    }

    $carpetaDestino = __DIR__ . '/../uploads/sliders/';
    $nuevoNombre = bin2hex(random_bytes(12)) . '.' . $ext;

    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $carpetaDestino . $nuevoNombre)) {
        if ($imagenNombre && is_file($carpetaDestino . $imagenNombre)) {
            @unlink($carpetaDestino . $imagenNombre);
        }
        $imagenNombre = $nuevoNombre;
    }
}

if ($id) {
    $stmt = $pdo->prepare('UPDATE sliders SET titulo=?, subtitulo=?, boton_texto=?, boton_enlace=?, imagen=?, clase_fondo=?, orden=?, activo=? WHERE id=?');
    $stmt->execute([$titulo, $subtitulo, $botonTexto ?: null, $botonEnlace ?: null, $imagenNombre, $claseFondo, $orden, $activo, $id]);
    $_SESSION['flash'] = 'Diapositiva actualizada.';
} else {
    $stmt = $pdo->prepare('INSERT INTO sliders (titulo, subtitulo, boton_texto, boton_enlace, imagen, clase_fondo, orden, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$titulo, $subtitulo, $botonTexto ?: null, $botonEnlace ?: null, $imagenNombre, $claseFondo, $orden, $activo]);
    $_SESSION['flash'] = 'Diapositiva creada.';
}

header('Location: sliders.php');
exit;
