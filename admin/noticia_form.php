<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$noticia = $id ? getNoticiaPorId($id) : null;

if ($id && !$noticia) {
    header('Location: noticias.php');
    exit;
}

$pageTitle = $noticia ? 'Editar noticia' : 'Nueva noticia';
$categorias = getCategorias();
$cargarQuill = true;

include __DIR__ . '/includes/admin_header.php';
?>

<h1 class="h3 fw-bold mb-4"><?= h($pageTitle) ?></h1>

<div class="card admin-card p-4">
  <form action="noticia_guardar.php" method="post" enctype="multipart/form-data" id="formNoticia">
    <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
    <?php if ($noticia): ?>
      <input type="hidden" name="id" value="<?= (int)$noticia['id'] ?>">
    <?php endif; ?>

    <div class="mb-3">
      <label class="form-label">Título</label>
      <input type="text" name="titulo" class="form-control" required value="<?= h($noticia['titulo'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Resumen breve <span class="text-muted small">(se muestra en las tarjetas de noticias)</span></label>
      <textarea name="resumen" class="form-control" rows="2"><?= h($noticia['resumen'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Categoría <span class="text-muted small">(opcional)</span></label>
      <select name="categoria_id" class="form-select">
        <option value="">Sin categoría</option>
        <?php foreach ($categorias as $cat): ?>
          <option value="<?= (int)$cat['id'] ?>" <?= (isset($noticia['categoria_id']) && (int)$noticia['categoria_id'] === (int)$cat['id']) ? 'selected' : '' ?>><?= h($cat['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
      <?php if (empty($categorias)): ?>
        <div class="form-text">Aún no tienes categorías. <a href="categoria_form.php">Crea una aquí</a>.</div>
      <?php endif; ?>
    </div>

    <div class="mb-3">
      <label class="form-label">Contenido</label>
      <div id="editorContenido"></div>
      <input type="hidden" name="contenido" id="contenidoHidden">
    </div>

    <div class="mb-3">
      <label class="form-label">Imagen <span class="text-muted small">(opcional, JPG/PNG/WEBP, máx. 3MB)</span></label>
      <?php if (!empty($noticia['imagen'])): ?>
        <div class="mb-2">
          <img src="../uploads/noticias/<?= h($noticia['imagen']) ?>" style="max-height:120px;" class="rounded border">
        </div>
      <?php endif; ?>
      <input type="file" name="imagen" class="form-control" accept=".jpg,.jpeg,.png,.webp">
    </div>

    <div class="form-check mb-4">
      <input type="checkbox" name="publicado" value="1" class="form-check-input" id="publicado" <?= (!$noticia || $noticia['publicado']) ? 'checked' : '' ?>>
      <label class="form-check-label" for="publicado">Publicada (visible en el sitio)</label>
    </div>

    <button type="submit" class="btn btn-brand">Guardar</button>
    <a href="noticias.php" class="btn btn-outline-secondary">Cancelar</a>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
(function () {
  var quill = new Quill('#editorContenido', {
    theme: 'snow',
    placeholder: 'Escribe el contenido de la noticia...',
    modules: {
      toolbar: [
        [{ header: [2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        ['blockquote'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['link'],
        ['clean']
      ]
    }
  });

  quill.root.innerHTML = <?= json_encode(contenidoComoHtml($noticia['contenido'] ?? '')) ?>;

  var form = document.getElementById('formNoticia');
  var hidden = document.getElementById('contenidoHidden');
  form.addEventListener('submit', function (e) {
    hidden.value = quill.root.innerHTML;
    if (quill.getText().trim() === '') {
      e.preventDefault();
      alert('El contenido de la noticia no puede estar vacío.');
    }
  });
})();
</script>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
