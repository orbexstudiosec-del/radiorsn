<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['en_vivo' => getVisitantesEnVivo()]);
