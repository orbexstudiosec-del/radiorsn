<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pageTitle = 'Noticias';
$mensaje = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$noticias = getTodasNoticias();
$totalPublicadas = count(array_filter($noticias, fn($n) => (int)$n['publicado'] === 1));
$totalBorradores = count($noticias) - $totalPublicadas;

include __DIR__ . '/includes/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
  <h1 class="h3 fw-bold mb-0">Noticias</h1>
  <a href="noticia_form.php" class="btn btn-brand"><i class="bi bi-plus-circle"></i> Nueva noticia</a>
</div>

<div class="d-flex flex-wrap gap-2 mb-4">
  <span class="news-count-pill"><i class="bi bi-newspaper"></i> <?= count($noticias) ?> en total</span>
  <span class="news-count-pill is-success"><i class="bi bi-check-circle"></i> <?= $totalPublicadas ?> publicadas</span>
  <span class="news-count-pill is-muted"><i class="bi bi-pencil-square"></i> <?= $totalBorradores ?> borradores</span>
</div>

<?php if ($mensaje): ?>
  <div class="alert alert-success py-2 small"><?= h($mensaje) ?></div>
<?php endif; ?>

<div class="card admin-card">
  <div class="table-responsive">
    <table class="table align-middle mb-0 news-table">
      <thead class="table-light">
        <tr>
          <th>Noticia</th>
          <th>Categoría</th>
          <th>Fecha</th>
          <th>Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($noticias)): ?>
          <tr><td colspan="5" class="text-center text-muted py-5">
            <i class="bi bi-newspaper fs-2 d-block mb-2"></i>
            Aún no hay noticias. <a href="noticia_form.php">Crea la primera</a>.
          </td></tr>
        <?php else: foreach ($noticias as $n): ?>
          <tr>
            <td>
              <div class="d-flex align-items-center gap-3">
                <?php if (!empty($n['imagen'])): ?>
                  <img src="../uploads/noticias/<?= h($n['imagen']) ?>" alt="" class="news-thumb">
                <?php else: ?>
                  <div class="news-thumb news-thumb-placeholder"><i class="bi bi-newspaper"></i></div>
                <?php endif; ?>
                <div class="min-w-0">
                  <div class="fw-semibold text-truncate news-thumb-title"><?= h($n['titulo']) ?></div>
                  <div class="text-muted small text-truncate news-thumb-title">
                    <?= h(recortarTexto($n['resumen'] ?: $n['contenido'], 70)) ?>
                  </div>
                </div>
              </div>
            </td>
            <td>
              <?php if (!empty($n['categoria_nombre'])): ?>
                <span class="badge" style="background:<?= h($n['categoria_color']) ?>"><?= h($n['categoria_nombre']) ?></span>
              <?php else: ?>
                <span class="text-muted small">Sin categoría</span>
              <?php endif; ?>
            </td>
            <td class="small text-muted"><?= fechaLegible($n['creado_en']) ?></td>
            <td>
              <?php if ($n['publicado']): ?>
                <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Publicada</span>
              <?php else: ?>
                <span class="badge bg-secondary"><i class="bi bi-pencil-square"></i> Borrador</span>
              <?php endif; ?>
            </td>
            <td class="text-end">
              <?php if ($n['publicado']): ?>
                <a href="../noticia.php?slug=<?= urlencode($n['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="Ver en el sitio"><i class="bi bi-box-arrow-up-right"></i></a>
              <?php endif; ?>
              <a href="noticia_form.php?id=<?= (int)$n['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar"><i class="bi bi-pencil"></i></a>
              <form action="noticia_eliminar.php" method="post" class="d-inline" onsubmit="return confirm('¿Eliminar esta noticia? Esta acción no se puede deshacer.');">
                <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
