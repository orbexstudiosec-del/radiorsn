<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pageTitle = 'Dashboard';

$visitantesEnVivo = getVisitantesEnVivo();
$visitasHoy = getVisitasHoy();
$visitas7dias = getVisitasUltimosDias(7);
$graficoVisitas = getVisitasPorDia(7);
$noticiasMasVistas = getNoticiasMasVistas(5, 30);

$totalNoticias = contarFilas('noticias', 'publicado = 1');
$totalCategorias = contarFilas('categorias');
$totalProgramas = contarFilas('programacion', 'activo = 1');

$maxGrafico = max(1, max(array_column($graficoVisitas, 'total')));
$diasCortos = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

include __DIR__ . '/includes/admin_header.php';
?>

<h1 class="h3 fw-bold mb-4">Dashboard</h1>

<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="card admin-card stat-card stat-card-live p-3">
      <div class="stat-label"><span class="live-dot on"></span> En vivo ahora</div>
      <div class="stat-value" id="statVivo"><?= $visitantesEnVivo ?></div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card admin-card stat-card p-3">
      <div class="stat-label">Visitas hoy</div>
      <div class="stat-value"><?= $visitasHoy ?></div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card admin-card stat-card p-3">
      <div class="stat-label">Visitas (7 días)</div>
      <div class="stat-value"><?= $visitas7dias ?></div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card admin-card stat-card p-3">
      <div class="stat-label">Noticias publicadas</div>
      <div class="stat-value"><?= $totalNoticias ?></div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <div class="col-lg-7">
    <div class="card admin-card p-4 h-100">
      <h2 class="h6 fw-bold mb-3">Visitas de los últimos 7 días</h2>
      <div class="mini-chart">
        <?php foreach ($graficoVisitas as $dia):
            $alturaPct = round(($dia['total'] / $maxGrafico) * 100);
            $esHoy = $dia['fecha'] === date('Y-m-d');
            $diaCorto = $diasCortos[(int)date('N', strtotime($dia['fecha'])) - 1];
        ?>
          <div class="mini-chart-col">
            <div class="mini-chart-value"><?= (int)$dia['total'] ?></div>
            <div class="mini-chart-bar <?= $esHoy ? 'today' : '' ?>" style="height:<?= max(4, $alturaPct) ?>%"></div>
            <div class="mini-chart-label"><?= h($diaCorto) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card admin-card p-4 h-100">
      <h2 class="h6 fw-bold mb-3">Noticias más vistas <span class="text-muted small fw-normal">(30 días)</span></h2>
      <?php if (empty($noticiasMasVistas)): ?>
        <p class="text-muted small mb-0">Todavía no hay suficientes datos de visitas.</p>
      <?php else: ?>
        <ol class="top-news-list mb-0">
          <?php foreach ($noticiasMasVistas as $n): ?>
            <li>
              <span class="text-truncate"><?= h($n['titulo']) ?></span>
              <span class="badge bg-dark rounded-pill"><?= (int)$n['vistas'] ?></span>
            </li>
          <?php endforeach; ?>
        </ol>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-6 col-lg-3">
    <div class="card admin-card stat-card p-3">
      <div class="stat-label">Categorías</div>
      <div class="stat-value"><?= $totalCategorias ?></div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card admin-card stat-card p-3">
      <div class="stat-label">Programas activos</div>
      <div class="stat-value"><?= $totalProgramas ?></div>
    </div>
  </div>
</div>

<script>
(function () {
  var statVivo = document.getElementById('statVivo');
  if (!statVivo) return;
  function actualizarEnVivo() {
    fetch('stats_vivo.php', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (res) { return res.json(); })
      .then(function (data) { statVivo.textContent = data.en_vivo; })
      .catch(function () {});
  }
  setInterval(actualizarEnVivo, 20000);
})();
</script>

<?php include __DIR__ . '/includes/admin_footer.php'; ?>
