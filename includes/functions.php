<?php
// VisualTech — includes/functions.php
require_once __DIR__ . '/config.php';
 
// ── AUTENTICAÇÃO CLIENTE ──────────────────────────────────
function clienteLogado(): bool { return !empty($_SESSION['cliente_id']); }
 
function getClienteAtual(): array|false {
    if (!clienteLogado()) return false;
    return db()->fetch('SELECT * FROM clientes WHERE id = ? AND ativo = 1',
        [$_SESSION['cliente_id']]);
}
 
function loginCliente(string $email, string $senha): bool {
    $c = db()->fetch('SELECT * FROM clientes WHERE email = ? AND ativo = 1', [trim($email)]);
    if ($c && password_verify($senha, $c['senha'])) {
        $_SESSION['cliente_id']   = $c['id'];
        $_SESSION['cliente_nome'] = $c['nome'];
        mergeCarrinhoSessao($c['id']);
        return true;
    }
    return false;
}
 
function logoutCliente(): void {
    unset($_SESSION['cliente_id'], $_SESSION['cliente_nome']);
}
 
function registrarCliente(array $dados): array {
    $email = trim($dados['email']);
    if (db()->count('SELECT COUNT(*) FROM clientes WHERE email = ?', [$email]))
        return ['erro' => 'E-mail já cadastrado.'];
    if (strlen($dados['senha']) < 6)
        return ['erro' => 'A senha deve ter pelo menos 6 caracteres.'];
    $hash = password_hash($dados['senha'], PASSWORD_BCRYPT, ['cost' => 12]);
    $id = db()->insert(
        'INSERT INTO clientes (nome,email,senha,cpf,telefone) VALUES (?,?,?,?,?)',
        [sanitize($dados['nome']),$email,$hash,
         sanitize($dados['cpf'] ?? ''),sanitize($dados['telefone'] ?? '')]
    );
    $_SESSION['cliente_id']   = $id;
    $_SESSION['cliente_nome'] = sanitize($dados['nome']);
    mergeCarrinhoSessao((int)$id);
    return ['sucesso' => true, 'id' => $id];
}
 
// ── AUTENTICAÇÃO ADMIN ────────────────────────────────────
function adminLogado(): bool { return !empty($_SESSION['vendedor_id']); }
 
function getAdminAtual(): array|false {
    if (!adminLogado()) return false;
    return db()->fetch('SELECT * FROM vendedores WHERE id = ? AND ativo = 1',
        [$_SESSION['vendedor_id']]);
}
 
function loginAdmin(string $email, string $senha): bool {
    $v = db()->fetch('SELECT * FROM vendedores WHERE email = ? AND ativo = 1', [trim($email)]);
    if ($v && password_verify($senha, $v['senha'])) {
        $_SESSION['vendedor_id']    = $v['id'];
        $_SESSION['vendedor_nome']  = $v['nome'];
        $_SESSION['vendedor_nivel'] = $v['nivel'];
        return true;
    }
    return false;
}
 
function logoutAdmin(): void {
    unset($_SESSION['vendedor_id'],$_SESSION['vendedor_nome'],$_SESSION['vendedor_nivel']);
}
 
function requireAdmin(): void {
    if (!adminLogado()) redirect(APP_URL . '/admin/index.php?erro=acesso');
}
 
// ── CARRINHO ──────────────────────────────────────────────
function getCarrinhoItens(): array {
    if (clienteLogado()) {
        return db()->fetchAll(
            'SELECT c.*,p.nome,p.preco,p.preco_promocional,p.imagem,p.estoque,p.marca
             FROM carrinho c JOIN produtos p ON c.produto_id=p.id
             WHERE c.cliente_id=? AND p.ativo=1 ORDER BY c.adicionado_em DESC',
            [$_SESSION['cliente_id']]);
    }
    return db()->fetchAll(
        'SELECT c.*,p.nome,p.preco,p.preco_promocional,p.imagem,p.estoque,p.marca
         FROM carrinho c JOIN produtos p ON c.produto_id=p.id
         WHERE c.sessao_id=? AND c.cliente_id IS NULL AND p.ativo=1
         ORDER BY c.adicionado_em DESC',
        [session_id()]);
}
 
