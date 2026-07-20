<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: sliders.php');
    exit;
}

verificarCsrf();

$id = (int)($_POST['id'] ?? 0);
$slider = $id ? getSliderPorId($id) : null;

if ($slider) {
    $pdo = getPDO();
    $stmt = $pdo->prepare('DELETE FROM sliders WHERE id = ?');
    $stmt->execute([$id]);

    if (!empty($slider['imagen'])) {
        $ruta = __DIR__ . '/../uploads/sliders/' . $slider['imagen'];
        if (is_file($ruta)) {
            @unlink($ruta);
        }
    }
    $_SESSION['flash'] = 'Diapositiva eliminada.';
}

header('Location: sliders.php');
exit;
