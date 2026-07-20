<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: categorias.php');
    exit;
}

verificarCsrf();

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$nombre = trim($_POST['nombre'] ?? '');
$color = trim($_POST['color'] ?? '#f7941d');

if ($nombre === '') {
    $_SESSION['flash_error'] = 'El nombre de la categoría es obligatorio.';
    header('Location: categoria_form.php' . ($id ? '?id=' . $id : ''));
    exit;
}

if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
    $color = '#f7941d';
}

$pdo = getPDO();

if ($id) {
    if (!getCategoriaPorId($id)) {
        header('Location: categorias.php');
        exit;
    }
    $slug = slugUnicoCategoria($nombre, $id);
    $stmt = $pdo->prepare('UPDATE categorias SET nombre=?, slug=?, color=? WHERE id=?');
    $stmt->execute([$nombre, $slug, $color, $id]);
    $_SESSION['flash'] = 'Categoría actualizada.';
} else {
    $slug = slugUnicoCategoria($nombre);
    $stmt = $pdo->prepare('INSERT INTO categorias (nombre, slug, color) VALUES (?, ?, ?)');
    $stmt->execute([$nombre, $slug, $color]);
    $_SESSION['flash'] = 'Categoría creada.';
}

header('Location: categorias.php');
exit;
