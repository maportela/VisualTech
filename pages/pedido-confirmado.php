<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (!clienteLogado()) redirect(APP_URL . '/pages/login.php');
$id = (int)($_GET['id'] ?? 0);
$pedido = $id ? db()->fetch('SELECT * FROM vw_pedidos_completo WHERE id=? AND cliente_id=?',[$id,$_SESSION['cliente_id']]) : null;
if (!$pedido) redirect(APP_URL);
$itens = db()->fetchAll('SELECT * FROM itens_pedido WHERE pedido_id=?',[$id]);
$pageTitle = 'Pedido Confirmado';
require_once __DIR__ . '/../includes/header.php';
?>
<script>window._vtUrl = '<?= APP_URL ?>';</script>
<div class="container" style="padding:60px 0;max-width:700px;">
  <div style="text-align:center;margin-bottom:40px;">
    <div style="width:80px;height:80px;background:var(--green-glow);border:2px solid var(--green);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:36px;color:var(--green);">
      <i class="fas fa-check"></i>
    </div>
    <h1 style="font-family:'Rajdhani',sans-serif;font-size:32px;font-weight:700;margin-bottom:8px;">Pedido Confirmado!</h1>
    <p style="color:var(--text-2);font-size:15px;">Pedido <strong class="text-cyan">#<?= $pedido['id'] ?></strong> recebido com sucesso. Obrigado pela compra!</p>
  </div>
 
  <div class="data-table-wrap" style="margin-bottom:24px;">
    <div class="data-table-head"><h3>Resumo do Pedido</h3></div>
    <div style="padding:20px;">
      <?php foreach($itens as $item): ?>
      <div class="summary-row">
        <span class="summary-label"><?= sanitize($item['nome_produto']) ?> ×<?= $item['quantidade'] ?></span>
        <span><?= formatarPreco($item['subtotal']) ?></span>
      </div>
      <?php endforeach; ?>
      <div class="summary-row"><span class="summary-label">Subtotal</span><span><?= formatarPreco($pedido['subtotal']) ?></span></div>
      <div class="summary-row"><span class="summary-label">Frete</span><span><?= $pedido['frete']>0?formatarPreco($pedido['frete']):'Grátis' ?></span></div>
      <div class="summary-row total"><span class="summary-label fw-600">Total</span><span class="summary-total"><?= formatarPreco($pedido['total']) ?></span></div>
    </div>
  </div>
 
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:32px;">
    <div class="data-table-wrap" style="padding:20px;">
      <div style="font-size:11px;color:var(--text-3);text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;">Pagamento</div>
      <strong><?= pagamento_label($pedido['forma_pagamento']) ?></strong>
    </div>
    <div class="data-table-wrap" style="padding:20px;">
      <div style="font-size:11px;color:var(--text-3);text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;">Status</div>
      <?= statusLabel($pedido['status']) ?>
    </div>
  </div>
 
  <div style="display:flex;gap:12px;justify-content:center;">
    <a href="<?= APP_URL ?>/pages/minha-conta.php?tab=pedidos" class="btn btn-outline">
      <i class="fas fa-box"></i> Meus Pedidos
    </a>
    <a href="<?= APP_URL ?>/pages/produtos.php" class="btn btn-primary">
      <i class="fas fa-th-large"></i> Continuar Comprando
    </a>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
