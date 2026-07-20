<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: noticias.php');
    exit;
}

verificarCsrf();

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$titulo = trim($_POST['titulo'] ?? '');
$resumen = trim($_POST['resumen'] ?? '');
$contenido = sanitizarHtmlNoticia($_POST['contenido'] ?? '');
$publicado = isset($_POST['publicado']) ? 1 : 0;
$categoriaId = !empty($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : null;
if ($categoriaId !== null && !getCategoriaPorId($categoriaId)) {
    $categoriaId = null;
}

if ($titulo === '' || trim(strip_tags($contenido)) === '') {
    $_SESSION['flash'] = 'El título y el contenido son obligatorios.';
    header('Location: noticia_form.php' . ($id ? '?id=' . $id : ''));
    exit;
}

$pdo = getPDO();
$imagenNombre = null;

if ($id) {
    $existente = getNoticiaPorId($id);
    if (!$existente) {
        header('Location: noticias.php');
        exit;
    }
    $imagenNombre = $existente['imagen'];
}

// ---- Subida de imagen (opcional) ----
if (!empty($_FILES['imagen']['name']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $permitidas = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
    $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
    $tamanoMax = 3 * 1024 * 1024; // 3MB

    if (!array_key_exists($ext, $permitidas)) {
        $_SESSION['flash'] = 'Formato de imagen no permitido. Usa JPG, PNG o WEBP.';
        header('Location: noticia_form.php' . ($id ? '?id=' . $id : ''));
        exit;
    }
    if ($_FILES['imagen']['size'] > $tamanoMax) {
        $_SESSION['flash'] = 'La imagen supera el tamaño máximo de 3MB.';
        header('Location: noticia_form.php' . ($id ? '?id=' . $id : ''));
        exit;
    }

    $tipoReal = mime_content_type($_FILES['imagen']['tmp_name']);
    if ($tipoReal !== $permitidas[$ext]) {
        $_SESSION['flash'] = 'El archivo no parece ser una imagen válida.';
        header('Location: noticia_form.php' . ($id ? '?id=' . $id : ''));
        exit;
    }

    $carpetaDestino = __DIR__ . '/../uploads/noticias/';
    $nuevoNombre = bin2hex(random_bytes(12)) . '.' . $ext;

    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $carpetaDestino . $nuevoNombre)) {
        // Elimina la imagen anterior si existía
        if ($imagenNombre && is_file($carpetaDestino . $imagenNombre)) {
            @unlink($carpetaDestino . $imagenNombre);
        }
        $imagenNombre = $nuevoNombre;
    }
}

if ($id) {
    $slug = slugUnico($titulo, $id);
    $stmt = $pdo->prepare('UPDATE noticias SET titulo=?, slug=?, resumen=?, contenido=?, imagen=?, categoria_id=?, publicado=? WHERE id=?');
    $stmt->execute([$titulo, $slug, $resumen, $contenido, $imagenNombre, $categoriaId, $publicado, $id]);
    $_SESSION['flash'] = 'Noticia actualizada correctamente.';
} else {
    $slug = slugUnico($titulo);
    $stmt = $pdo->prepare('INSERT INTO noticias (titulo, slug, resumen, contenido, imagen, categoria_id, publicado) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$titulo, $slug, $resumen, $contenido, $imagenNombre, $categoriaId, $publicado]);
    $_SESSION['flash'] = 'Noticia creada correctamente.';
}

header('Location: noticias.php');
exit;