function getCarrinhoCount(): int {
    return (int)array_sum(array_column(getCarrinhoItens(),'quantidade'));
}
 
function getCarrinhoTotal(): float {
    $total = 0.0;
    foreach (getCarrinhoItens() as $item) {
        $preco = (float)($item['preco_promocional'] ?: $item['preco']);
        $total += $preco * $item['quantidade'];
    }
    return $total;
}
 
function adicionarAoCarrinho(int $produtoId, int $qtd = 1): bool {
    $prod = db()->fetch('SELECT id,estoque FROM produtos WHERE id=? AND ativo=1',[$produtoId]);
    if (!$prod) return false;
    if (clienteLogado()) {
        $ex = db()->fetch('SELECT id,quantidade FROM carrinho WHERE cliente_id=? AND produto_id=?',
            [$_SESSION['cliente_id'],$produtoId]);
        if ($ex) {
            db()->query('UPDATE carrinho SET quantidade=? WHERE id=?',
                [min($ex['quantidade']+$qtd,$prod['estoque']),$ex['id']]);
        } else {
            db()->insert('INSERT INTO carrinho (cliente_id,produto_id,quantidade) VALUES (?,?,?)',
                [$_SESSION['cliente_id'],$produtoId,min($qtd,$prod['estoque'])]);
        }
    } else {
        $sid = session_id();
        $ex  = db()->fetch('SELECT id,quantidade FROM carrinho WHERE sessao_id=? AND produto_id=? AND cliente_id IS NULL',
            [$sid,$produtoId]);
        if ($ex) {
            db()->query('UPDATE carrinho SET quantidade=? WHERE id=?',
                [min($ex['quantidade']+$qtd,$prod['estoque']),$ex['id']]);
        } else {
            db()->insert('INSERT INTO carrinho (sessao_id,produto_id,quantidade) VALUES (?,?,?)',
                [$sid,$produtoId,min($qtd,$prod['estoque'])]);
        }
    }
    return true;
}
 
function removerDoCarrinho(int $itemId): void {
    if (clienteLogado())
        db()->query('DELETE FROM carrinho WHERE id=? AND cliente_id=?',[$itemId,$_SESSION['cliente_id']]);
    else
        db()->query('DELETE FROM carrinho WHERE id=? AND sessao_id=? AND cliente_id IS NULL',
            [$itemId,session_id()]);
}
 
function atualizarQuantidade(int $itemId, int $qtd): void {
    if ($qtd <= 0) { removerDoCarrinho($itemId); return; }
    if (clienteLogado())
        db()->query('UPDATE carrinho SET quantidade=? WHERE id=? AND cliente_id=?',
            [$qtd,$itemId,$_SESSION['cliente_id']]);
    else
        db()->query('UPDATE carrinho SET quantidade=? WHERE id=? AND sessao_id=? AND cliente_id IS NULL',
            [$qtd,$itemId,session_id()]);
}
 
function limparCarrinho(): void {
    if (clienteLogado())
        db()->query('DELETE FROM carrinho WHERE cliente_id=?',[$_SESSION['cliente_id']]);
    else
        db()->query('DELETE FROM carrinho WHERE sessao_id=? AND cliente_id IS NULL',[session_id()]);
}
 
function mergeCarrinhoSessao(int $clienteId): void {
    $itens = db()->fetchAll('SELECT * FROM carrinho WHERE sessao_id=? AND cliente_id IS NULL',[session_id()]);
    foreach ($itens as $item) {
        $ex = db()->fetch('SELECT id,quantidade FROM carrinho WHERE cliente_id=? AND produto_id=?',
            [$clienteId,$item['produto_id']]);
        if ($ex) {
            db()->query('UPDATE carrinho SET quantidade=quantidade+? WHERE id=?',
                [$item['quantidade'],$ex['id']]);
            db()->query('DELETE FROM carrinho WHERE id=?',[$item['id']]);
        } else {
            db()->query('UPDATE carrinho SET cliente_id=?,sessao_id=NULL WHERE id=?',
                [$clienteId,$item['id']]);
        }
    }
}
 
