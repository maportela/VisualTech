<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Pedidos';
 
// Atualizar status
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['pedido_id'])) {
    db()->query('UPDATE pedidos SET status=?,vendedor_id=? WHERE id=?',
        [sanitize($_POST['status']),$_SESSION['vendedor_id'],(int)$_POST['pedido_id']]);
    flash('sucesso','Status atualizado!'); redirect(APP_URL.'/admin/pages/pedidos.php');
}
 
$status = trim($_GET['status'] ?? '');
$page   = max(1,(int)($_GET['page']??1));
$perPage= 20; $offset=($page-1)*$perPage;
$where  = []; $params=[];
if ($status) { $where[]='p.status=?'; $params[]=$status; }
$whereStr = $where ? 'WHERE '.implode(' AND ',$where) : '';
$total    = db()->count("SELECT COUNT(*) FROM pedidos p $whereStr",$params);
$pedidos  = db()->fetchAll("SELECT * FROM vw_pedidos_completo $whereStr ORDER BY criado_em DESC LIMIT $perPage OFFSET $offset",$params);
$totalPages=(int)ceil($total/$perPage);
 
require_once __DIR__ . '/../includes/header.php';
?>
<div class="admin-page-title">Pedidos</div>
 
<!-- Filtro status -->
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
  <?php foreach([''=> 'Todos','pendente'=>'Pendente','confirmado'=>'Confirmado','em_separacao'=>'Em Separação','enviado'=>'Enviado','entregue'=>'Entregue','cancelado'=>'Cancelado'] as $val=>$label): ?>
    <a href="?status=<?= $val ?>" class="cat-pill <?= $status===$val?'active':'' ?>"><?= $label ?></a>
  <?php endforeach; ?>
  <span class="results-count"><?= $total ?> pedido(s)</span>
</div>
 
<div class="data-table-wrap">
  <table class="data-table">
    <thead><tr><th>#</th><th>Cliente</th><th>Total</th><th>Pagamento</th><th>Status</th><th>Data</th><th>Ação</th></tr></thead>
    <tbody>
      <?php if(empty($pedidos)): ?>
        <tr><td colspan="7" style="text-align:center;color:var(--text-2);padding:40px;">Nenhum pedido.</td></tr>
      <?php else: ?>
        <?php foreach($pedidos as $p): ?>
        <tr>
          <td><strong>#<?= $p['id'] ?></strong></td>
          <td>
            <div style="font-size:13.5px;font-weight:500;"><?= sanitize($p['cliente_nome']) ?></div>
            <div style="font-size:12px;color:var(--text-2);"><?= sanitize($p['cliente_email']) ?></div>
          </td>
          <td class="text-cyan font-raj"><?= formatarPreco($p['total']) ?></td>
          <td style="font-size:13px;"><?= pagamento_label($p['forma_pagamento']) ?></td>
          <td><?= statusLabel($p['status']) ?></td>
          <td style="font-size:13px;color:var(--text-2);"><?= date('d/m/Y H:i',strtotime($p['criado_em'])) ?></td>
          <td>
            <form method="POST" style="display:flex;gap:6px;align-items:center;">
              <input type="hidden" name="pedido_id" value="<?= $p['id'] ?>">
              <select name="status" class="filter-select" style="font-size:12px;padding:6px 10px;">
                <?php foreach(['pendente','confirmado','em_separacao','enviado','entregue','cancelado'] as $s): ?>
                  <option value="<?= $s ?>" <?= $p['status']===$s?'selected':'' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
                <?php endforeach; ?>
              </select>
              <button type="submit" class="table-btn" title="Salvar"><i class="fas fa-check"></i></button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
 
<?php if($totalPages>1): ?>
<nav class="pagination" style="margin-top:24px;">
  <?php $qstr=$status?"&status=".urlencode($status):''; ?>
  <?php if($page>1): ?><a href="?page=<?= $page-1 ?><?= $qstr ?>" class="page-link">‹</a><?php endif; ?>
  <?php for($i=max(1,$page-2);$i<=min($totalPages,$page+2);$i++): ?>
    <a href="?page=<?= $i ?><?= $qstr ?>" class="page-link <?= $i===$page?'active':'' ?>"><?= $i ?></a>
  <?php endfor; ?>
  <?php if($page<$totalPages): ?><a href="?page=<?= $page+1 ?><?= $qstr ?>" class="page-link">›</a><?php endif; ?>
</nav>
<?php endif; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
