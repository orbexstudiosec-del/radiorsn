<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (estaLogueado()) {
    header('Location: dashboard.php');
    exit;
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Iniciar sesión - Admin <?= h(SITE_NAME) ?></title>
<link rel="icon" type="image/svg+xml" href="../assets/img/favicon.svg?v=3">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  body {
    background: #f4f5f7;
    min-height: 100vh;
    display: flex;
    align-items: center;
    font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
    padding: 1.5rem .75rem;
    overflow-x: hidden;
    position: relative;
  }
  /* Ondas de radio expandiéndose de fondo, como una señal al aire */
  .bg-waves {
    position: fixed;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    z-index: 0;
    pointer-events: none;
  }
  .wave-ring {
    position: absolute;
    width: 140px;
    height: 140px;
    border-radius: 50%;
    border: 2px solid rgba(247,148,29,.4);
    animation: waveExpand 4.5s ease-out infinite;
  }
  .wave-ring:nth-child(2) { border-color: rgba(226,35,26,.35); animation-delay: 1.5s; }
  .wave-ring:nth-child(3) { border-color: rgba(255,212,0,.35); animation-delay: 3s; }
  @keyframes waveExpand {
    0%   { transform: scale(1); opacity: .9; }
    100% { transform: scale(8); opacity: 0; }
  }
  @media (prefers-reduced-motion: reduce) {
    .wave-ring { animation: none; opacity: 0; }
    .mini-eq span { animation: none; transform: scaleY(.7); }
  }
  .login-card {
    position: relative;
    z-index: 1;
    max-width: 380px;
    width: 100%;
    margin: 0 auto;
    background: #14161c;
    border: none;
    border-radius: 1rem;
    border-top: 4px solid transparent;
    border-image: linear-gradient(90deg, #ffd400, #f7941d, #e2231a) 1;
    overflow: hidden;
  }
  /* Mini ecualizador junto al texto "Panel de administración" */
  .mini-eq {
    display: inline-flex;
    align-items: flex-end;
    gap: 2px;
    height: 12px;
  }
  .mini-eq span {
    width: 3px;
    background: linear-gradient(180deg, #ffd400, #f7941d, #e2231a);
    border-radius: 1px;
    animation: eqBar 1s ease-in-out infinite;
    transform-origin: bottom;
  }
  .mini-eq span:nth-child(1) { height: 40%; animation-delay: 0s; }
  .mini-eq span:nth-child(2) { height: 100%; animation-delay: .2s; }
  .mini-eq span:nth-child(3) { height: 60%; animation-delay: .4s; }
  .mini-eq span:nth-child(4) { height: 85%; animation-delay: .1s; }
  @keyframes eqBar {
    0%, 100% { transform: scaleY(.4); }
    50% { transform: scaleY(1); }
  }
  .login-card .form-label {
    color: #cfd0d6;
  }
  .login-card .form-control {
    background: #1c1f27;
    border-color: #33363f;
    color: #fff;
  }
  .login-card .form-control:focus {
    background: #1c1f27;
    border-color: #f7941d;
    color: #fff;
    box-shadow: 0 0 0 .2rem rgba(247,148,29,.25);
  }
  .login-card .form-control::placeholder { color: #7a7d87; }
  .login-card .subtitle { color: #9a9ba4; }
  .btn-brand-login {
    background: linear-gradient(90deg, #f7941d, #e2231a);
    border: none;
    font-weight: 600;
    transition: opacity .2s ease, transform .2s ease;
  }
  .btn-brand-login:hover { opacity: .92; transform: translateY(-1px); color: #fff; }
</style>
</head>
<body>
<div class="bg-waves" aria-hidden="true">
  <span class="wave-ring"></span>
  <span class="wave-ring"></span>
  <span class="wave-ring"></span>
</div>
<div class="container">
  <div class="card login-card shadow-lg p-4">
    <div class="text-center mb-3">
      <img src="../assets/img/logo.png" alt="<?= h(SITE_NAME) ?>" class="mb-2" style="height:80px; max-width:100%;" onerror="this.style.display='none'">
      <p class="subtitle small mb-0 d-flex align-items-center justify-content-center gap-2">
        <span class="mini-eq" aria-hidden="true"><span></span><span></span><span></span><span></span></span>
        Panel de administración
      </p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2 small"><?= h($error) ?></div>
    <?php endif; ?>

    <form action="login.php" method="post">
      <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
      <div class="mb-3">
        <label class="form-label small">Usuario</label>
        <input type="text" name="username" class="form-control" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label small">Contraseña</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-brand-login w-100 text-white">Ingresar</button>
    </form>
  </div>
</div>
</body>
</html>
