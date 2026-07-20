<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pageTitle = 'Programación';
$mensaje = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

$franjas = getTodaLaProgramacion();

include __DIR__ . '/includes/admin_header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
  <h1 class="h3 fw-bold mb-0">Programación</h1>
  <a href="programa_form.php" class="btn btn-brand"><i class="bi bi-plus-circle"></i> Nueva franja</a>
</div>

<?php if ($mensaje): ?>
  <div class="alert alert-success py-2 small"><?= h($mensaje) ?></div>
<?php endif; ?>

<div class="card admin-card">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Día</th>
          <th>Horario</th>
          <th>Programa</th>
          <th>Conductor</th>
          <th>Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($franjas)): ?>
          <tr><td colspan="6" class="text-center text-muted py-4">No hay franjas de programación todavía.</td></tr>
        <?php else: foreach ($franjas as $f): ?>
          <tr>
            <td><?= h(DIAS_SEMANA[(int)$f['dia_semana']]) ?></td>
            <td class="small"><?= h(horaLegible($f['hora_inicio'])) ?> - <?= h(horaLegible($f['hora_fin'])) ?></td>
            <td><?= h($f['programa']) ?></td>
            <td class="small text-muted"><?= h($f['conductor'] ?: '—') ?></td>
            <td>
              <?php if ($f['activo']): ?>
                <span class="badge bg-success">Activo</span>
              <?php else: ?>
                <span class="badge bg-secondary">Inactivo</span>
              <?php endif; ?>
            </td>
            <td class="text-end">
              <a href="programa_form.php?id=<?= (int)$f['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <form action="programa_eliminar.php" method="post" class="d-inline" onsubmit="return confirm('¿Eliminar esta franja de programación?');">
                <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
                <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
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
