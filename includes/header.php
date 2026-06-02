<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' — ' : '' ?>VisualTech</title>
    <meta name="description" content="VisualTech — Periféricos, eletrônicos e produtos gamer. Os melhores preços em hardware e acessórios gaming.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/style.css">
    <?php if (isset($extraCss)) echo $extraCss; ?>
</head>
<body>

<?php $flash = getFlash(); if ($flash): ?>
<div class="flash flash-<?= sanitize($flash['tipo']) ?>" id="flashMsg" role="alert">
    <i class="fas <?= $flash['tipo'] === 'sucesso' ? 'fa-circle-check' : ($flash['tipo'] === 'erro' ? 'fa-circle-xmark' : 'fa-circle-info') ?>"></i>
    <span><?= sanitize($flash['msg']) ?></span>
    <button onclick="this.parentElement.remove()" aria-label="Fechar">×</button>
</div>
<?php endif; ?>

<header class="header" id="header">
    <div class="container">

        <!-- Logo -->
        <a href="<?= APP_URL ?>" class="logo" aria-label="VisualTech - Página Inicial">
            <span class="logo-vt" aria-hidden="true">VT</span>
            <span class="logo-text">Visual<strong>Tech</strong></span>
        </a>

        <!-- Nav categorias -->
        <nav class="nav-cats" aria-label="Categorias">
            <a href="<?= APP_URL ?>/pages/produtos.php?cat=placas-de-video">GPUs</a>
            <a href="<?= APP_URL ?>/pages/produtos.php?cat=processadores">CPUs</a>
            <a href="<?= APP_URL ?>/pages/produtos.php?cat=monitores">Monitores</a>
            <a href="<?= APP_URL ?>/pages/produtos.php?cat=teclados">Teclados</a>
            <a href="<?= APP_URL ?>/pages/produtos.php?cat=mouses">Mouses</a>
            <a href="<?= APP_URL ?>/pages/produtos.php?cat=headsets">Headsets</a>
            <a href="<?= APP_URL ?>/pages/produtos.php" class="all-link">Ver Tudo</a>
        </nav>

        <!-- Busca -->
        <div class="header-search">
            <form action="<?= APP_URL ?>/pages/produtos.php" method="GET" role="search">
                <input type="text" name="q"
                       placeholder="Buscar produtos, marcas..."
                       value="<?= isset($_GET['q']) ? sanitize($_GET['q']) : '' ?>"
                       autocomplete="off"
                       aria-label="Buscar produtos">
                <button type="submit" aria-label="Buscar">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <!-- Ações -->
        <div class="header-actions">
            <?php if (clienteLogado()): ?>
                <a href="<?= APP_URL ?>/pages/minha-conta.php" class="btn-icon" title="Minha Conta">
                    <i class="fas fa-user-circle"></i>
                    <span class="btn-icon-label"><?= sanitize(explode(' ', $_SESSION['cliente_nome'])[0]) ?></span>
                </a>
            <?php else: ?>
                <a href="<?= APP_URL ?>/pages/login.php" class="btn-icon" title="Entrar / Cadastrar">
                    <i class="fas fa-user"></i>
                    <span class="btn-icon-label">Entrar</span>
                </a>
            <?php endif; ?>

            <a href="<?= APP_URL ?>/pages/carrinho.php" class="btn-icon btn-cart" title="Carrinho">
                <i class="fas fa-shopping-cart"></i>
                <?php $cartCount = getCarrinhoCount(); if ($cartCount > 0): ?>
                    <span class="cart-badge" aria-label="<?= $cartCount ?> itens no carrinho"><?= $cartCount ?></span>
                <?php endif; ?>
            </a>
        </div>

        <!-- Mobile toggle -->
        <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu" aria-expanded="false" aria-controls="mobileNav">
            <span></span><span></span><span></span>
        </button>
    </div>

    <!-- Mobile nav -->
    <nav class="mobile-nav" id="mobileNav" aria-label="Menu mobile" hidden>
        <div class="mobile-nav-inner">
            <form action="<?= APP_URL ?>/pages/produtos.php" method="GET" class="mobile-search">
                <input type="text" name="q" placeholder="Buscar..." autocomplete="off">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <div class="mobile-nav-section">
                <span class="mobile-nav-title">Categorias</span>
                <a href="<?= APP_URL ?>/pages/produtos.php?cat=placas-de-video"><i class="fas fa-microchip"></i> Placas de Vídeo</a>
                <a href="<?= APP_URL ?>/pages/produtos.php?cat=processadores"><i class="fas fa-cpu"></i> Processadores</a>
                <a href="<?= APP_URL ?>/pages/produtos.php?cat=monitores"><i class="fas fa-desktop"></i> Monitores</a>
                <a href="<?= APP_URL ?>/pages/produtos.php?cat=teclados"><i class="fas fa-keyboard"></i> Teclados</a>
                <a href="<?= APP_URL ?>/pages/produtos.php?cat=mouses"><i class="fas fa-computer-mouse"></i> Mouses</a>
                <a href="<?= APP_URL ?>/pages/produtos.php?cat=headsets"><i class="fas fa-headphones"></i> Headsets</a>
                <a href="<?= APP_URL ?>/pages/produtos.php"><i class="fas fa-th-large"></i> Ver Todos</a>
            </div>
            <div class="mobile-nav-section">
                <span class="mobile-nav-title">Conta</span>
                <?php if (clienteLogado()): ?>
                    <a href="<?= APP_URL ?>/pages/minha-conta.php"><i class="fas fa-user"></i> Minha Conta</a>
                    <a href="<?= APP_URL ?>/pages/minha-conta.php?tab=pedidos"><i class="fas fa-box"></i> Meus Pedidos</a>
                    <a href="<?= APP_URL ?>/pages/logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
                <?php else: ?>
                    <a href="<?= APP_URL ?>/pages/login.php"><i class="fas fa-sign-in-alt"></i> Entrar</a>
                    <a href="<?= APP_URL ?>/pages/cadastro.php"><i class="fas fa-user-plus"></i> Cadastrar</a>
                <?php endif; ?>
                <a href="<?= APP_URL ?>/pages/carrinho.php"><i class="fas fa-shopping-cart"></i> Carrinho (<?= getCarrinhoCount() ?>)</a>
            </div>
        </div>
    </nav>
</header>

<main id="main-content">