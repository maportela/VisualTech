<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Clientes';
$busca  = trim($_GET['q']??'');
$page   = max(1,(int)($_GET['page']??1));
$perPage= 20; $offset=($page-1)*$perPage;
$where  = ['ativo=1']; $params=[];
if ($busca) { $where[]='(nome LIKE ? OR email LIKE ?)'; $params=["%%{$busca}%%","%%{$busca}%%"]; }
$whereStr=implode(' AND ',$where);
$total    = db()->count("SELECT COUNT(*) FROM clientes WHERE $whereStr",$params);
$clientes = db()->fetchAll("SELECT * FROM clientes WHERE $whereStr ORDER BY criado_em DESC LIMIT $perPage OFFSET $offset",$params);
$totalPages=(int)ceil($total/$perPage);
require_once __DIR__ . '/../includes/header.php';
?>
<div class="admin-page-title">Clientes</div>
<div style="display:flex;gap:12px;margin-bottom:20px;">
  <div class="header-search" style="flex:1;max-width:360px;">
    <form method="GET">
      <input type="text" name="q" placeholder="Buscar por nome ou e-mail..." value="<?= sanitize($busca) ?>">
      <button type="submit"><i class="fas fa-search"></i></button>
    </form>
  </div>
  <span class="results-count"><?= $total ?> cliente(s)</span>
</div>
<div class="data-table-wrap">
  <table class="data-table">
    <thead><tr><th>Nome</th><th>E-mail</th><th>CPF</th><th>Telefone</th><th>Cadastro</th></tr></thead>
    <tbody>
      <?php if(empty($clientes)): ?>
        <tr><td colspan="5" style="text-align:center;color:var(--text-2);padding:40px;">Nenhum cliente.</td></tr>
      <?php else: ?>
        <?php foreach($clientes as $c): ?>
        <tr>
          <td><div style="font-weight:500;"><?= sanitize($c['nome']) ?></div></td>
          <td style="font-size:13px;"><?= sanitize($c['email']) ?></td>
          <td style="font-size:13px;"><?= $c['cpf']?sanitize($c['cpf']):'—' ?></td>
          <td style="font-size:13px;"><?= $c['telefone']?sanitize($c['telefone']):'—' ?></td>
          <td style="font-size:13px;color:var(--text-2);"><?= date('d/m/Y',strtotime($c['criado_em'])) ?></td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
