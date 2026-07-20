<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: programacion.php');
    exit;
}

verificarCsrf();

$id = (int)($_POST['id'] ?? 0);

if ($id) {
    $pdo = getPDO();
    $stmt = $pdo->prepare('DELETE FROM programacion WHERE id = ?');
    $stmt->execute([$id]);
    $_SESSION['flash'] = 'Franja de programación eliminada.';
}

header('Location: programacion.php');
exit;
