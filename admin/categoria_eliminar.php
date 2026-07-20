<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: categorias.php');
    exit;
}

verificarCsrf();

$id = (int)($_POST['id'] ?? 0);

if ($id) {
    $pdo = getPDO();
    $stmt = $pdo->prepare('DELETE FROM categorias WHERE id = ?');
    $stmt->execute([$id]);
    $_SESSION['flash'] = 'Categoría eliminada. Las noticias que la usaban quedaron sin categoría.';
}

header('Location: categorias.php');
exit;
