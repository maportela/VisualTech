<?php // Detalhe do produto
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
 
$slug = trim($_GET['slug'] ?? '');
if (!$slug) redirect(APP_URL . '/pages/produtos.php');
 
$produto = db()->fetch('SELECT * FROM vw_produtos_completo WHERE slug = ? AND ativo = 1', [$slug]);
if (!$produto) redirect(APP_URL . '/pages/produtos.php');
 
$relacionados = db()->fetchAll(
    'SELECT * FROM vw_produtos_completo WHERE categoria_id=? AND id!=? AND ativo=1 ORDER BY RAND() LIMIT 4',
    [$produto['categoria_id'], $produto['id']]
);
 
$preco     = (float)$produto['preco'];
$promo     = $produto['preco_promocional'] ? (float)$produto['preco_promocional'] : null;
$precoShow = $promo ?? $preco;
$desconto  = $promo ? calcularDesconto($preco, $promo) : 0;
$semEstoque= (int)$produto['estoque'] <= 0;
$pageTitle = sanitize($produto['nome']);
 
require_once __DIR__ . '/../includes/header.php';
?>
<script>window._vtUrl = '<?= APP_URL ?>';</script>
 
<div class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?= APP_URL ?>">Home</a><span>/</span>
            <a href="<?= APP_URL ?>/pages/produtos.php?cat=<?= sanitize($produto['categoria_slug']) ?>"><?= sanitize($produto['categoria_nome']) ?></a>
            <span>/</span><span><?= sanitize($produto['nome']) ?></span>
        </nav>
    </div>
</div>
 
<div class="container">
    <div class="product-detail">
        <!-- Imagem -->
        <div class="product-gallery-main">
            <?php if($produto['imagem']): ?>
                <img src="<?= APP_URL ?>/images/<?= sanitize($produto['imagem']) ?>" alt="<?= sanitize($produto['nome']) ?>">
            <?php else: ?>
                <i class="fas fa-microchip ph-icon"></i>
            <?php endif; ?>
        </div>
 
        <!-- Info -->
        <div class="product-detail-info">
            <div class="product-detail-brand"><?= sanitize($produto['marca'] ?? '') ?></div>
            <h1 class="product-detail-name"><?= sanitize($produto['nome']) ?></h1>
            <p class="product-detail-desc"><?= sanitize($produto['descricao'] ?? '') ?></p>
 
            <!-- Preço -->
            <div class="product-price-box">
                <?php if($promo): ?>
                    <div class="price-original"><?= formatarPreco($preco) ?></div>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="price-current"><?= formatarPreco($promo) ?></div>
                        <span class="badge-promo" style="position:static;">-<?= $desconto ?>%</span>
                    </div>
                <?php else: ?>
                    <div class="price-current"><?= formatarPreco($preco) ?></div>
                <?php endif; ?>
                <div class="price-install" style="margin-top:8px;">
                    ou 12× de <?= formatarPreco($precoShow/12) ?> sem juros
                </div>
            </div>
 
            <!-- Estoque -->
            <?php if($semEstoque): ?>
                <span class="stock-badge stock-out"><i class="fas fa-times-circle"></i> Sem estoque</span>
            <?php elseif($produto['estoque'] <= 5): ?>
                <span class="stock-badge stock-low"><i class="fas fa-exclamation-circle"></i> Últimas <?= $produto['estoque'] ?> unidades</span>
            <?php else: ?>
                <span class="stock-badge stock-in"><i class="fas fa-check-circle"></i> Em estoque</span>
            <?php endif; ?>
 
            <!-- Adicionar no carrinho -->
            <?php if(!$semEstoque): ?>
            <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
                <div class="qty-control">
                    <button class="qty-btn qty-minus">−</button>
                    <input class="qty-input" type="number" id="qty" value="1" min="1" max="<?= (int)$produto['estoque'] ?>">
                    <button class="qty-btn qty-plus">+</button>
                </div>
                <button class="btn btn-primary btn-lg" data-add-cart="<?= (int)$produto['id'] ?>" style="flex:1;">
                    <i class="fas fa-cart-plus"></i> Adicionar ao Carrinho
                </button>
            </div>
            <?php endif; ?>
 
            <!-- Especificações -->
            <?php if(!empty($produto['especificacoes'])): ?>
            <div>
                <h3 style="font-family:'Rajdhani',sans-serif;font-size:18px;font-weight:700;margin-bottom:12px;">Especificações</h3>
                <table class="spec-table">
                    <?php foreach(explode('|',$produto['especificacoes']) as $spec):
                        $parts = explode(':',$spec,2);
                        if(count($parts)<2) continue;
                    ?>
                    <tr>
                        <td><?= sanitize(trim($parts[0])) ?></td>
                        <td><?= sanitize(trim($parts[1])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
 
    <!-- Relacionados -->
    <?php if($relacionados): ?>
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">Produtos <span>Relacionados</span></h2>
            <div class="section-line"></div>
        </div>
        <div class="products-grid">
            <?php foreach($relacionados as $p): include __DIR__.'/../includes/product-card.php'; endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
