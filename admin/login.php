<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

verificarCsrf();

// Freno simple ante intentos repetidos de fuerza bruta
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if ($_SESSION['login_attempts'] >= 5) {
    usleep(1500000);
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$pdo = getPDO();
$stmt = $pdo->prepare('SELECT * FROM admin_users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['login_attempts'] = 0;
    session_regenerate_id(true);
    $_SESSION['admin_id'] = $user['id'];
    $_SESSION['admin_username'] = $user['username'];
    header('Location: dashboard.php');
    exit;
}

$_SESSION['login_attempts']++;
$_SESSION['login_error'] = 'Usuario o contraseña incorrectos.';
header('Location: index.php');
exit;
