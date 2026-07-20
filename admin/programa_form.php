<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$franja = $id ? getProgramaPorId($id) : null;

if ($id && !$franja) {
    header('Location: programacion.php');
    exit;
}

$pageTitle = $franja ? 'Editar franja' : 'Nueva franja';

include __DIR__ . '/includes/admin_header.php';
?>

<h1 class="h3 fw-bold mb-4"><?= h($pageTitle) ?></h1>

<div class="card admin-card p-4" style="max-width:640px;">
  <form action="programa_guardar.php" method="post">
    <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
    <?php if ($franja): ?>
      <input type="hidden" name="id" value="<?= (int)$franja['id'] ?>">
    <?php endif; ?>

    <div class="mb-3">
      <label class="form-label">Nombre del programa</label>
      <input type="text" name="programa" class="form-control" required value="<?= h($franja['programa'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Conductor(es) <span class="text-muted small">(opcional)</span></label>
      <input type="text" name="conductor" class="form-control" value="<?= h($franja['conductor'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Descripción breve <span class="text-muted small">(opcional)</span></label>
      <textarea name="descripcion" class="form-control" rows="2"><?= h($franja['descripcion'] ?? '') ?></textarea>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-sm-4">
        <label class="form-label">Día</label>
        <select name="dia_semana" class="form-select" required>
          <?php foreach (DIAS_SEMANA as $numero => $nombre): ?>
            <option value="<?= $numero ?>" <?= (isset($franja['dia_semana']) && (int)$franja['dia_semana'] === $numero) ? 'selected' : '' ?>><?= h($nombre) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-sm-4">
        <label class="form-label">Hora inicio</label>
        <input type="time" name="hora_inicio" class="form-control" required value="<?= h(isset($franja['hora_inicio']) ? substr($franja['hora_inicio'], 0, 5) : '') ?>">
      </div>
      <div class="col-sm-4">
        <label class="form-label">Hora fin</label>
        <input type="time" name="hora_fin" class="form-control" required value="<?= h(isset($franja['hora_fin']) ? substr($franja['hora_fin'], 0, 5) : '') ?>">
      </div>
    </div>

    <div class="form-check mb-4">
      <input type="checkbox" name="activo" value="1" class="form-check-input" id="activo" <?= (!$franja || $franja['activo']) ? 'checked' : '' ?>>
      <label class="form-check-label" for="activo">Activo (visible en el sitio)</label>
    </div>

    <button type="submit" class="btn btn-brand">Guardar</button>
    <a href="programacion.php" class="btn btn-outline-secondary">Cancelar</a>
  </form>
</div>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
