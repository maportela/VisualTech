<?php // Catálogo com filtros e páginas
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
 
$catSlug  = trim($_GET['cat']   ?? '');
$busca    = trim($_GET['q']     ?? '');
$ordem    = trim($_GET['ordem'] ?? 'destaque');
$promo    = isset($_GET['promo']) && $_GET['promo'] == '1';
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 16;
$offset   = ($page - 1) * $perPage;
 
$catAtual   = $catSlug ? db()->fetch('SELECT * FROM categorias WHERE slug=? AND ativo=1',[$catSlug]) : null;
$categorias = db()->fetchAll('SELECT * FROM categorias WHERE ativo=1 ORDER BY nome');
 
$where  = ['p.ativo = 1'];
$params = [];
if ($catAtual) { $where[] = 'p.categoria_id = ?'; $params[] = $catAtual['id']; }
if ($busca)    { $where[] = '(p.nome LIKE ? OR p.marca LIKE ? OR p.descricao LIKE ?)';
                 $params  = array_merge($params,["%%{$busca}%%","%%{$busca}%%","%%{$busca}%%"]); }
if ($promo)    { $where[] = 'p.preco_promocional IS NOT NULL'; }
$whereStr = implode(' AND ', $where);
 
$ordemMap = ['destaque'=>'p.destaque DESC,p.criado_em DESC','preco_asc'=>'preco_show ASC',
             'preco_desc'=>'preco_show DESC','nome'=>'p.nome ASC','mais_recentes'=>'p.criado_em DESC'];
$ordemSql = $ordemMap[$ordem] ?? $ordemMap['destaque'];
 
$baseSelect = "SELECT p.*,c.nome AS categoria_nome,c.slug AS categoria_slug,
    COALESCE(p.preco_promocional,p.preco) AS preco_show
    FROM produtos p JOIN categorias c ON c.id=p.categoria_id WHERE $whereStr";
 
$total      = db()->count("SELECT COUNT(*) FROM produtos p JOIN categorias c ON c.id=p.categoria_id WHERE $whereStr",$params);
$produtos   = db()->fetchAll("$baseSelect ORDER BY $ordemSql LIMIT $perPage OFFSET $offset",$params);
$totalPages = (int)ceil($total/$perPage);
$pageTitle  = $catAtual ? sanitize($catAtual['nome']) : ($busca ? 'Busca: '.sanitize($busca) : 'Produtos');
 
require_once __DIR__ . '/../includes/header.php';
?>
<script>window._vtUrl = '<?= APP_URL ?>';</script>
 
<div class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?= APP_URL ?>">Home</a><span>/</span>
            <span><?= $catAtual ? sanitize($catAtual['nome']) : ($busca ? 'Busca: "'.sanitize($busca).'"' : 'Todos os Produtos') ?></span>
        </nav>
        <h1><?= $pageTitle ?></h1>
    </div>
</div>
 
<div class="filter-bar">
    <div class="container">
        <div class="filter-inner">
            <div class="cat-strip-inner" style="flex:1;">
                <a href="<?= APP_URL ?>/pages/produtos.php" class="cat-pill <?= !$catSlug?'active':'' ?>">Todas</a>
                <?php foreach($categorias as $cat): ?>
                <a href="<?= APP_URL ?>/pages/produtos.php?cat=<?= urlencode($cat['slug']) ?>" class="cat-pill <?= $catSlug===$cat['slug']?'active':'' ?>">
                    <i class="fas <?= sanitize($cat['icone']) ?>"></i> <?= sanitize($cat['nome']) ?>
                </a>
                <?php endforeach; ?>
            </div>
            <form method="GET" style="display:flex;gap:8px;align-items:center;flex-shrink:0;">
                <?php if($catSlug): ?><input type="hidden" name="cat" value="<?= sanitize($catSlug) ?>"><?php endif; ?>
                <?php if($busca):   ?><input type="hidden" name="q"   value="<?= sanitize($busca) ?>"><?php endif; ?>
                <select name="ordem" class="filter-select" onchange="this.form.submit()">
                    <option value="destaque"      <?= $ordem==='destaque'?'selected':'' ?>>Destaques</option>
                    <option value="mais_recentes" <?= $ordem==='mais_recentes'?'selected':'' ?>>Mais recentes</option>
                    <option value="preco_asc"     <?= $ordem==='preco_asc'?'selected':'' ?>>Menor preço</option>
                    <option value="preco_desc"    <?= $ordem==='preco_desc'?'selected':'' ?>>Maior preço</option>
                    <option value="nome"          <?= $ordem==='nome'?'selected':'' ?>>A-Z</option>
                </select>
            </form>
            <span class="results-count"><?= $total ?> produto(s)</span>
        </div>
    </div>
</div>
 
<section class="section" style="padding-top:36px;">
    <div class="container">
        <?php if(empty($produtos)): ?>
            <div class="empty-state">
                <i class="fas fa-search-minus"></i>
                <h3>Nenhum produto encontrado</h3>
                <p>Tente buscar por outro termo ou explore nossas categorias.</p>
                <a href="<?= APP_URL ?>/pages/produtos.php" class="btn btn-primary">Ver todos</a>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach($produtos as $p): include __DIR__.'/../includes/product-card.php'; endforeach; ?>
            </div>
            <?php if($totalPages > 1): ?>
            <nav class="pagination">
                <?php
                $qs = array_filter(['cat'=>$catSlug,'q'=>$busca,'ordem'=>$ordem]);
                $qstr = ($q=http_build_query($qs)) ? "&$q" : '';
                ?>
                <?php if($page>1): ?><a href="?page=<?= $page-1 ?><?= $qstr ?>" class="page-link">‹</a><?php endif; ?>
                <?php for($i=max(1,$page-2);$i<=min($totalPages,$page+2);$i++): ?>
                    <a href="?page=<?= $i ?><?= $qstr ?>" class="page-link <?= $i===$page?'active':'' ?>"><?= $i ?></a>
                <?php endfor; ?>
                <?php if($page<$totalPages): ?><a href="?page=<?= $page+1 ?><?= $qstr ?>" class="page-link">›</a><?php endif; ?>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
