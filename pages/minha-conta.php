<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (!clienteLogado()) { flash('info','Faça login para acessar sua conta.'); redirect(APP_URL.'/pages/login.php'); }
 
$tab     = $_GET['tab'] ?? 'pedidos';
$cliente = getClienteAtual();
$pedidos = db()->fetchAll('SELECT * FROM vw_pedidos_completo WHERE cliente_id=? ORDER BY criado_em DESC',[$_SESSION['cliente_id']]);
$enderecos=db()->fetchAll('SELECT * FROM enderecos WHERE cliente_id=? ORDER BY principal DESC',[$_SESSION['cliente_id']]);
$erro = $suc = '';
 
// Adicionar endereço
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['acao'])) {
    if ($_POST['acao']==='add_endereco') {
        db()->insert('INSERT INTO enderecos (cliente_id,apelido,cep,rua,numero,complemento,bairro,cidade,estado) VALUES (?,?,?,?,?,?,?,?,?)',
            [$_SESSION['cliente_id'],sanitize($_POST['apelido']??'Casa'),sanitize($_POST['cep']),sanitize($_POST['rua']),
             sanitize($_POST['numero']),sanitize($_POST['complemento']??''),sanitize($_POST['bairro']),
             sanitize($_POST['cidade']),sanitize($_POST['estado'])]);
        flash('sucesso','Endereço adicionado!'); redirect(APP_URL.'/pages/minha-conta.php?tab=enderecos');
    }
    if ($_POST['acao']==='del_endereco') {
        db()->query('DELETE FROM enderecos WHERE id=? AND cliente_id=?',[(int)$_POST['end_id'],$_SESSION['cliente_id']]);
        flash('sucesso','Endereço removido.'); redirect(APP_URL.'/pages/minha-conta.php?tab=enderecos');
    }
}
$pageTitle = 'Minha Conta';
require_once __DIR__ . '/../includes/header.php';
?>
<script>window._vtUrl = '<?= APP_URL ?>';</script>
<div class="page-header">
  <div class="container">
    <nav class="breadcrumb"><a href="<?= APP_URL ?>">Home</a><span>/</span><span>Minha Conta</span></nav>
    <h1>Olá, <?= sanitize(explode(' ',$cliente['nome'])[0]) ?>!</h1>
  </div>
</div>
 
