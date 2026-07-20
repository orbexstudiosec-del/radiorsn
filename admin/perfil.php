<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pageTitle = 'Mi perfil';
$mensaje = $_SESSION['flash'] ?? '';
$error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash'], $_SESSION['flash_error']);

include __DIR__ . '/includes/admin_header.php';
?>

<h1 class="h3 fw-bold mb-4">Mi perfil</h1>

<?php if ($mensaje): ?>
  <div class="alert alert-success py-2 small"><?= h($mensaje) ?></div>
<?php endif; ?>
<?php if ($error): ?>
  <div class="alert alert-danger py-2 small"><?= h($error) ?></div>
<?php endif; ?>

<div class="card admin-card p-4" style="max-width:480px;">
  <h2 class="h6 fw-bold mb-3">Cambiar contraseña</h2>
  <form action="perfil_guardar.php" method="post">
    <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
    <div class="mb-3">
      <label class="form-label small">Contraseña actual</label>
      <input type="password" name="actual" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label small">Nueva contraseña <span class="text-muted">(mínimo 8 caracteres)</span></label>
      <input type="password" name="nueva" class="form-control" minlength="8" required>
    </div>
    <div class="mb-3">
      <label class="form-label small">Confirmar nueva contraseña</label>
      <input type="password" name="confirmar" class="form-control" minlength="8" required>
    </div>
    <button type="submit" class="btn btn-brand">Actualizar contraseña</button>
  </form>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
