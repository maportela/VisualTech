<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Produtos';
 
// Excluir produto
if (isset($_GET['del'])) {
    db()->query('UPDATE produtos SET ativo=0 WHERE id=?',[(int)$_GET['del']]);
    flash('sucesso','Produto desativado.'); redirect(APP_URL.'/admin/pages/produtos.php');
}
 
$busca    = trim($_GET['q'] ?? '');
$catFiltro= trim($_GET['cat'] ?? '');
$page     = max(1,(int)($_GET['page']??1));
$perPage  = 20;
$offset   = ($page-1)*$perPage;
$where    = ['p.ativo=1'];
$params   = [];
if ($busca)    { $where[]='(p.nome LIKE ? OR p.marca LIKE ?)'; $params=array_merge($params,["%%{$busca}%%","%%{$busca}%%"]); }
if ($catFiltro){ $where[]='c.slug=?'; $params[]=$catFiltro; }
$whereStr = implode(' AND ',$where);
$total    = db()->count("SELECT COUNT(*) FROM produtos p JOIN categorias c ON c.id=p.categoria_id WHERE $whereStr",$params);
$produtos = db()->fetchAll("SELECT p.*,c.nome AS cat_nome FROM produtos p JOIN categorias c ON c.id=p.categoria_id WHERE $whereStr ORDER BY p.criado_em DESC LIMIT $perPage OFFSET $offset",$params);
$categorias=db()->fetchAll('SELECT * FROM categorias WHERE ativo=1 ORDER BY nome');
$totalPages=(int)ceil($total/$perPage);
 
require_once __DIR__ . '/../includes/header.php';
?>
<div class="admin-page-title">
  Produtos
  <a href="<?= APP_URL ?>/admin/pages/produto-form.php" class="btn btn-primary"><i class="fas fa-plus"></i> Novo Produto</a>
</div>
 
<!-- Filtros -->
<div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
  <form method="GET" style="display:flex;gap:10px;flex:1;flex-wrap:wrap;">
    <div class="header-search" style="flex:1;min-width:200px;">
      <form method="GET">
        <input type="text" name="q" placeholder="Buscar produto..." value="<?= sanitize($busca) ?>">
        <button type="submit"><i class="fas fa-search"></i></button>
      </form>
    </div>
    <select name="cat" class="filter-select" onchange="this.form.submit()">
      <option value="">Todas as categorias</option>
      <?php foreach($categorias as $cat): ?>
        <option value="<?= sanitize($cat['slug']) ?>" <?= $catFiltro===$cat['slug']?'selected':'' ?>><?= sanitize($cat['nome']) ?></option>
      <?php endforeach; ?>
    </select>
    <?php if($busca): ?><input type="hidden" name="q" value="<?= sanitize($busca) ?>"><?php endif; ?>
  </form>
  <span class="results-count"><?= $total ?> produto(s)</span>
</div>
<div class="data-table-wrap">
  <table class="data-table">
    <thead><tr><th>Produto</th><th>Categoria</th><th>Preço</th><th>Promo</th><th>Estoque</th><th>Destaque</th><th>Ações</th></tr></thead>
    <tbody>
      <?php if(empty($produtos)): ?>
        <tr><td colspan="7" style="text-align:center;color:var(--text-2);padding:40px;">Nenhum produto encontrado.</td></tr>
      <?php else: ?>
        <?php foreach($produtos as $p): ?>
        <tr>
          <td>
            <div style="font-weight:500;font-size:13.5px;"><?= sanitize($p['nome']) ?></div>
            <div style="font-size:12px;color:var(--text-2);"><?= sanitize($p['marca']??'') ?></div>
          </td>
          <td style="font-size:13px;"><?= sanitize($p['cat_nome']) ?></td>
          <td class="text-cyan"><?= formatarPreco($p['preco']) ?></td>
          <td><?= $p['preco_promocional'] ? '<span class="tag tag-amber">'.formatarPreco($p['preco_promocional']).'</span>' : '—' ?></td>
          <td>
            <?php if($p['estoque']<=0): ?><span class="tag tag-red"><?= $p['estoque'] ?></span>
            <?php elseif($p['estoque']<=$p['estoque_minimo']): ?><span class="tag tag-amber"><?= $p['estoque'] ?></span>
            <?php else: ?><span class="tag tag-green"><?= $p['estoque'] ?></span><?php endif; ?>
          </td>
          <td><?= $p['destaque'] ? '<span class="tag tag-cyan">Sim</span>' : '—' ?></td>
          <td>
            <div class="table-actions">
              <a href="<?= APP_URL ?>/pages/produto.php?slug=<?= sanitize($p['slug']) ?>" class="table-btn" title="Ver na loja" target="_blank"><i class="fas fa-eye"></i></a>
              <a href="<?= APP_URL ?>/admin/pages/produto-form.php?id=<?= $p['id'] ?>" class="table-btn" title="Editar"><i class="fas fa-edit"></i></a>
              <a href="?del=<?= $p['id'] ?>" class="table-btn danger" title="Desativar" onclick="return confirm('Desativar produto?')"><i class="fas fa-trash"></i></a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>
 
<?php if($totalPages>1): ?>
<nav class="pagination" style="margin-top:24px;">
  <?php
  $qstr='';
  if($busca)    $qstr.='&q='.urlencode($busca);
  if($catFiltro)$qstr.='&cat='.urlencode($catFiltro);
  ?>
  <?php if($page>1): ?><a href="?page=<?= $page-1 ?><?= $qstr ?>" class="page-link">‹</a><?php endif; ?>
  <?php for($i=max(1,$page-2);$i<=min($totalPages,$page+2);$i++): ?>
    <a href="?page=<?= $i ?><?= $qstr ?>" class="page-link <?= $i===$page?'active':'' ?>"><?= $i ?></a>
  <?php endfor; ?>
  <?php if($page<$totalPages): ?><a href="?page=<?= $page+1 ?><?= $qstr ?>" class="page-link">›</a><?php endif; ?>
</nav>
<?php endif; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
