<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pageTitle = 'Slider';
$mensaje = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$sliders = getSliders(false);

include __DIR__ . '/includes/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
  <h1 class="h3 fw-bold mb-0">Slider del home</h1>
  <a href="slider_form.php" class="btn btn-brand"><i class="bi bi-plus-circle"></i> Nueva diapositiva</a>
</div>

<?php if ($mensaje): ?>
  <div class="alert alert-success py-2 small"><?= h($mensaje) ?></div>
<?php endif; ?>

<?php if (empty($sliders)): ?>
  <div class="card admin-card p-5 text-center text-muted">
    <i class="bi bi-images fs-2 d-block mb-2"></i>
    Aún no hay diapositivas. <a href="slider_form.php">Crea la primera</a>.
  </div>
<?php else: ?>
  <div class="row g-3">
    <?php foreach ($sliders as $s): ?>
      <div class="col-md-6 col-lg-4">
        <div class="card admin-card overflow-hidden h-100">
          <div class="slider-preview <?= empty($s['imagen']) ? h($s['clase_fondo']) : '' ?>"
               <?php if (!empty($s['imagen'])): ?>style="background-image:url('../uploads/sliders/<?= h($s['imagen']) ?>');"<?php endif; ?>>
            <?php if (!$s['activo']): ?><span class="badge bg-secondary slider-preview-badge">Inactiva</span><?php endif; ?>
            <span class="badge bg-dark slider-preview-order">#<?= (int)$s['orden'] ?></span>
          </div>
          <div class="card-body">
            <h2 class="h6 fw-bold mb-1 text-truncate"><?= h($s['titulo']) ?></h2>
            <p class="text-muted small mb-2 text-truncate"><?= h($s['subtitulo'] ?: '—') ?></p>
            <div class="d-flex justify-content-between align-items-center">
              <a href="slider_form.php?id=<?= (int)$s['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Editar</a>
              <form action="slider_eliminar.php" method="post" onsubmit="return confirm('¿Eliminar esta diapositiva?');">
                <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
