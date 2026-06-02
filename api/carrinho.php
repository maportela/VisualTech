<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');

$acao = $_POST['acao'] ?? '';

switch ($acao) {
    case 'adicionar':
        $pid = (int)($_POST['produto_id'] ?? 0);
        $qty = max(1, (int)($_POST['quantidade'] ?? 1));
        $ok  = adicionarAoCarrinho($pid, $qty);
        echo json_encode(['sucesso' => $ok, 'quantidade_total' => getCarrinhoCount()]);
        break;

    case 'remover':
        removerDoCarrinho((int)($_POST['item_id'] ?? 0));
        echo json_encode(['sucesso' => true, 'quantidade_total' => getCarrinhoCount()]);
        break;

    case 'atualizar':
        atualizarQuantidade((int)($_POST['item_id'] ?? 0), (int)($_POST['quantidade'] ?? 1));
        echo json_encode(['sucesso' => true, 'quantidade_total' => getCarrinhoCount()]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['erro' => 'Ação inválida']);
}