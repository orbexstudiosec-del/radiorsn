<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Panel de administración';
}
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title><?= h($pageTitle) ?> - Admin <?= h(SITE_NAME) ?></title>
<link rel="icon" type="image/svg+xml" href="../assets/img/favicon.svg?v=3">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
<?php if (!empty($cargarQuill)): ?>
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<?php endif; ?>
<style>
  body { background: #f4f5f7; padding-bottom: 0; font-family: 'Poppins', 'Segoe UI', Arial, sans-serif; }
  .admin-navbar { background: #14161c; }
  .admin-navbar .nav-link, .admin-navbar .navbar-brand { color: #f1f1f1; }
  .admin-navbar .nav-link:hover, .admin-navbar .nav-link.active { color: #f7941d; }
  .admin-card { border: none; border-radius: .75rem; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg admin-navbar navbar-dark mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="dashboard.php">
      <img src="../assets/img/logo.png" alt="<?= h(SITE_NAME) ?>" class="brand-logo" onerror="this.style.display='none'">
      Admin
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="adminNav">
      <ul class="navbar-nav ms-auto gap-lg-2">
        <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="noticias.php"><i class="bi bi-newspaper"></i> Noticias</a></li>
        <li class="nav-item"><a class="nav-link" href="categorias.php"><i class="bi bi-tags"></i> Categorías</a></li>
        <li class="nav-item"><a class="nav-link" href="programacion.php"><i class="bi bi-calendar-week"></i> Programación</a></li>
        <li class="nav-item"><a class="nav-link" href="sliders.php"><i class="bi bi-images"></i> Slider</a></li>
        <li class="nav-item"><a class="nav-link" href="perfil.php"><i class="bi bi-person-circle"></i> Mi perfil</a></li>
        <li class="nav-item"><a class="nav-link" href="../index.php" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Ver sitio</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Salir</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container pb-5">
