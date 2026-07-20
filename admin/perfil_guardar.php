<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: perfil.php');
    exit;
}

verificarCsrf();

$actual = $_POST['actual'] ?? '';
$nueva = $_POST['nueva'] ?? '';
$confirmar = $_POST['confirmar'] ?? '';

$pdo = getPDO();
$stmt = $pdo->prepare('SELECT * FROM admin_users WHERE id = ?');
$stmt->execute([$_SESSION['admin_id']]);
$user = $stmt->fetch();

if (!$user || !password_verify($actual, $user['password_hash'])) {
    $_SESSION['flash_error'] = 'La contraseña actual no es correcta.';
} elseif (strlen($nueva) < 8) {
    $_SESSION['flash_error'] = 'La nueva contraseña debe tener al menos 8 caracteres.';
} elseif ($nueva !== $confirmar) {
    $_SESSION['flash_error'] = 'Las contraseñas nuevas no coinciden.';
} else {
    $hash = password_hash($nueva, PASSWORD_DEFAULT);
    $upd = $pdo->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?');
    $upd->execute([$hash, $user['id']]);
    $_SESSION['flash'] = 'Contraseña actualizada correctamente.';
}

header('Location: perfil.php');
exit;
