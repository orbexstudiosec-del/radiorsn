<?php
require_once __DIR__ . '/../../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function estaLogueado(): bool
{
    return !empty($_SESSION['admin_id']);
}

function requireLogin(): void
{
    if (!estaLogueado()) {
        header('Location: index.php');
        exit;
    }
}

/** Genera (o reutiliza) el token CSRF de la sesión actual */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Verifica el token CSRF recibido por POST; corta la ejecución si no coincide */
function verificarCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        die('Token de seguridad inválido. Recarga la página e inténtalo de nuevo.');
    }
}
