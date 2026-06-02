<?php
require_once __DIR__ . '/../includes/auth.php';
$pageTitle = 'Estoque';
 
// Ajustar estoque
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $pid = (int)$_POST['produto_id'];
    $qtd = (int)$_POST['quantidade'];
    $tipo= $_POST['tipo'];
    $motivo=sanitize($_POST['motivo']??'');
    if ($tipo==='entrada') {
        db()->query('UPDATE produtos SET estoque=estoque+? WHERE id=?',[$qtd,$pid]);
    } elseif($tipo==='saida') {
        db()->query('UPDATE produtos SET estoque=GREATEST(0,estoque-?) WHERE id=?',[$qtd,$pid]);
    } else {
        db()->query('UPDATE produtos SET estoque=? WHERE id=?',[$qtd,$pid]);
    }
    db()->insert('INSERT INTO log_estoque (produto_id,vendedor_id,tipo,quantidade,motivo) VALUES (?,?,?,?,?)',
        [$pid,$_SESSION['vendedor_id'],$tipo,$qtd,$motivo]);
    flash('sucesso','Estoque atualizado!'); redirect(APP_URL.'/admin/pages/estoque.php');
}
 
$produtos    = db()->fetchAll('SELECT p.*,c.nome AS cat_nome FROM produtos p JOIN categorias c ON c.id=p.categoria_id WHERE p.ativo=1 ORDER BY p.estoque ASC');
$logEstoque  = db()->fetchAll('SELECT l.*,p.nome AS produto_nome,v.nome AS vendedor_nome FROM log_estoque l JOIN produtos p ON p.id=l.produto_id LEFT JOIN vendedores v ON v.id=l.vendedor_id ORDER BY l.criado_em DESC LIMIT 20');
require_once __DIR__ . '/../includes/header.php';
?>
<div class="admin-page-title">Controle de Estoque</div>
<div style="display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start;">
  <div class="data-table-wrap">
    <div class="data-table-head"><h3>Estoque Atual</h3></div>
    <table class="data-table">
      <thead><tr><th>Produto</th><th>Categoria</th><th>Estoque</th><th>Mínimo</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach($produtos as $p): ?>
        <tr>
          <td><div style="font-weight:500;font-size:13.5px;"><?= sanitize($p['nome']) ?></div><div style="font-size:12px;color:var(--text-2);"><?= sanitize($p['marca']??'') ?></div></td>
          <td style="font-size:13px;"><?= sanitize($p['cat_nome']) ?></td>
          <td style="font-weight:700;"><?= $p['estoque'] ?></td>
          <td style="color:var(--text-2);"><?= $p['estoque_minimo'] ?></td>
          <td><?php
            if($p['estoque']<=0) echo '<span class="tag tag-red">Esgotado</span>';
            elseif($p['estoque']<=$p['estoque_minimo']) echo '<span class="tag tag-amber">Baixo</span>';
            else echo '<span class="tag tag-green">OK</span>';
          ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
 
  <div style="display:flex;flex-direction:column;gap:20px;">
    <div class="data-table-wrap">
      <div class="data-table-head"><h3>Ajustar Estoque</h3></div>
      <form method="POST" style="padding:24px;">
        <div class="form-group">
          <label class="form-label">Produto</label>
          <select class="form-control" name="produto_id" required>
            <option value="">Selecione...</option>
            <?php foreach($produtos as $p): ?>
              <option value="<?= $p['id'] ?>"><?= sanitize($p['nome']) ?> (<?= $p['estoque'] ?> un.)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Tipo de Movimentação</label>
          <select class="form-control" name="tipo" required>
            <option value="entrada">Entrada (+ adicionar)</option>
            <option value="saida">Saída (- remover)</option>
            <option value="ajuste">Ajuste (= definir valor)</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Quantidade</label>
          <input class="form-control" type="number" name="quantidade" min="0" required>
        </div>
        <div class="form-group">
          <label class="form-label">Motivo (opcional)</label>
          <input class="form-control" type="text" name="motivo" placeholder="Ex: Recebimento de nota fiscal">
        </div>
        <button type="submit" class="btn btn-primary btn-full"><i class="fas fa-save"></i> Aplicar</button>
      </form>
    </div>
 
    <div class="data-table-wrap">
      <div class="data-table-head"><h3>Últimas Movimentações</h3></div>
      <table class="data-table">
        <thead><tr><th>Produto</th><th>Tipo</th><th>Qtd</th><th>Data</th></tr></thead>
        <tbody>
          <?php foreach($logEstoque as $l): ?>
          <tr>
            <td style="font-size:12px;"><?= sanitize($l['produto_nome']) ?></td>
            <td><span class="tag <?= $l['tipo']==='entrada'?'tag-green':($l['tipo']==='saida'?'tag-red':'tag-amber') ?>"><?= $l['tipo'] ?></span></td>
            <td><?= $l['quantidade'] ?></td>
            <td style="font-size:12px;color:var(--text-2);"><?= date('d/m H:i',strtotime($l['criado_em'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
