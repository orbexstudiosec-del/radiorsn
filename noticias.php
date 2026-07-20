<?php
require_once __DIR__ . '/includes/functions.php';
registrarVisita('noticias');

$categorias = getCategorias();
$categoriaActual = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';
$categoriaInfo = $categoriaActual !== '' ? getCategoriaPorSlug($categoriaActual) : null;

// Si llega un slug de categoría que no existe, lo tratamos como "todas".
if ($categoriaActual !== '' && !$categoriaInfo) {
    $categoriaActual = '';
}

$pageTitle = ($categoriaInfo ? $categoriaInfo['nombre'] . ' - ' : '') . 'Noticias - ' . SITE_NAME;
$pageDescription = 'Todas las noticias de ' . SITE_NAME;

$noticias = getNoticias(null, true, $categoriaActual !== '' ? $categoriaActual : null);

ob_start();
?>

<section class="container py-4 py-md-5">
  <h1 class="section-title">Noticias</h1>

  <?php if (!empty($categorias)): ?>
    <ul class="nav nav-pills category-tabs mb-4 flex-nowrap overflow-auto">
      <li class="nav-item">
        <a href="noticias.php" class="nav-link <?= $categoriaActual === '' ? 'active' : '' ?>" data-ajax-link>Todas</a>
      </li>
      <?php foreach ($categorias as $cat): $activa = $categoriaActual === $cat['slug']; ?>
        <li class="nav-item">
          <a href="noticias.php?categoria=<?= urlencode($cat['slug']) ?>"
             class="nav-link <?= $activa ? 'active' : '' ?>"
             style="<?= $activa ? 'background:' . h($cat['color']) . ';border-color:' . h($cat['color']) . ';' : '' ?>"
             data-ajax-link><?= h($cat['nombre']) ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <?php if (empty($noticias)): ?>
    <p class="text-muted">
      <?= $categoriaInfo ? 'Aún no hay noticias publicadas en "' . h($categoriaInfo['nombre']) . '".' : 'Aún no hay noticias publicadas.' ?>
    </p>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($noticias as $n): ?>
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
