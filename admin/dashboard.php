<?php // Painel do adm
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'Dashboard';
 
$totalProdutos  = db()->count('SELECT COUNT(*) FROM produtos WHERE ativo=1');
$totalClientes  = db()->count('SELECT COUNT(*) FROM clientes WHERE ativo=1');
$totalPedidos   = db()->count('SELECT COUNT(*) FROM pedidos');
$totalVendas    = db()->fetch('SELECT COALESCE(SUM(total),0) AS total FROM pedidos WHERE status != "cancelado"');
$pedidosRecentes= db()->fetchAll('SELECT * FROM vw_pedidos_completo ORDER BY criado_em DESC LIMIT 10');
$estoqueBaixo   = db()->fetchAll('SELECT * FROM vw_estoque_baixo LIMIT 5');
 
require_once __DIR__ . '/includes/header.php';
?>
<script>window._vtUrl = '<?= APP_URL ?>';</script>
 
<div class="admin-page-title">Dashboard</div>
 
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-card-header">
      <div class="stat-icon stat-icon-cyan"><i class="fas fa-box"></i></div>
    </div>
    <div class="stat-value"><?= $totalProdutos ?></div>
    <div class="stat-label">Produtos Ativos</div>
  </div>
  <div class="stat-card">
    <div class="stat-card-header">
      <div class="stat-icon stat-icon-amber"><i class="fas fa-users"></i></div>
    </div>
    <div class="stat-value"><?= $totalClientes ?></div>
    <div class="stat-label">Clientes</div>
  </div>
  <div class="stat-card">
    <div class="stat-card-header">
      <div class="stat-icon stat-icon-green"><i class="fas fa-shopping-bag"></i></div>
    </div>
    <div class="stat-value"><?= $totalPedidos ?></div>
    <div class="stat-label">Pedidos Totais</div>
  </div>
  <div class="stat-card">
    <div class="stat-card-header">
      <div class="stat-icon stat-icon-cyan"><i class="fas fa-dollar-sign"></i></div>
    </div>
    <div class="stat-value" style="font-size:22px;"><?= formatarPreco((float)$totalVendas['total']) ?></div>
    <div class="stat-label">Receita Total</div>
  </div>
</div>
 
<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;">
  <!-- Pedidos recentes -->
  <div class="data-table-wrap">
    <div class="data-table-head">
      <h3>Pedidos Recentes</h3>
      <a href="<?= APP_URL ?>/admin/pages/pedidos.php" class="btn btn-ghost btn-sm">Ver todos</a>
    </div>
    <table class="data-table">
      <thead><tr><th>#</th><th>Cliente</th><th>Total</th><th>Pagamento</th><th>Status</th><th>Data</th></tr></thead>
      <tbody>
        <?php if(empty($pedidosRecentes)): ?>
          <tr><td colspan="6" style="text-align:center;color:var(--text-2);padding:32px;">Nenhum pedido ainda.</td></tr>
        <?php else: ?>
          <?php foreach($pedidosRecentes as $p): ?>
          <tr>
            <td><strong>#<?= $p['id'] ?></strong></td>
            <td><?= sanitize($p['cliente_nome']) ?></td>
            <td class="text-cyan"><?= formatarPreco($p['total']) ?></td>
            <td><?= pagamento_label($p['forma_pagamento']) ?></td>
            <td><?= statusLabel($p['status']) ?></td>
            <td style="color:var(--text-2);font-size:13px;"><?= date('d/m/Y',strtotime($p['criado_em'])) ?></td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
 
  <!-- Estoque baixo -->
  <div class="data-table-wrap">
    <div class="data-table-head"><h3>⚠ Estoque Baixo</h3></div>
    <?php if(empty($estoqueBaixo)): ?>
      <div style="padding:24px;text-align:center;color:var(--text-2);">Tudo em ordem!</div>
    <?php else: ?>
      <table class="data-table">
        <thead><tr><th>Produto</th><th>Qtd</th></tr></thead>
        <tbody>
          <?php foreach($estoqueBaixo as $p): ?>
          <tr>
            <td style="font-size:13px;"><?= sanitize($p['nome']) ?></td>
            <td><span class="tag <?= $p['estoque']==0?'tag-red':'tag-amber' ?>"><?= $p['estoque'] ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>
 
<?php require_once __DIR__ . '/includes/footer.php'; ?>
