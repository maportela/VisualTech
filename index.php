<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Home';
$destaques  = db()->fetchAll('SELECT * FROM vw_produtos_completo WHERE destaque=1 AND ativo=1 ORDER BY criado_em DESC LIMIT 8');
$promocoes  = db()->fetchAll('SELECT * FROM vw_produtos_completo WHERE preco_promocional IS NOT NULL AND ativo=1 ORDER BY RAND() LIMIT 4');
$categorias = db()->fetchAll('SELECT * FROM categorias WHERE ativo=1 ORDER BY nome');
require_once __DIR__ . '/includes/header.php';
?>
<script>window._vtUrl = '<?= APP_URL ?>';</script>
 
<!-- HERO -->
<section class="hero">
    <div class="container">
        <div class="hero-inner">
            <div class="hero-content">
                <div class="hero-tag"><i class="fas fa-bolt"></i> Lançamentos &amp; Promoções</div>
                <h1 class="hero-title">
                    Eleve seu<br>
                    <span class="accent">setup</span> ao<br>
                    próximo nível
                </h1>
                <p class="hero-desc">
                    Periféricos, eletrônicos e produtos gamer com os melhores preços.
                    Frete grátis acima de R$ 299 e parcelamento em até 12×.
                </p>
                <div class="hero-actions">
                    <a href="<?= APP_URL ?>/pages/produtos.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-th-large"></i> Ver Catálogo
                    </a>
                    <a href="<?= APP_URL ?>/pages/produtos.php?cat=placas-de-video" class="btn btn-outline btn-lg">
                        <i class="fas fa-microchip"></i> GPUs
                    </a>
                </div>
                <div class="hero-badges">
                    <div class="hero-badge"><i class="fas fa-truck"></i> Frete grátis acima de R$ 299</div>
                    <div class="hero-badge"><i class="fas fa-shield-halved"></i> Compra 100% segura</div>
                    <div class="hero-badge"><i class="fas fa-rotate-left"></i> Troca em 30 dias</div>
                </div>
            </div>
            <div class="hero-visual">
                <i class="fas fa-desktop hero-visual-icon"></i>
            </div>
        </div>
    </div>
</section>
 
<!-- CATEGORIAS -->
<div class="cat-strip">
    <div class="container">
        <div class="cat-strip-inner">
            <a href="<?= APP_URL ?>/pages/produtos.php" class="cat-pill active">
                <i class="fas fa-th-large"></i> Todos
            </a>
            <?php foreach ($categorias as $cat): ?>
            <a href="<?= APP_URL ?>/pages/produtos.php?cat=<?= sanitize($cat['slug']) ?>" class="cat-pill">
                <i class="fas <?= sanitize($cat['icone']) ?>"></i>
                <?= sanitize($cat['nome']) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
 
<!-- DESTAQUES -->
<?php if ($destaques): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><span>Destaques</span> da Semana</h2>
            <div class="section-line"></div>
            <a href="<?= APP_URL ?>/pages/produtos.php" class="btn btn-ghost btn-sm">Ver todos →</a>
        </div>
        <div class="products-grid">
            <?php foreach ($destaques as $p): include __DIR__ . '/includes/product-card.php'; endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
 
<!-- BANNER INFO -->
<section style="background:var(--bg-card);border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:40px 0;">
    <div class="container">
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:24px;text-align:center;">
            <div style="padding:20px;">
                <i class="fas fa-truck" style="font-size:28px;color:var(--cyan);margin-bottom:12px;display:block;"></i>
                <strong style="font-family:'Rajdhani',sans-serif;font-size:16px;display:block;margin-bottom:4px;">Frete Grátis</strong>
                <span style="font-size:13px;color:var(--text-2);">Compras acima de R$ 299</span>
            </div>
            <div style="padding:20px;">
                <i class="fas fa-credit-card" style="font-size:28px;color:var(--cyan);margin-bottom:12px;display:block;"></i>
                <strong style="font-family:'Rajdhani',sans-serif;font-size:16px;display:block;margin-bottom:4px;">12x sem juros</strong>
                <span style="font-size:13px;color:var(--text-2);">Nos principais cartões</span>
            </div>
            <div style="padding:20px;">
                <i class="fas fa-shield-halved" style="font-size:28px;color:var(--cyan);margin-bottom:12px;display:block;"></i>
                <strong style="font-family:'Rajdhani',sans-serif;font-size:16px;display:block;margin-bottom:4px;">Compra Protegida</strong>
                <span style="font-size:13px;color:var(--text-2);">Site seguro com criptografia</span>
            </div>
            <div style="padding:20px;">
                <i class="fas fa-rotate-left" style="font-size:28px;color:var(--cyan);margin-bottom:12px;display:block;"></i>
                <strong style="font-family:'Rajdhani',sans-serif;font-size:16px;display:block;margin-bottom:4px;">Troca Fácil</strong>
                <span style="font-size:13px;color:var(--text-2);">30 dias para devolver</span>
            </div>
        </div>
    </div>
</section>
 
<!-- PROMOÇÕES -->
<?php if ($promocoes): ?>
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><span>Promoções</span> Imperdíveis</h2>
            <div class="section-line"></div>
            <a href="<?= APP_URL ?>/pages/produtos.php?promo=1" class="btn btn-ghost btn-sm">Ver todas →</a>
        </div>
        <div class="products-grid">
            <?php foreach ($promocoes as $p): include __DIR__ . '/includes/product-card.php'; endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
