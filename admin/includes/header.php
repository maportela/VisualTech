<?php
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= isset($pageTitle)?sanitize($pageTitle).' — ':'' ?>Admin VisualTech</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?= APP_URL ?>/css/style.css">
</head><body>
<?php $flash=getFlash(); if($flash): ?>
<div class="flash flash-<?= sanitize($flash['tipo']) ?>" id="flashMsg">
  <span><?= sanitize($flash['msg']) ?></span>
  <button onclick="this.parentElement.remove()">×</button>
</div>
<?php endif; ?>
<div class="admin-wrap">
<aside class="admin-sidebar">
  <div class="admin-sidebar-brand">
    <a href="<?= APP_URL ?>/admin/dashboard.php" class="logo">
      <span class="logo-vt">VT</span>
      <span class="logo-text" style="font-size:17px;">Visual<strong>Tech</strong></span>
    </a>
    <div style="font-size:10px;color:var(--text-3);text-transform:uppercase;letter-spacing:1.2px;margin-top:4px;">Admin Panel</div>
  </div>
  <nav class="admin-sidebar-nav">
    <div class="admin-nav-group">
      <span class="admin-nav-label">Geral</span>
      <a href="<?= APP_URL ?>/admin/dashboard.php" class="admin-nav-item <?= basename($_SERVER['PHP_SELF'])==='dashboard.php'?'active':'' ?>">
        <i class="fas fa-chart-line"></i> Dashboard
      </a>
    </div>
    <div class="admin-nav-group">
      <span class="admin-nav-label">Loja</span>
      <a href="<?= APP_URL ?>/admin/pages/produtos.php" class="admin-nav-item <?= basename($_SERVER['PHP_SELF'])==='produtos.php'?'active':'' ?>">
        <i class="fas fa-box"></i> Produtos
      </a>
      <a href="<?= APP_URL ?>/admin/pages/produto-form.php" class="admin-nav-item">
        <i class="fas fa-plus"></i> Novo Produto
      </a>
      <a href="<?= APP_URL ?>/admin/pages/estoque.php" class="admin-nav-item <?= basename($_SERVER['PHP_SELF'])==='estoque.php'?'active':'' ?>">
        <i class="fas fa-warehouse"></i> Estoque
      </a>
    </div>
    <div class="admin-nav-group">
      <span class="admin-nav-label">Vendas</span>
      <a href="<?= APP_URL ?>/admin/pages/pedidos.php" class="admin-nav-item <?= basename($_SERVER['PHP_SELF'])==='pedidos.php'?'active':'' ?>">
        <i class="fas fa-shopping-bag"></i> Pedidos
      </a>
    </div>
    <div class="admin-nav-group">
      <span class="admin-nav-label">Usuários</span>
      <a href="<?= APP_URL ?>/admin/pages/clientes.php" class="admin-nav-item <?= basename($_SERVER['PHP_SELF'])==='clientes.php'?'active':'' ?>">
        <i class="fas fa-users"></i> Clientes
      </a>
      <a href="<?= APP_URL ?>/admin/pages/vendedores.php" class="admin-nav-item <?= basename($_SERVER['PHP_SELF'])==='vendedores.php'?'active':'' ?>">
        <i class="fas fa-user-tie"></i> Vendedores
      </a>
    </div>
  </nav>
  <div class="admin-sidebar-footer">
    <div style="font-size:12px;color:var(--text-2);margin-bottom:8px;"><?= sanitize($adminAtual['nome']??'Admin') ?></div>
    <a href="<?= APP_URL ?>/admin/logout.php" class="btn btn-ghost btn-sm btn-full"><i class="fas fa-sign-out-alt"></i> Sair</a>
  </div>
</aside>
<div class="admin-content">
