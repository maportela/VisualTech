<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Carrinho';
$itens     = getCarrinhoItens();
$subtotal  = getCarrinhoTotal();
$frete     = $subtotal >= 299 ? 0.0 : ($subtotal > 0 ? 29.90 : 0.0);
$total     = $subtotal + $frete;

require_once __DIR__ . '/../includes/header.php';
?>
<script>window._vtUrl = '<?= APP_URL ?>';</script>
<div class="page-header">
  <div class="container">
    <nav class="breadcrumb"><a href="<?= APP_URL ?>">Home</a><span>/</span><span>Carrinho</span></nav>
    <h1>Carrinho de Compras</h1>
  </div>
</div>

<div class="container">
  <?php if (empty($itens)): ?>
    <div class="empty-state" style="padding:100px 0;">
      <i class="fas fa-cart-xmark"></i>
      <h3>Seu carrinho está vazio</h3>
      <p>Adicione produtos incríveis ao carrinho e finalize sua compra.</p>
      <a href="<?= APP_URL ?>/pages/produtos.php" class="btn btn-primary btn-lg">
        <i class="fas fa-th-large"></i> Ver Produtos
      </a>
    </div>
  <?php else: ?>
    <div class="cart-page-layout">
      <div>
        <div class="cart-items-list">
          <?php foreach ($itens as $item):
            $precoUnit = (float)($item['preco_promocional'] ?: $item['preco']);
          ?>
          <div class="cart-item">
            <div class="cart-item-img">
              <?php if ($item['imagem']): ?>
                <img src="<?= APP_URL ?>/images/<?= sanitize($item['imagem']) ?>" alt="<?= sanitize($item['nome']) ?>">
              <?php else: ?>
                <i class="fas fa-microchip"></i>
              <?php endif; ?>
            </div>
            <div class="cart-item-info">
              <?php if ($item['marca']): ?><div class="cart-item-brand"><?= sanitize($item['marca']) ?></div><?php endif; ?>
              <div class="cart-item-name"><?= sanitize($item['nome']) ?></div>
              <div class="cart-item-actions">
                <div class="qty-control">
                  <button class="qty-btn qty-minus" aria-label="Diminuir">−</button>
                  <input class="qty-input" type="number" value="<?= (int)$item['quantidade'] ?>"
                         min="1" max="<?= (int)$item['estoque'] ?>"
                         data-cart-qty="<?= (int)$item['id'] ?>" aria-label="Quantidade">
                  <button class="qty-btn qty-plus" aria-label="Aumentar">+</button>
                </div>
                <button class="cart-remove" data-remove-cart="<?= (int)$item['id'] ?>">
                  <i class="fas fa-trash-can"></i> Remover
                </button>
              </div>
            </div>
            <div class="cart-item-price"><?= formatarPreco($precoUnit * $item['quantidade']) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Summary -->
      <div class="cart-summary">
        <h3 class="cart-summary-title">Resumo do Pedido</h3>
        <div class="summary-row">
          <span class="summary-label">Subtotal</span>
          <span class="summary-value"><?= formatarPreco($subtotal) ?></span>
        </div>
        <div class="summary-row">
          <span class="summary-label">Frete</span>
          <?php if ($frete == 0 && $subtotal > 0): ?>
            <span class="frete-gratis"><i class="fas fa-gift"></i> Grátis!</span>
          <?php elseif ($subtotal == 0): ?>
            <span class="summary-value text-muted">—</span>
          <?php else: ?>
            <span class="summary-value"><?= formatarPreco($frete) ?></span>
          <?php endif; ?>
        </div>
        <?php if ($subtotal > 0 && $frete > 0): ?>
        <div class="alert alert-info" style="font-size:12.5px;padding:10px 14px;margin:8px 0;">
          <i class="fas fa-truck"></i> Falta <?= formatarPreco(299 - $subtotal) ?> para frete grátis!
        </div>
        <?php endif; ?>
        <div class="summary-row total">
          <span class="summary-label fw-600">Total</span>
          <span class="summary-total"><?= formatarPreco($total) ?></span>
        </div>
        <div style="font-size:12px;color:var(--text-2);margin:8px 0 16px;">
          ou 12× de <?= formatarPreco($total / 12) ?> sem juros
        </div>
        <?php if (clienteLogado()): ?>
          <a href="<?= APP_URL ?>/pages/checkout.php" class="btn btn-primary btn-full btn-lg">
            <i class="fas fa-lock"></i> Finalizar Compra
          </a>
        <?php else: ?>
          <a href="<?= APP_URL ?>/pages/login.php" class="btn btn-primary btn-full btn-lg">
            <i class="fas fa-sign-in-alt"></i> Entrar para Comprar
          </a>
          <a href="<?= APP_URL ?>/pages/cadastro.php" class="btn btn-ghost btn-full" style="margin-top:8px;">
            Criar conta grátis
          </a>
        <?php endif; ?>
        <a href="<?= APP_URL ?>/pages/produtos.php" class="btn btn-ghost btn-full" style="margin-top:8px;font-size:13px;">
          <i class="fas fa-arrow-left"></i> Continuar comprando
        </a>
      </div>
    </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>