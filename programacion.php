<?php
require_once __DIR__ . '/includes/functions.php';
registrarVisita('programacion');

$pageTitle = 'Programación - ' . SITE_NAME;
$pageDescription = 'Conoce la programación semanal de ' . SITE_NAME;

$programacion = getProgramacionPorDia();
$hoy = (int)date('N');

ob_start();
?>

<section class="container py-4 py-md-5">
  <h1 class="section-title">Programación</h1>
  <p class="text-muted mb-4">Conoce qué está sonando en <?= h(SITE_NAME) ?> cada día de la semana.</p>

  <ul class="nav nav-pills schedule-tabs mb-4 flex-nowrap overflow-auto" id="scheduleTabs" role="tablist">
    <?php foreach (DIAS_SEMANA as $numero => $nombre): ?>
      <li class="nav-item" role="presentation">
        <button class="nav-link <?= $numero === $hoy ? 'active' : '' ?>" id="tab-dia-<?= $numero ?>" data-bs-toggle="pill" data-bs-target="#dia-<?= $numero ?>" type="button" role="tab">
          <?= h($nombre) ?>
          <?php if ($numero === $hoy): ?><span class="badge bg-light text-dark ms-1">Hoy</span><?php endif; ?>
        </button>
      </li>
    <?php endforeach; ?>
  </ul>

  <div class="tab-content" id="scheduleTabsContent">
    <?php foreach (DIAS_SEMANA as $numero => $nombre): ?>
      <div class="tab-pane fade <?= $numero === $hoy ? 'show active' : '' ?>" id="dia-<?= $numero ?>" role="tabpanel">
        <?php if (empty($programacion[$numero])): ?>
          <p class="text-muted">Aún no hay programas registrados para este día.</p>
        <?php else: ?>
          <div class="list-group schedule-list">
            <?php foreach ($programacion[$numero] as $franja): $alAire = programaAlAireAhora($franja); ?>
              <div class="list-group-item schedule-item <?= $alAire ? 'al-aire' : '' ?>">
                <div class="schedule-icon"><i class="bi bi-mic-fill"></i></div>
                <div class="schedule-time"><?= h(horaLegible($franja['hora_inicio'])) ?> - <?= h(horaLegible($franja['hora_fin'])) ?></div>
                <div class="flex-grow-1">
                  <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h5 class="mb-0"><?= h($franja['programa']) ?></h5>
                    <?php if ($alAire): ?><span class="badge-live">AL AIRE</span><?php endif; ?>
                  </div>
                  <?php if (!empty($franja['conductor'])): ?>
                    <div class="schedule-host"><i class="bi bi-mic-fill"></i> <?= h($franja['conductor']) ?></div>
                  <?php endif; ?>
                  <?php if (!empty($franja['descripcion'])): ?>
                    <p class="text-muted small mb-0 mt-1"><?= h($franja['descripcion']) ?></p>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
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
