  </main><!-- /#content -->

  <footer class="site-footer">
    <div class="container">
      <div class="row gy-4">
        <div class="col-md-4">
          <img src="assets/img/logo.png" alt="<?= h(SITE_NAME) ?>" class="brand-logo mb-2" onerror="this.style.display='none'">
          <p class="footer-desc small mb-0"><?= h(SITE_DESCRIPCION_FOOTER) ?></p>
        </div>
        <div class="col-md-4">
          <h6 class="fw-bold">Enlaces</h6>
          <ul class="list-unstyled">
            <li><a href="index.php" data-ajax-link>Inicio</a></li>
            <li><a href="quienes-somos.php" data-ajax-link>Quiénes somos</a></li>
            <li><a href="noticias.php" data-ajax-link>Noticias</a></li>
            <li><a href="programacion.php" data-ajax-link>Programación</a></li>
          </ul>
        </div>
        <div class="col-md-4" id="redes">
          <h6 class="fw-bold">Síguenos</h6>
          <div class="d-flex gap-3 fs-4 social-icons">
            <a href="<?= h(SOCIAL_FACEBOOK) ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="<?= h(SOCIAL_INSTAGRAM) ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="<?= h(SOCIAL_WHATSAPP) ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
            <a href="<?= h(SOCIAL_YOUTUBE) ?>" target="_blank" rel="noopener" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
            <a href="<?= h(SOCIAL_TIKTOK) ?>" target="_blank" rel="noopener" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
            <a href="<?= h(SOCIAL_X) ?>" target="_blank" rel="noopener" aria-label="X"><i class="bi bi-twitter-x"></i></a>
          </div>
        </div>
      </div>
      <hr class="border-secondary">
      <p class="footer-bottom text-center small mb-0">
        &copy; <?= date('Y') ?> <?= h(SITE_NAME) ?>. Todos los derechos reservados.
        &middot; Desarrollado por <a href="https://orbexec.com/" target="_blank" rel="noopener">Orbex Studios</a>
        &middot; <a href="admin/">Admin</a>
      </p>
    </div>
  </footer>

  <?php include __DIR__ . '/player.php'; ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>
