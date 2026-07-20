<?php
require_once __DIR__ . '/includes/functions.php';
registrarVisita('quienes-somos');

$pageTitle = 'Quiénes somos - ' . SITE_NAME;
$pageDescription = 'Conoce la misión y visión de ' . SITE_NAME;

ob_start();
?>

<section class="about-hero reveal">
  <div class="container text-center">
    <h1 class="section-title d-inline-block">Quiénes somos</h1>
    <p class="about-lead mx-auto"><?= h(SITE_QUIENES_SOMOS) ?></p>
  </div>
</section>

<section class="container py-4 py-md-5 reveal">
  <div class="row g-4">
    <div class="col-md-6">
      <div class="about-card h-100">
        <div class="about-icon"><i class="bi bi-bullseye"></i></div>
        <h2 class="h4 fw-bold">Misión</h2>
        <p class="text-muted mb-0"><?= h(SITE_MISION) ?></p>
      </div>
    </div>
    <div class="col-md-6">
      <div class="about-card h-100">
        <div class="about-icon"><i class="bi bi-binoculars"></i></div>
        <h2 class="h4 fw-bold">Visión</h2>
        <p class="text-muted mb-0"><?= h(SITE_VISION) ?></p>
      </div>
    </div>
  </div>
</section>

<section class="py-4 py-md-5 reveal" style="background:#fff;">
  <div class="container text-center">
    <h2 class="section-title d-inline-block">Escúchanos y síguenos</h2>
    <p class="text-muted mb-4">Vive la programación de <?= h(SITE_NAME) ?> en vivo y en tus redes favoritas.</p>
    <a href="#radioPlayer" class="btn btn-brand btn-lg" data-ajax-link data-play-trigger>Escuchar en vivo</a>
  </div>
</section>

<?php
$content = ob_get_clean();

if (esPeticionAjax()) {
    header('Content-Type: text/html; charset=utf-8');
    header('X-Page-Title: ' . $pageTitle);
    echo $content;
    exit;
}

include __DIR__ . '/includes/header.php';
echo $content;
include __DIR__ . '/includes/footer.php';
