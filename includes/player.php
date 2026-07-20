<?php
/**
 * Reproductor de radio persistente (barra fija inferior).
 * Vive fuera de #content para que la navegación AJAX nunca lo recargue
 * y el audio no se interrumpa al cambiar de página.
 */
?>
<div class="radio-player fixed-bottom" id="radioPlayer" data-stream="<?= h(STREAM_URL) ?>">
  <div class="container">
    <div class="d-flex align-items-center gap-3 py-2">
      <button type="button" class="btn btn-play" id="btnPlay" aria-label="Reproducir">
        <i class="bi bi-play-fill" id="btnPlayIcon"></i>
      </button>

      <div class="live-dot" id="liveDot" title="En vivo"></div>

      <div class="player-info flex-grow-1">
        <div class="player-station"><?= h(SITE_NAME) ?> <span class="badge-live">EN VIVO</span></div>
        <div class="player-status" id="playerStatus">Presiona play para escuchar</div>
      </div>

      <div class="d-none d-sm-flex align-items-center gap-2 volume-wrap">
        <i class="bi bi-volume-up"></i>
        <input type="range" class="form-range" id="volumeRange" min="0" max="100" value="80">
      </div>
    </div>
  </div>
  <audio id="audioStream" preload="none">
    <source src="<?= h(STREAM_URL) ?>">
  </audio>
</div>
