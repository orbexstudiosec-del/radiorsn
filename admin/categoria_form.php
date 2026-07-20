<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$categoria = $id ? getCategoriaPorId($id) : null;

if ($id && !$categoria) {
    header('Location: categorias.php');
    exit;
}

$pageTitle = $categoria ? 'Editar categoría' : 'Nueva categoría';

include __DIR__ . '/includes/admin_header.php';
?>

<h1 class="h3 fw-bold mb-4"><?= h($pageTitle) ?></h1>

<div class="card admin-card p-4" style="max-width:480px;">
  <form action="categoria_guardar.php" method="post">
    <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
    <?php if ($categoria): ?>
      <input type="hidden" name="id" value="<?= (int)$categoria['id'] ?>">
    <?php endif; ?>

    <div class="mb-3">
      <label class="form-label">Nombre</label>
      <input type="text" name="nombre" class="form-control" required value="<?= h($categoria['nombre'] ?? '') ?>">
    </div>

    <div class="mb-4">
      <label class="form-label">Color de la insignia</label>
      <input type="color" name="color" class="form-control form-control-color" value="<?= h($categoria['color'] ?? '#f7941d') ?>">
    </div>

    <button type="submit" class="btn btn-brand">Guardar</button>
    <a href="categorias.php" class="btn btn-outline-secondary">Cancelar</a>
  </form>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
