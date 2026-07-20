<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: noticias.php');
    exit;
}

verificarCsrf();

$id = (int)($_POST['id'] ?? 0);
$noticia = $id ? getNoticiaPorId($id) : null;

if ($noticia) {
    $pdo = getPDO();
    $stmt = $pdo->prepare('DELETE FROM noticias WHERE id = ?');
    $stmt->execute([$id]);

    if (!empty($noticia['imagen'])) {
        $ruta = __DIR__ . '/../uploads/noticias/' . $noticia['imagen'];
        if (is_file($ruta)) {
            @unlink($ruta);
        }
    }
    $_SESSION['flash'] = 'Noticia eliminada.';
}

header('Location: noticias.php');
exit;
