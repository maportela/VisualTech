<?php // Finalizando a compra
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (!clienteLogado()) { flash('info','Faça login para finalizar sua compra.'); redirect(APP_URL.'/pages/login.php'); }
$itens    = getCarrinhoItens();
if (empty($itens)) { flash('info','Seu carrinho está vazio.'); redirect(APP_URL.'/pages/carrinho.php'); }
$subtotal = getCarrinhoTotal();
$frete    = $subtotal >= 299 ? 0.0 : 29.90;
$total    = $subtotal + $frete;
$enderecos= db()->fetchAll('SELECT * FROM enderecos WHERE cliente_id=? ORDER BY principal DESC',[$_SESSION['cliente_id']]);
$erro     = '';
 
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $endId = (int)($_POST['endereco_id']??0);
    $forma = $_POST['forma_pagamento']??'';
    $parc  = (int)($_POST['parcelas']??1);
    $obs   = $_POST['observacoes']??'';
    if (!$endId)  $erro = 'Selecione um endereço de entrega.';
    elseif(!$forma) $erro = 'Selecione a forma de pagamento.';
    else {
        $pedidoId = criarPedido($endId,$forma,$parc,$obs);
        if ($pedidoId) { flash('sucesso','Pedido #'.$pedidoId.' realizado com sucesso!'); redirect(APP_URL.'/pages/pedido-confirmado.php?id='.$pedidoId); }
        else $erro = 'Erro ao criar pedido. Tente novamente.';
    }
}
$pageTitle = 'Finalizar Compra';
require_once __DIR__ . '/../includes/header.php';
?>
<script>window._vtUrl = '<?= APP_URL ?>';</script>
<div class="page-header">
  <div class="container">
    <nav class="breadcrumb">
      <a href="<?= APP_URL ?>">Home</a><span>/</span>
      <a href="<?= APP_URL ?>/pages/carrinho.php">Carrinho</a><span>/</span>
      <span>Finalizar Compra</span>
    </nav>
    <h1>Finalizar Compra</h1>
  </div>
</div>
 
<div class="container" style="padding-top:36px;padding-bottom:60px;">
  <?php if($erro): ?>
    <div class="alert alert-danger" style="max-width:900px;margin:0 auto 20px;">
      <i class="fas fa-circle-xmark"></i> <?= sanitize($erro) ?>
    </div>
  <?php endif; ?>
 
  <form method="POST">
  <div class="cart-page-layout">
    <div style="display:flex;flex-direction:column;gap:24px;">
 
      <!-- Endereço -->
      <div class="data-table-wrap">
        <div class="data-table-head"><h3>Endereço de Entrega</h3></div>
        <div style="padding:24px;">
          <?php if(empty($enderecos)): ?>
            <div class="alert alert-warning">
              <i class="fas fa-exclamation-triangle"></i>
              Você não tem endereços cadastrados.
              <a href="<?= APP_URL ?>/pages/minha-conta.php?tab=enderecos" class="text-cyan">Cadastrar endereço</a>
            </div>
          <?php else: ?>
            <?php foreach($enderecos as $end): ?>
            <label style="display:flex;gap:14px;align-items:flex-start;padding:16px;background:var(--bg-card2);border:1px solid var(--border);border-radius:var(--r-lg);margin-bottom:10px;cursor:pointer;">
              <input type="radio" name="endereco_id" value="<?= $end['id'] ?>" <?= $end['principal']?'checked':'' ?> style="margin-top:3px;">
              <div>
                <strong style="font-size:14px;"><?= sanitize($end['apelido']) ?></strong>
                <?php if($end['principal']): ?><span class="tag tag-cyan" style="margin-left:8px;font-size:11px;">Principal</span><?php endif; ?>
                <div style="font-size:13px;color:var(--text-2);margin-top:4px;">
                  <?= sanitize($end['rua']) ?>, <?= sanitize($end['numero']) ?>
                  <?php if($end['complemento']): ?>, <?= sanitize($end['complemento']) ?><?php endif; ?><br>
                  <?= sanitize($end['bairro']) ?> — <?= sanitize($end['cidade']) ?>/<?= sanitize($end['estado']) ?> — CEP: <?= sanitize($end['cep']) ?>
                </div>
              </div>
            </label>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
 
      <!-- Pagamento -->
      <div class="data-table-wrap">
        <div class="data-table-head"><h3>Forma de Pagamento</h3></div>
        <div style="padding:24px;">
          <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
            <?php foreach(['pix'=>['PIX','fa-pix','fab'],'cartao_credito'=>['Cartão de Crédito','fa-credit-card','fas'],'cartao_debito'=>['Cartão de Débito','fa-credit-card','fas'],'boleto'=>['Boleto','fa-barcode','fas']] as $val=>[$label,$icon,$prefix]): ?>
            <label style="display:flex;align-items:center;gap:12px;padding:14px;background:var(--bg-card2);border:1px solid var(--border);border-radius:var(--r-lg);cursor:pointer;">
              <input type="radio" name="forma_pagamento" value="<?= $val ?>" <?= $val==='pix'?'checked':'' ?>>
              <i class="<?= $prefix ?> <?= $icon ?>" style="font-size:20px;color:var(--cyan);width:24px;text-align:center;"></i>
              <span style="font-size:14px;"><?= $label ?></span>
            </label>
            <?php endforeach; ?>
          </div>
 
          <div style="margin-top:16px;" id="parcelasBox">
            <label class="form-label">Parcelas (Cartão de Crédito)</label>
            <select name="parcelas" class="form-control">
              <?php for($i=1;$i<=12;$i++): ?>
                <option value="<?= $i ?>"><?= $i ?>× de <?= formatarPreco($total/$i) ?> <?= $i===1?'(sem juros)':'sem juros' ?></option>
              <?php endfor; ?>
            </select>
          </div>
 
          <div class="form-group" style="margin-top:16px;">
            <label class="form-label" for="observacoes">Observações (opcional)</label>
            <textarea class="form-control" id="observacoes" name="observacoes" rows="2"
                      placeholder="Instruções de entrega, ponto de referência..."></textarea>
          </div>
        </div>
      </div>
    </div>
 
    <!-- Resumo -->
    <div class="cart-summary">
      <h3 class="cart-summary-title">Resumo do Pedido</h3>
      <?php foreach($itens as $item):
        $p=(float)($item['preco_promocional']?:$item['preco']); ?>
        <div class="summary-row" style="font-size:13px;">
          <span class="summary-label"><?= sanitize($item['nome']) ?> ×<?= $item['quantidade'] ?></span>
          <span><?= formatarPreco($p*$item['quantidade']) ?></span>
        </div>
      <?php endforeach; ?>
      <div class="summary-row"><span class="summary-label">Subtotal</span><span><?= formatarPreco($subtotal) ?></span></div>
      <div class="summary-row">
        <span class="summary-label">Frete</span>
        <?php if($frete==0): ?><span class="frete-gratis">Grátis!</span>
        <?php else: ?><span><?= formatarPreco($frete) ?></span><?php endif; ?>
      </div>
      <div class="summary-row total">
        <span class="summary-label fw-600">Total</span>
        <span class="summary-total"><?= formatarPreco($total) ?></span>
      </div>
      <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:16px;"
              <?= empty($enderecos)?'disabled':'' ?>>
        <i class="fas fa-check-circle"></i> Confirmar Pedido
      </button>
      <a href="<?= APP_URL ?>/pages/carrinho.php" class="btn btn-ghost btn-full" style="margin-top:8px;font-size:13px;">
        <i class="fas fa-arrow-left"></i> Voltar ao carrinho
      </a>
    </div>
  </div>
  </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
