<?php
require_once __DIR__ . '/includes/functions.php';
registrarVisita('inicio');

$pageTitle = SITE_NAME . ' - ' . SITE_SLOGAN;
$pageDescription = SITE_SLOGAN;

$sliders = getSliders();
$ultimasNoticias = getNoticias(6);
$noticiaDestacada = $ultimasNoticias ? array_shift($ultimasNoticias) : null;
$programacionHoy = getProgramacionPorDia()[(int)date('N')];
$programaEnVivo = null;
foreach ($programacionHoy as $franja) {
    if (programaAlAireAhora($franja)) {
        $programaEnVivo = $franja;
        break;
    }
}

ob_start();
?>

<!-- ===== SLIDER ===== -->
<?php if (!empty($sliders)): ?>
<section id="inicio">
  <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
    <div class="carousel-indicators">
      <?php foreach ($sliders as $i => $slide): ?>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : '' ?>" aria-current="<?= $i === 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $i + 1 ?>"></button>
      <?php endforeach; ?>
    </div>
    <div class="carousel-inner">
      <?php foreach ($sliders as $i => $slide): ?>
        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
          <div class="hero-slide <?= h($slide['clase_fondo']) ?>" <?php if (!empty($slide['imagen'])): ?>style="background-image:url('uploads/sliders/<?= h($slide['imagen']) ?>');background-size:cover;background-position:center;"<?php endif; ?>>
            <div class="container">
              <div class="col-lg-7">
                <h1><?= h($slide['titulo']) ?></h1>
                <?php if (!empty($slide['subtitulo'])): ?><p><?= h($slide['subtitulo']) ?></p><?php endif; ?>
                <?php if (!empty($slide['boton_texto']) && !empty($slide['boton_enlace'])): ?>
                  <a href="<?= h($slide['boton_enlace']) ?>" class="btn btn-brand btn-lg" data-ajax-link <?= $slide['boton_enlace'] === '#radioPlayer' ? 'data-play-trigger' : '' ?>><?= h($slide['boton_texto']) ?></a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>
  </div>
</section>
<?php endif; ?>

<!-- ===== NOTICIAS DESTACADAS ===== -->
<section class="container py-4 py-md-5 reveal">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h2 class="section-title mb-0">Últimas noticias</h2>
    <a href="noticias.php" class="btn btn-outline-dark btn-sm" data-ajax-link>Ver todas <i class="bi bi-arrow-right"></i></a>
  </div>

  <?php if (!$noticiaDestacada): ?>
    <p class="text-muted">Aún no hay noticias publicadas.</p>
  <?php else: ?>
    <a href="noticia.php?slug=<?= urlencode($noticiaDestacada['slug']) ?>" class="featured-news mb-4" data-ajax-link>
      <div class="featured-news-img <?= empty($noticiaDestacada['imagen']) ? 'no-image' : '' ?>"
           <?php if (!empty($noticiaDestacada['imagen'])): ?>style="background-image:url('uploads/noticias/<?= h($noticiaDestacada['imagen']) ?>');"<?php endif; ?>>
        <span class="news-date-badge"><?= fechaLegible($noticiaDestacada['creado_en']) ?></span>
        <?php if (!empty($noticiaDestacada['categoria_nombre'])): ?>
          <span class="news-cat-badge" style="background:<?= h($noticiaDestacada['categoria_color']) ?>"><?= h($noticiaDestacada['categoria_nombre']) ?></span>
        <?php endif; ?>
        <?php if (empty($noticiaDestacada['imagen'])): ?><i class="bi bi-newspaper"></i><?php endif; ?>
      </div>
      <div class="featured-news-body">
        <span class="featured-tag"><i class="bi bi-lightning-charge-fill"></i> Última hora</span>
        <h3><?= h($noticiaDestacada['titulo']) ?></h3>
        <p><?= h(recortarTexto($noticiaDestacada['resumen'] ?: $noticiaDestacada['contenido'], 170)) ?></p>
        <span class="read-more">Leer más <i class="bi bi-arrow-right"></i></span>
      </div>
    </a>

    <?php if (!empty($ultimasNoticias)): ?>
      <div class="row g-4">
        <?php foreach ($ultimasNoticias as $n): ?>
          <div class="col-sm-6 col-lg-4">
            <div class="card news-card">
              <div class="card-img-wrap">
                <span class="news-date-badge"><?= fechaLegible($n['creado_en']) ?></span>
                <?php if (!empty($n['categoria_nombre'])): ?>
                  <span class="news-cat-badge" style="background:<?= h($n['categoria_color']) ?>"><?= h($n['categoria_nombre']) ?></span>
                <?php endif; ?>
                <?php if (!empty($n['imagen'])): ?>
                  <img src="uploads/noticias/<?= h($n['imagen']) ?>" class="card-img-top" alt="<?= h($n['titulo']) ?>">
                <?php else: ?>
                  <div class="card-img-top d-flex align-items-center justify-content-center">
                    <i class="bi bi-newspaper text-white fs-1"></i>
                  </div>
                <?php endif; ?>
              </div>
              <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?= h($n['titulo']) ?></h5>
                <p class="card-text text-muted small"><?= h(recortarTexto($n['resumen'] ?: $n['contenido'], 100)) ?></p>
                <a href="noticia.php?slug=<?= urlencode($n['slug']) ?>" class="read-more mt-auto" data-ajax-link>Leer más <i class="bi bi-arrow-right"></i></a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</section>

