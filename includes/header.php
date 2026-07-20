<?php
/**
 * Cabecera completa del sitio. Solo se incluye en cargas de página completas
 * (no en las peticiones AJAX de navegación interna).
 * Espera opcionalmente $pageTitle y $pageDescription definidas antes de incluirse.
 */
if (!isset($pageTitle)) {
    $pageTitle = SITE_NAME . ' - ' . SITE_SLOGAN;
}
if (!isset($pageDescription)) {
    $pageDescription = SITE_SLOGAN;
}
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($pageTitle) ?></title>
<meta name="description" content="<?= h($pageDescription) ?>">
<link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg?v=3">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<!-- Estilos propios -->
<link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<header class="site-header">
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php" data-ajax-link>
        <img src="assets/img/logo.png" alt="<?= h(SITE_NAME) ?>" class="brand-logo" onerror="this.style.display='none'">
      </a>
      <button class="navbar-toggler hamburger-btn" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-label="Abrir menú">
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
          <li class="nav-item"><a class="nav-link" href="index.php" data-ajax-link>Inicio</a></li>
          <li class="nav-item"><a class="nav-link" href="quienes-somos.php" data-ajax-link>Quiénes somos</a></li>
          <li class="nav-item"><a class="nav-link" href="noticias.php" data-ajax-link>Noticias</a></li>
          <li class="nav-item"><a class="nav-link" href="programacion.php" data-ajax-link>Programación</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php#redes-section" data-ajax-link>Redes sociales</a></li>
          <li class="nav-item d-flex align-items-center gap-2 ms-lg-2 social-icons">
            <a href="<?= h(SOCIAL_FACEBOOK) ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="<?= h(SOCIAL_INSTAGRAM) ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="<?= h(SOCIAL_WHATSAPP) ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
            <a href="<?= h(SOCIAL_YOUTUBE) ?>" target="_blank" rel="noopener" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>

<main id="content">
