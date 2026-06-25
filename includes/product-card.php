<?php
// includes/product-card.php (colar em qualquer loop de produtos
// requer: $p = linha da tabela produtos ou vw_produtos_completo
$preco     = (float)$p['preco'];
$promo     = isset($p['preco_promocional']) && $p['preco_promocional'] ? (float)$p['preco_promocional'] : null;
$precoShow = $promo ?? $preco;
$desconto  = $promo ? calcularDesconto($preco, $promo) : 0;
$semEstoque= (int)$p['estoque'] <= 0;
$slug = sanitize($p['slug']);
$nome = sanitize($p['nome']);
$marca= sanitize($p['marca'] ?? '');
?>
<article class="product-card">
    <a href="<?= APP_URL ?>/pages/produto.php?slug=<?= $slug ?>" class="product-card-img">
        <?php if ($desconto > 0): ?><span class="badge-promo">-<?= $desconto ?>%</span><?php endif; ?>
        <?php if (!empty($p['destaque'])): ?><span class="badge-destaque">Destaque</span><?php endif; ?>
        <?php if ($semEstoque): ?><span class="badge-sem-estoque">Esgotado</span><?php endif; ?>
        <?php if (!empty($p['imagem'])): ?>
            <img src="<?= APP_URL ?>/images/<?= sanitize($p['imagem']) ?>" alt="<?= $nome ?>" loading="lazy">
        <?php else: ?>
            <i class="fas fa-microchip ph-icon"></i>
        <?php endif; ?>
    </a>
    <div class="product-card-body">
        <?php if ($marca): ?><span class="product-brand"><?= $marca ?></span><?php endif; ?>
        <a href="<?= APP_URL ?>/pages/produto.php?slug=<?= $slug ?>" class="product-name"><?= $nome ?></a>
        <div class="product-price">
            <?php if ($promo): ?><div class="price-original"><?= formatarPreco($preco) ?></div><?php endif; ?>
            <div class="price-current"><?= formatarPreco($precoShow) ?></div>
            <div class="price-install">ou 12× de <?= formatarPreco($precoShow/12) ?> sem juros</div>
        </div>
        <div class="product-card-actions">
            <?php if (!$semEstoque): ?>
                <button class="btn btn-primary btn-sm" data-add-cart="<?= (int)$p['id'] ?>" style="flex:1;">
                    <i class="fas fa-cart-plus"></i> Adicionar
                </button>
                <a href="<?= APP_URL ?>/pages/produto.php?slug=<?= $slug ?>" class="btn btn-ghost btn-sm btn-icon-only" title="Ver detalhes">
                    <i class="fas fa-eye"></i>
                </a>
            <?php else: ?>
                <a href="<?= APP_URL ?>/pages/produto.php?slug=<?= $slug ?>" class="btn btn-ghost btn-sm" style="flex:1;">
                    <i class="fas fa-eye"></i> Ver produto
                </a>
            <?php endif; ?>
        </div>
    </div>
</article>
