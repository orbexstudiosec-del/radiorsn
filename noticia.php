<?php
require_once __DIR__ . '/includes/functions.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$noticia = $slug !== '' ? getNoticiaPorSlug($slug) : null;
registrarVisita('noticia', $noticia ? $noticia['slug'] : null);

if (!$noticia) {
    http_response_code(404);
    $pageTitle = 'Noticia no encontrada - ' . SITE_NAME;
    ob_start();
    ?>
    <section class="container py-4 py-md-5 text-center">
      <h1 class="section-title d-inline-block">Noticia no encontrada</h1>
      <p class="text-muted">La noticia que buscas no existe o fue eliminada.</p>
      <a href="noticias.php" class="btn btn-outline-dark" data-ajax-link>Volver a noticias</a>
    </section>
    <?php
    $content = ob_get_clean();
} else {
    $pageTitle = $noticia['titulo'] . ' - ' . SITE_NAME;
    $pageDescription = recortarTexto($noticia['resumen'] ?: $noticia['contenido'], 160);

    $relacionadas = array_filter(getNoticias(4), function ($n) use ($noticia) {
        return $n['id'] != $noticia['id'];
    });
    $relacionadas = array_slice($relacionadas, 0, 3);

    ob_start();
    ?>
    <article class="container py-4 py-md-5" style="max-width: 860px;">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
          <li class="breadcrumb-item"><a href="index.php" data-ajax-link>Inicio</a></li>
          <li class="breadcrumb-item"><a href="noticias.php" data-ajax-link>Noticias</a></li>
          <li class="breadcrumb-item active text-truncate" style="max-width:250px;"><?= h($noticia['titulo']) ?></li>
        </ol>
      </nav>

      <div class="d-flex align-items-center gap-2 mb-2">
        <span class="news-date"><?= fechaLegible($noticia['creado_en']) ?></span>
        <?php if (!empty($noticia['categoria_nombre'])): ?>
          <a href="noticias.php?categoria=<?= urlencode($noticia['categoria_slug']) ?>" class="news-cat-badge-inline" style="background:<?= h($noticia['categoria_color']) ?>" data-ajax-link><?= h($noticia['categoria_nombre']) ?></a>
        <?php endif; ?>
      </div>
      <h1 class="article-title fw-bold mb-4"><?= h($noticia['titulo']) ?></h1>

      <?php if (!empty($noticia['imagen'])): ?>
        <img src="uploads/noticias/<?= h($noticia['imagen']) ?>" class="img-fluid rounded mb-4" alt="<?= h($noticia['titulo']) ?>">
      <?php endif; ?>

      <div class="news-body fs-6 fs-md-5">
        <?= contenidoComoHtml($noticia['contenido']) ?>
      </div>

      <hr class="my-5">

      <?php if (!empty($relacionadas)): ?>
        <h4 class="fw-bold mb-3">También te puede interesar</h4>
        <div class="row g-3">
          <?php foreach ($relacionadas as $r): ?>
            <div class="col-md-4">
              <a href="noticia.php?slug=<?= urlencode($r['slug']) ?>" class="text-decoration-none text-dark" data-ajax-link>
                <div class="card news-card">
                  <div class="card-body">
                    <div class="news-date"><?= fechaLegible($r['creado_en']) ?></div>
                    <h6 class="card-title"><?= h($r['titulo']) ?></h6>
                  </div>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </article>
    <?php
    $content = ob_get_clean();
}

if (esPeticionAjax()) {
    header('Content-Type: text/html; charset=utf-8');
    header('X-Page-Title: ' . $pageTitle);
    echo $content;
    exit;
}

include __DIR__ . '/includes/header.php';
echo $content;
include __DIR__ . '/includes/footer.php';
