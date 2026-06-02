<?php
// admin/index.php — Login do Admin
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (adminLogado()) redirect(APP_URL . '/admin/dashboard.php');
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (loginAdmin($_POST['email']??'', $_POST['senha']??'')) redirect(APP_URL.'/admin/dashboard.php');
    $erro = 'Credenciais inválidas.';
}
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin — VisualTech</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?= APP_URL ?>/css/style.css">
</head><body>
<div class="auth-page">
  <div class="container">
    <div class="form-card" style="max-width:400px;">
      <div style="text-align:center;margin-bottom:28px;">
        <div style="display:inline-flex;align-items:center;gap:10px;margin-bottom:10px;">
          <span class="logo-vt">VT</span>
          <span class="logo-text">Visual<strong>Tech</strong></span>
        </div>
        <div style="font-size:11px;color:var(--text-2);text-transform:uppercase;letter-spacing:1.5px;">Painel Administrativo</div>
      </div>
      <?php if(!empty($erro)): ?>
        <div class="alert alert-danger"><i class="fas fa-circle-xmark"></i> <?= sanitize($erro) ?></div>
      <?php endif; ?>
      <form method="POST">
        <div class="form-group"><label class="form-label" for="email">E-mail</label>
          <input class="form-control" type="email" id="email" name="email" required autofocus></div>
        <div class="form-group"><label class="form-label" for="senha">Senha</label>
          <input class="form-control" type="password" id="senha" name="senha" required></div>
        <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:8px;">
          <i class="fas fa-shield-halved"></i> Acessar Painel
        </button>
      </form>
      <div style="text-align:center;margin-top:20px;">
        <a href="<?= APP_URL ?>" style="font-size:13px;color:var(--text-2);">← Voltar à loja</a>
      </div>
    </div>
  </div>
</div>
</body></html>
