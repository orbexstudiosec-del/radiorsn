<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pageTitle = 'Categorías';
$mensaje = $_SESSION['flash'] ?? '';
$error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash'], $_SESSION['flash_error']);

$categorias = getCategorias();

include __DIR__ . '/includes/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
  <h1 class="h3 fw-bold mb-0">Categorías</h1>
  <a href="categoria_form.php" class="btn btn-brand"><i class="bi bi-plus-circle"></i> Nueva categoría</a>
</div>

<?php if ($mensaje): ?>
  <div class="alert alert-success py-2 small"><?= h($mensaje) ?></div>
<?php endif; ?>
<?php if ($error): ?>
  <div class="alert alert-danger py-2 small"><?= h($error) ?></div>
<?php endif; ?>

<div class="card admin-card">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Color</th>
          <th>Nombre</th>
          <th>Slug</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($categorias)): ?>
          <tr><td colspan="4" class="text-center text-muted py-4">No hay categorías todavía.</td></tr>
        <?php else: foreach ($categorias as $c): ?>
          <tr>
            <td><span class="d-inline-block rounded-circle" style="width:22px;height:22px;background:<?= h($c['color']) ?>"></span></td>
            <td><?= h($c['nombre']) ?></td>
            <td class="text-muted small"><?= h($c['slug']) ?></td>
            <td class="text-end">
              <a href="categoria_form.php?id=<?= (int)$c['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <form action="categoria_eliminar.php" method="post" class="d-inline" onsubmit="return confirm('¿Eliminar esta categoría? Las noticias que la usan quedarán sin categoría.');">
                <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