<!-- ===== PROGRAMACIÓN DE HOY ===== -->
<section class="py-4 py-md-5 reveal" style="background:#fff;">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
      <h2 class="section-title mb-0">Programación de hoy</h2>
      <a href="programacion.php" class="btn btn-outline-dark btn-sm" data-ajax-link>Ver programación completa <i class="bi bi-arrow-right"></i></a>
    </div>

    <?php if (empty($programacionHoy)): ?>
      <p class="text-muted">Aún no hay programación registrada para hoy.</p>
    <?php else: ?>

      <?php if ($programaEnVivo): ?>
        <div class="on-air-hero mb-4">
          <div class="on-air-hero-icon"><i class="bi bi-broadcast"></i></div>
          <div>
            <span class="on-air-hero-label"><span class="live-dot on"></span> Al aire ahora</span>
            <h3><?= h($programaEnVivo['programa']) ?></h3>
            <?php if (!empty($programaEnVivo['conductor'])): ?>
              <div class="schedule-host"><i class="bi bi-mic-fill"></i> <?= h($programaEnVivo['conductor']) ?></div>
            <?php endif; ?>
            <div class="on-air-hero-time"><?= h(horaLegible($programaEnVivo['hora_inicio'])) ?> - <?= h(horaLegible($programaEnVivo['hora_fin'])) ?></div>
          </div>
        </div>
      <?php endif; ?>

      <div class="list-group schedule-list">
        <?php foreach ($programacionHoy as $franja): $alAire = programaAlAireAhora($franja); ?>
          <div class="list-group-item schedule-item <?= $alAire ? 'al-aire' : '' ?>">
            <div class="schedule-icon"><i class="bi bi-mic-fill"></i></div>
            <div class="schedule-time"><?= h(horaLegible($franja['hora_inicio'])) ?> - <?= h(horaLegible($franja['hora_fin'])) ?></div>
            <div class="flex-grow-1">
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <h5 class="mb-0"><?= h($franja['programa']) ?></h5>
                <?php if ($alAire): ?><span class="badge-live">AL AIRE</span><?php endif; ?>
              </div>
              <?php if (!empty($franja['conductor'])): ?>
                <div class="schedule-host"><i class="bi bi-mic-fill"></i> <?= h($franja['conductor']) ?></div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- ===== REDES SOCIALES ===== -->
<section class="py-4 py-md-5 reveal" id="redes-section">
  <div class="container text-center">
    <h2 class="section-title d-inline-block">Síguenos en redes</h2>
    <p class="text-muted mb-4">No te pierdas ningún contenido exclusivo de <?= h(SITE_NAME) ?></p>
    <div class="d-flex justify-content-center gap-3 social-block flex-wrap">
      <a href="<?= h(SOCIAL_FACEBOOK) ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
      <a href="<?= h(SOCIAL_INSTAGRAM) ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
      <a href="<?= h(SOCIAL_WHATSAPP) ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
      <a href="<?= h(SOCIAL_YOUTUBE) ?>" target="_blank" rel="noopener" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
      <a href="<?= h(SOCIAL_TIKTOK) ?>" target="_blank" rel="noopener" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
      <a href="<?= h(SOCIAL_X) ?>" target="_blank" rel="noopener" aria-label="X"><i class="bi bi-twitter-x"></i></a>
    </div>
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
