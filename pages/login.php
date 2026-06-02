<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (clienteLogado()) redirect(APP_URL . '/pages/minha-conta.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    if (loginCliente($email, $senha)) {
        flash('sucesso', 'Bem-vindo de volta!');
        redirect(APP_URL);
    } else {
        $erro = 'E-mail ou senha inválidos.';
    }
}
$pageTitle = 'Entrar';
require_once __DIR__ . '/../includes/header.php';
?>
<script>window._vtUrl = '<?= APP_URL ?>';</script>
<div class="auth-page">
  <div class="container">
    <div class="form-card">
      <h1 class="form-title">Entrar</h1>
      <p class="form-subtitle">Acesse sua conta VisualTech</p>
      <?php if (!empty($erro)): ?>
        <div class="alert alert-danger"><i class="fas fa-circle-xmark"></i> <?= sanitize($erro) ?></div>
      <?php endif; ?>
      <form method="POST">
        <div class="form-group">
          <label class="form-label" for="email">E-mail</label>
          <input class="form-control" type="email" id="email" name="email" required autofocus
                 value="<?= sanitize($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="senha">Senha</label>
          <input class="form-control" type="password" id="senha" name="senha" required>
        </div>
        <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:8px;">
          <i class="fas fa-sign-in-alt"></i> Entrar
        </button>
      </form>
      <div class="divider"></div>
      <p class="text-center text-muted" style="font-size:14px;">
        Não tem conta?
        <a href="<?= APP_URL ?>/pages/cadastro.php" class="text-cyan">Cadastre-se</a>
      </p>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>