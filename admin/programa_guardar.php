<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: programacion.php');
    exit;
}

verificarCsrf();

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$programa = trim($_POST['programa'] ?? '');
$conductor = trim($_POST['conductor'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$diaSemana = (int)($_POST['dia_semana'] ?? 0);
$horaInicio = $_POST['hora_inicio'] ?? '';
$horaFin = $_POST['hora_fin'] ?? '';
$activo = isset($_POST['activo']) ? 1 : 0;

$volverA = 'programa_form.php' . ($id ? '?id=' . $id : '');

if ($programa === '' || $diaSemana < 1 || $diaSemana > 7) {
    $_SESSION['flash'] = 'El nombre del programa y el día son obligatorios.';
    header('Location: ' . $volverA);
    exit;
}

if (!preg_match('/^\d{2}:\d{2}$/', $horaInicio) || !preg_match('/^\d{2}:\d{2}$/', $horaFin)) {
    $_SESSION['flash'] = 'Las horas de inicio y fin son obligatorias.';
    header('Location: ' . $volverA);
    exit;
}

if ($horaFin <= $horaInicio) {
    $_SESSION['flash'] = 'La hora de fin debe ser posterior a la hora de inicio.';
    header('Location: ' . $volverA);
    exit;
}

$pdo = getPDO();

if ($id) {
    if (!getProgramaPorId($id)) {
        header('Location: programacion.php');
        exit;
    }
    $stmt = $pdo->prepare('UPDATE programacion SET programa=?, conductor=?, descripcion=?, dia_semana=?, hora_inicio=?, hora_fin=?, activo=? WHERE id=?');
    $stmt->execute([$programa, $conductor, $descripcion, $diaSemana, $horaInicio, $horaFin, $activo, $id]);
    $_SESSION['flash'] = 'Franja de programación actualizada.';
} else {
    $stmt = $pdo->prepare('INSERT INTO programacion (programa, conductor, descripcion, dia_semana, hora_inicio, hora_fin, activo) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$programa, $conductor, $descripcion, $diaSemana, $horaInicio, $horaFin, $activo]);
    $_SESSION['flash'] = 'Franja de programación creada.';
}

header('Location: programacion.php');
exit;