// ── PEDIDOS ───────────────────────────────────────────────
function criarPedido(int $enderecoId, string $formaPagamento, int $parcelas=1, string $obs=''): int|false {
if (!clienteLogado()) return false;
    $itens = getCarrinhoItens();
    if (empty($itens)) return false;
    $subtotal = 0.0;
    foreach ($itens as $item) {
        $preco = (float)($item['preco_promocional'] ?: $item['preco']);
        $subtotal += $preco * $item['quantidade'];
    }
    $frete = $subtotal >= 299 ? 0.0 : 29.90;
    $total = $subtotal + $frete;
    $pedidoId = db()->insert(
        'INSERT INTO pedidos (cliente_id,endereco_id,subtotal,frete,total,forma_pagamento,parcelas,observacoes)
         VALUES (?,?,?,?,?,?,?,?)',
        [$_SESSION['cliente_id'],$enderecoId,$subtotal,$frete,$total,$formaPagamento,$parcelas,sanitize($obs)]
    );
    if (!$pedidoId) return false;
    foreach ($itens as $item) {
        $preco = (float)($item['preco_promocional'] ?: $item['preco']);
        db()->insert(
            'INSERT INTO itens_pedido (pedido_id,produto_id,nome_produto,quantidade,preco_unitario,subtotal)
             VALUES (?,?,?,?,?,?)',
            [$pedidoId,$item['produto_id'],$item['nome'],$item['quantidade'],$preco,$preco*$item['quantidade']]
        );
        db()->query('UPDATE produtos SET estoque=estoque-? WHERE id=?',[$item['quantidade'],$item['produto_id']]);
        db()->insert('INSERT INTO log_estoque (produto_id,tipo,quantidade,motivo) VALUES (?,?,?,?)',
            [$item['produto_id'],'saida',$item['quantidade'],"Pedido #{$pedidoId}"]);
    }
    limparCarrinho();
    return (int)$pedidoId;
}
 
// ── HELPERS ───────────────────────────────────────────────
function formatarPreco(float $valor): string {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}
function calcularDesconto(float $original, float $promo): int {
    if ($original <= 0) return 0;
    return (int)round((($original - $promo) / $original) * 100);
}
function redirect(string $url): never { header("Location: $url"); exit; }
function sanitize(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}
function flash(string $tipo, string $msg): void {
    $_SESSION['flash'] = ['tipo' => $tipo, 'msg' => $msg];
}
function getFlash(): array|null {
    if (!empty($_SESSION['flash'])) { $f=$_SESSION['flash']; unset($_SESSION['flash']); return $f; }
    return null;
}
function slugify(string $texto): string {
    $texto = mb_strtolower($texto,'UTF-8');
    $texto = strtr($texto,['á'=>'a','à'=>'a','â'=>'a','ã'=>'a','é'=>'e','ê'=>'e',
        'í'=>'i','ó'=>'o','ô'=>'o','õ'=>'o','ú'=>'u','ç'=>'c','ñ'=>'n']);
    $texto = preg_replace('/[^a-z0-9s-]/','', $texto);
    return preg_replace('/[s-]+/','-',trim($texto));
}
function statusLabel(string $status): string {
    $labels = [
        'pendente'     => '<span class="status status-pendente">Pendente</span>',
        'confirmado'   => '<span class="status status-confirmado">Confirmado</span>',
        'em_separacao' => '<span class="status status-enviado">Em Separação</span>',
        'enviado'      => '<span class="status status-enviado">Enviado</span>',
        'entregue'     => '<span class="status status-entregue">Entregue</span>',
        'cancelado'    => '<span class="status status-cancelado">Cancelado</span>',
    ];
    return $labels[$status] ?? $status;
}
function pagamento_label(string $p): string {
    $l=['pix'=>'PIX','cartao_credito'=>'Cartão de Crédito',
        'cartao_debito'=>'Cartão de Débito','boleto'=>'Boleto'];
    return $l[$p] ?? $p;
}
