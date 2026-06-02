<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (clienteLogado()) redirect(APP_URL . '/pages/minha-conta.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = registrarCliente([
        'nome'     => $_POST['nome']     ?? '',
        'email'    => $_POST['email']    ?? '',
        'senha'    => $_POST['senha']    ?? '',
        'cpf'      => $_POST['cpf']      ?? '',
        'telefone' => $_POST['telefone'] ?? '',
    ]);
    if (isset($resultado['sucesso'])) {
        flash('sucesso', 'Conta criada! Bem-vindo à VisualTech.');
        redirect(APP_URL);
    } else {
        $erro = $resultado['erro'];
    }
}
$pageTitle = 'Criar Conta';
require_once __DIR__ . '/../includes/header.php';
?>
<script>window._vtUrl = '<?= APP_URL ?>';</script>
<div class="auth-page">
  <div class="container">
    <div class="form-card">
      <h1 class="form-title">Criar Conta</h1>
      <p class="form-subtitle">Junte-se à VisualTech e aproveite os melhores preços</p>
      <?php if (!empty($erro)): ?>
        <div class="alert alert-danger"><i class="fas fa-circle-xmark"></i> <?= sanitize($erro) ?></div>
      <?php endif; ?>
      <form method="POST">
        <div class="form-group">
          <label class="form-label" for="nome">Nome completo</label>
          <input class="form-control" type="text" id="nome" name="nome" required autofocus
                 value="<?= sanitize($_POST['nome'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="email">E-mail</label>
          <input class="form-control" type="email" id="email" name="email" required
                 value="<?= sanitize($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="cpf">CPF (opcional)</label>
            <input class="form-control" type="text" id="cpf" name="cpf" maxlength="14"
                   placeholder="000.000.000-00" value="<?= sanitize($_POST['cpf'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="telefone">Telefone (opcional)</label>
            <input class="form-control" type="text" id="telefone" name="telefone" maxlength="15"
                   placeholder="(00) 00000-0000" value="<?= sanitize($_POST['telefone'] ?? '') ?>">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="senha">Senha (mín. 6 caracteres)</label>
          <input class="form-control" type="password" id="senha" name="senha" required minlength="6">
        </div>
        <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:8px;">
          <i class="fas fa-user-plus"></i> Criar Conta
        </button>
      </form>
      <div class="divider"></div>
      <p class="text-center text-muted" style="font-size:14px;">
        Já tem conta? <a href="<?= APP_URL ?>/pages/login.php" class="text-cyan">Entrar</a>
      </p>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>