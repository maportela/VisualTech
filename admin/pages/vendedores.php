<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Vendedores';
$erro = $suc = '';
 
// Cadastrar novo vendedor
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $nome  = sanitize($_POST['nome']??'');
    $email = trim($_POST['email']??'');
    $senha = $_POST['senha']??'';
    $cargo = sanitize($_POST['cargo']??'Vendedor');
    $nivel = $_POST['nivel']??'vendedor';
    if (!$nome||!$email||!$senha) $erro='Preencha todos os campos.';
    elseif(db()->count('SELECT COUNT(*) FROM vendedores WHERE email=?',[$email])) $erro='E-mail já cadastrado.';
    else {
        db()->insert('INSERT INTO vendedores (nome,email,senha,cargo,nivel) VALUES (?,?,?,?,?)',
            [$nome,$email,password_hash($senha,PASSWORD_BCRYPT,['cost'=>12]),$cargo,$nivel]);
        flash('sucesso','Vendedor cadastrado!'); redirect(APP_URL.'/admin/pages/vendedores.php');
    }
}
 
$vendedores=db()->fetchAll('SELECT * FROM vendedores ORDER BY criado_em DESC');
require_once __DIR__ . '/../includes/header.php';
?>
<div class="admin-page-title">Vendedores</div>
 
<?php if($erro): ?><div class="alert alert-danger"><i class="fas fa-circle-xmark"></i> <?= sanitize($erro) ?></div><?php endif; ?>
 
<div style="display:grid;grid-template-columns:1fr 380px;gap:24px;align-items:start;">
  <div class="data-table-wrap">
    <table class="data-table">
      <thead><tr><th>Nome</th><th>E-mail</th><th>Cargo</th><th>Nível</th><th>Cadastro</th></tr></thead>
      <tbody>
        <?php if(empty($vendedores)): ?>
          <tr><td colspan="5" style="text-align:center;color:var(--text-2);padding:40px;">Nenhum vendedor.</td></tr>
        <?php else: ?>
          <?php foreach($vendedores as $v): ?>
          <tr>
            <td><div style="font-weight:500;"><?= sanitize($v['nome']) ?></div></td>
            <td style="font-size:13px;"><?= sanitize($v['email']) ?></td>
            <td style="font-size:13px;"><?= sanitize($v['cargo']) ?></td>
            <td><span class="tag <?= $v['nivel']==='admin'?'tag-cyan':($v['nivel']==='gerente'?'tag-amber':'tag-green') ?>"><?= ucfirst($v['nivel']) ?></span></td>
            <td style="font-size:13px;color:var(--text-2);"><?= date('d/m/Y',strtotime($v['criado_em'])) ?></td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
 
  <div class="data-table-wrap">
    <div class="data-table-head"><h3>Novo Vendedor</h3></div>
    <form method="POST" style="padding:24px;">
      <div class="form-group"><label class="form-label">Nome</label><input class="form-control" name="nome" required></div>
      <div class="form-group"><label class="form-label">E-mail</label><input class="form-control" type="email" name="email" required></div>
      <div class="form-group"><label class="form-label">Senha</label><input class="form-control" type="password" name="senha" required minlength="6"></div>
      <div class="form-group"><label class="form-label">Cargo</label><input class="form-control" name="cargo" value="Vendedor"></div>
      <div class="form-group">
        <label class="form-label">Nível de Acesso</label>
        <select class="form-control" name="nivel">
          <option value="vendedor">Vendedor</option>
          <option value="gerente">Gerente</option>
          <option value="admin">Admin</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-full"><i class="fas fa-user-plus"></i> Cadastrar</button>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
