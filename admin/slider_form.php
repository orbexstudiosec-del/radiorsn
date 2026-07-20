<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$slider = $id ? getSliderPorId($id) : null;

if ($id && !$slider) {
    header('Location: sliders.php');
    exit;
}

$pageTitle = $slider ? 'Editar diapositiva' : 'Nueva diapositiva';

include __DIR__ . '/includes/admin_header.php';
?>

<h1 class="h3 fw-bold mb-4"><?= h($pageTitle) ?></h1>

<div class="card admin-card p-4" style="max-width:640px;">
  <form action="slider_guardar.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
    <?php if ($slider): ?>
      <input type="hidden" name="id" value="<?= (int)$slider['id'] ?>">
    <?php endif; ?>

    <div class="mb-3">
      <label class="form-label">Título</label>
      <input type="text" name="titulo" class="form-control" required value="<?= h($slider['titulo'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Subtítulo <span class="text-muted small">(opcional)</span></label>
      <input type="text" name="subtitulo" class="form-control" value="<?= h($slider['subtitulo'] ?? '') ?>">
    </div>

    <div class="row g-3 mb-3">
      <div class="col-sm-6">
        <label class="form-label">Texto del botón <span class="text-muted small">(opcional)</span></label>
        <input type="text" name="boton_texto" class="form-control" value="<?= h($slider['boton_texto'] ?? '') ?>" placeholder="Ej: Escuchar en vivo">
      </div>
      <div class="col-sm-6">
        <label class="form-label">Enlace del botón</label>
        <input type="text" name="boton_enlace" class="form-control" value="<?= h($slider['boton_enlace'] ?? '') ?>" placeholder="#radioPlayer, noticias.php...">
        <div class="form-text">Usa <code>#radioPlayer</code> para que también active el reproductor.</div>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Imagen de fondo <span class="text-muted small">(opcional, JPG/PNG/WEBP, máx. 4MB, recomendado 1920×1080)</span></label>
      <?php if (!empty($slider['imagen'])): ?>
        <div class="mb-2">
          <img src="../uploads/sliders/<?= h($slider['imagen']) ?>" style="max-height:120px;" class="rounded border">
        </div>
      <?php endif; ?>
      <input type="file" name="imagen" class="form-control" accept=".jpg,.jpeg,.png,.webp">
      <div class="form-text">Si no subes una imagen, se usa un degradado de color como fondo (elige uno abajo).</div>
    </div>

    <div class="mb-4">
      <label class="form-label">Degradado de fondo <span class="text-muted small">(se usa solo si no hay imagen)</span></label>
      <div class="d-flex gap-2 flex-wrap">
        <?php foreach (SLIDER_FONDOS as $clase): $sel = ($slider['clase_fondo'] ?? 'slide-bg-1') === $clase; ?>
          <label class="fondo-opcion <?= $clase ?> <?= $sel ? 'is-selected' : '' ?>">
            <input type="radio" name="clase_fondo" value="<?= $clase ?>" <?= $sel ? 'checked' : '' ?> class="visually-hidden">
          </label>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-sm-6">
        <label class="form-label">Orden</label>
        <input type="number" name="orden" class="form-control" value="<?= (int)($slider['orden'] ?? 0) ?>">
        <div class="form-text">Las diapositivas se muestran de menor a mayor.</div>
      </div>
      <div class="col-sm-6 d-flex align-items-end">
        <div class="form-check">
          <input type="checkbox" name="activo" value="1" class="form-check-input" id="activo" <?= (!$slider || $slider['activo']) ? 'checked' : '' ?>>
          <label class="form-check-label" for="activo">Activa (visible en el sitio)</label>
        </div>
      </div>
    </div>

    <button type="submit" class="btn btn-brand">Guardar</button>
    <a href="sliders.php" class="btn btn-outline-secondary">Cancelar</a>
  </form>
</div>

<script>
document.querySelectorAll('.fondo-opcion').forEach(function (label) {
  label.addEventListener('click', function () {
    document.querySelectorAll('.fondo-opcion').forEach(function (l) { l.classList.remove('is-selected'); });
    label.classList.add('is-selected');
  });
});
</script>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
