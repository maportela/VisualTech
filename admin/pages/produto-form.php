<?php // Criar e editar produto
require_once __DIR__ . '/../includes/auth.php';
$id         = (int)($_GET['id'] ?? 0);
$produto    = $id ? db()->fetch('SELECT * FROM produtos WHERE id=?',[$id]) : null;
$categorias = db()->fetchAll('SELECT * FROM categorias WHERE ativo=1 ORDER BY nome');
$erro = $suc = '';
 
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $dados = [
        'categoria_id'      => (int)$_POST['categoria_id'],
        'nome'              => sanitize($_POST['nome']),
        'slug'              => slugify($_POST['nome']),
        'descricao'         => sanitize($_POST['descricao']??''),
        'especificacoes'    => sanitize($_POST['especificacoes']??''),
        'preco'             => (float)str_replace(['.',',' ],['','.'],$_POST['preco']),
        'preco_promocional' => !empty($_POST['preco_promocional']) ? (float)str_replace(['.',',' ],['','.'],$_POST['preco_promocional']) : null,
        'estoque'           => (int)$_POST['estoque'],
        'estoque_minimo'    => (int)($_POST['estoque_minimo']??5),
        'marca'             => sanitize($_POST['marca']??''),
        'destaque'          => isset($_POST['destaque']) ? 1 : 0,
    ];
    if (!$dados['nome']) $erro='Nome é obrigatório.';
    elseif (!$dados['preco']) $erro='Preço é obrigatório.';
    else {
        if ($id) {
            db()->query('UPDATE produtos SET categoria_id=?,nome=?,slug=?,descricao=?,especificacoes=?,
                preco=?,preco_promocional=?,estoque=?,estoque_minimo=?,marca=?,destaque=? WHERE id=?',
                array_merge(array_values($dados),[$id]));
            flash('sucesso','Produto atualizado!');
        } else {
            $novoId = db()->insert('INSERT INTO produtos (categoria_id,nome,slug,descricao,especificacoes,
                preco,preco_promocional,estoque,estoque_minimo,marca,destaque) VALUES (?,?,?,?,?,?,?,?,?,?,?)',
                array_values($dados));
            flash('sucesso','Produto criado!');
            redirect(APP_URL.'/admin/pages/produto-form.php?id='.$novoId);
        }
        redirect(APP_URL.'/admin/pages/produtos.php');
    }
}
$pageTitle = $id ? 'Editar Produto' : 'Novo Produto';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="admin-page-title">
  <?= $id ? 'Editar Produto' : 'Novo Produto' ?>
  <a href="<?= APP_URL ?>/admin/pages/produtos.php" class="btn btn-ghost"><i class="fas fa-arrow-left"></i> Voltar</a>
</div>
 
<?php if($erro): ?><div class="alert alert-danger"><i class="fas fa-circle-xmark"></i> <?= sanitize($erro) ?></div><?php endif; ?>
 
<div class="data-table-wrap" style="max-width:800px;">
  <form method="POST" style="padding:28px;">
    <div class="form-row">
      <div class="form-group" style="grid-column:span 2;">
        <label class="form-label">Nome do Produto *</label>
        <input class="form-control" type="text" name="nome" required value="<?= sanitize($produto['nome']??$_POST['nome']??'') ?>">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Categoria *</label>
        <select class="form-control" name="categoria_id" required>
          <option value="">Selecione...</option>
          <?php foreach($categorias as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($produto['categoria_id']??'')==$cat['id']?'selected':'' ?>><?= sanitize($cat['nome']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Marca</label>
        <input class="form-control" type="text" name="marca" value="<?= sanitize($produto['marca']??'') ?>">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Preço (R$) *</label>
        <input class="form-control" type="text" name="preco" placeholder="0,00" required
               value="<?= isset($produto['preco'])?number_format($produto['preco'],2,',','.'):'' ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Preço Promocional (R$)</label>
        <input class="form-control" type="text" name="preco_promocional" placeholder="Deixe vazio se não há promoção"
               value="<?= isset($produto['preco_promocional'])&&$produto['preco_promocional']?number_format($produto['preco_promocional'],2,',','.'):'' ?>">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Estoque</label>
        <input class="form-control" type="number" name="estoque" min="0" value="<?= (int)($produto['estoque']??0) ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Estoque Mínimo (alerta)</label>
        <input class="form-control" type="number" name="estoque_minimo" min="0" value="<?= (int)($produto['estoque_minimo']??5) ?>">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Descrição</label>
      <textarea class="form-control" name="descricao" rows="3"><?= sanitize($produto['descricao']??'') ?></textarea>
    </div>
    <div class="form-group">
      <label class="form-label">Especificações</label>
      <textarea class="form-control" name="especificacoes" rows="3"
                placeholder="VRAM: 12GB GDDR6X | Clock: 2550MHz | TDP: 220W"><?= sanitize($produto['especificacoes']??'') ?></textarea>
      <span class="form-hint">Separe cada spec com | (pipe). Ex: VRAM: 8GB | Clock: 2.4GHz</span>
    </div>
    <div class="form-group" style="flex-direction:row;align-items:center;gap:10px;">
      <input type="checkbox" id="destaque" name="destaque" value="1" <?= !empty($produto['destaque'])?'checked':'' ?>>
      <label for="destaque" style="text-transform:none;letter-spacing:0;font-size:14px;cursor:pointer;">Exibir como produto em destaque na página inicial</label>
    </div>
    <div style="display:flex;gap:12px;margin-top:8px;">
      <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> <?= $id?'Salvar Alterações':'Criar Produto' ?></button>
      <a href="<?= APP_URL ?>/admin/pages/produtos.php" class="btn btn-ghost btn-lg">Cancelar</a>
    </div>
  </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