<div class="container" style="padding-top:32px;padding-bottom:60px;">
  <div style="display:grid;grid-template-columns:220px 1fr;gap:28px;align-items:start;">
 
    <!-- Sidebar -->
    <div class="admin-sidebar" style="border-radius:var(--r-xl);border:1px solid var(--border);">
      <div style="padding:20px;border-bottom:1px solid var(--border);">
        <div style="font-size:13px;font-weight:700;color:var(--text-1);"><?= sanitize($cliente['nome']) ?></div>
        <div style="font-size:12px;color:var(--text-2);margin-top:2px;"><?= sanitize($cliente['email']) ?></div>
      </div>
      <nav style="padding:8px 0;">
        <a href="?tab=pedidos"   class="admin-nav-item <?= $tab==='pedidos'?'active':'' ?>"><i class="fas fa-box"></i> Meus Pedidos</a>
        <a href="?tab=enderecos" class="admin-nav-item <?= $tab==='enderecos'?'active':'' ?>"><i class="fas fa-map-pin"></i> Endereços</a>
        <a href="?tab=dados"     class="admin-nav-item <?= $tab==='dados'?'active':'' ?>"><i class="fas fa-user"></i> Meus Dados</a>
        <a href="<?= APP_URL ?>/pages/logout.php" class="admin-nav-item"><i class="fas fa-sign-out-alt"></i> Sair</a>
      </nav>
    </div>
 
    <!-- Content -->
    <div>
      <?php if($tab==='pedidos'): ?>
        <h2 style="font-family:'Rajdhani',sans-serif;font-size:22px;font-weight:700;margin-bottom:20px;">Meus Pedidos</h2>
        <?php if(empty($pedidos)): ?>
          <div class="empty-state"><i class="fas fa-box-open"></i><h3>Nenhum pedido ainda</h3><p>Explore nosso catálogo e faça seu primeiro pedido!</p><a href="<?= APP_URL ?>/pages/produtos.php" class="btn btn-primary">Ver Produtos</a></div>
        <?php else: ?>
          <div class="data-table-wrap">
            <table class="data-table">
              <thead><tr><th>Pedido</th><th>Data</th><th>Total</th><th>Pagamento</th><th>Status</th></tr></thead>
              <tbody>
                <?php foreach($pedidos as $p): ?>
                <tr>
                  <td><strong>#<?= $p['id'] ?></strong></td>
                  <td><?= date('d/m/Y',strtotime($p['criado_em'])) ?></td>
                  <td class="text-cyan font-raj"><?= formatarPreco($p['total']) ?></td>
                  <td><?= pagamento_label($p['forma_pagamento']) ?></td>
                  <td><?= statusLabel($p['status']) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
 
      <?php elseif($tab==='enderecos'): ?>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
          <h2 style="font-family:'Rajdhani',sans-serif;font-size:22px;font-weight:700;">Meus Endereços</h2>
          <button class="btn btn-primary btn-sm" onclick="document.getElementById('formEnd').style.display=document.getElementById('formEnd').style.display==='none'?'block':'none'">
            <i class="fas fa-plus"></i> Novo Endereço
          </button>
        </div>
 
        <!-- Form novo endereço -->
        <div id="formEnd" style="display:none;margin-bottom:24px;">
          <div class="data-table-wrap">
            <div class="data-table-head"><h3>Novo Endereço</h3></div>
            <form method="POST" style="padding:24px;">
              <input type="hidden" name="acao" value="add_endereco">
              <div class="form-row">
                <div class="form-group"><label class="form-label">Apelido</label><input class="form-control" name="apelido" placeholder="Casa, Trabalho..." value="Casa"></div>
                <div class="form-group"><label class="form-label">CEP</label><input class="form-control" name="cep" id="cep" placeholder="00000-000" required></div>
              </div>
              <div class="form-group"><label class="form-label">Rua</label><input class="form-control" name="rua" id="rua" required></div>
              <div class="form-row">
                <div class="form-group"><label class="form-label">Número</label><input class="form-control" name="numero" id="numero" required></div>
                <div class="form-group"><label class="form-label">Complemento</label><input class="form-control" name="complemento" placeholder="Apto, Bloco..."></div>
              </div>
              <div class="form-row">
                <div class="form-group"><label class="form-label">Bairro</label><input class="form-control" name="bairro" id="bairro" required></div>
                <div class="form-group"><label class="form-label">Cidade</label><input class="form-control" name="cidade" id="cidade" required></div>
              </div>
              <div class="form-group"><label class="form-label">Estado (UF)</label><input class="form-control" name="estado" id="estado" maxlength="2" style="max-width:100px;" required></div>
              <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Endereço</button>
            </form>
          </div>
        </div>
 
        <?php if(empty($enderecos)): ?>
          <div class="empty-state"><i class="fas fa-map-pin"></i><h3>Nenhum endereço</h3><p>Adicione um endereço para agilizar suas compras.</p></div>
        <?php else: ?>
          <?php foreach($enderecos as $end): ?>
          <div class="data-table-wrap" style="margin-bottom:12px;padding:18px 20px;display:flex;align-items:center;gap:16px;">
            <div style="flex:1;">
              <div style="font-weight:600;font-size:14px;margin-bottom:4px;">
                <?= sanitize($end['apelido']) ?>
                <?php if($end['principal']): ?><span class="tag tag-cyan" style="margin-left:8px;font-size:11px;">Principal</span><?php endif; ?>
              </div>
              <div style="font-size:13px;color:var(--text-2);">
                <?= sanitize($end['rua']) ?>, <?= sanitize($end['numero']) ?><?= $end['complemento']?', '.sanitize($end['complemento']):'' ?><br>
                <?= sanitize($end['bairro']) ?> — <?= sanitize($end['cidade']) ?>/<?= sanitize($end['estado']) ?> — CEP: <?= sanitize($end['cep']) ?>
              </div>
            </div>
            <form method="POST">
              <input type="hidden" name="acao" value="del_endereco">
              <input type="hidden" name="end_id" value="<?= $end['id'] ?>">
              <button type="submit" class="table-btn danger" onclick="return confirm('Remover endereço?')" title="Remover"><i class="fas fa-trash"></i></button>
            </form>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
 
      <?php elseif($tab==='dados'): ?>
        <h2 style="font-family:'Rajdhani',sans-serif;font-size:22px;font-weight:700;margin-bottom:20px;">Meus Dados</h2>
        <div class="data-table-wrap" style="padding:24px;max-width:500px;">
          <table class="spec-table">
            <tr><td>Nome</td><td><?= sanitize($cliente['nome']) ?></td></tr>
            <tr><td>E-mail</td><td><?= sanitize($cliente['email']) ?></td></tr>
            <tr><td>CPF</td><td><?= $cliente['cpf'] ? sanitize($cliente['cpf']) : '—' ?></td></tr>
            <tr><td>Telefone</td><td><?= $cliente['telefone'] ? sanitize($cliente['telefone']) : '—' ?></td></tr>
            <tr><td>Membro desde</td><td><?= date('d/m/Y',strtotime($cliente['criado_em'])) ?></td></tr>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
