<?php // VisualTech — includes/footer.php ?>
</main>
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="<?= APP_URL ?>" class="logo footer-logo">
                    <span class="logo-vt">VT</span>
                    <span class="logo-text">Visual<strong>Tech</strong></span>
                </a>
                <p>A melhor loja de periféricos, eletrônicos e produtos gamer.</p>
                <div class="footer-social">
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-x-twitter"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                    <a href="#"><i class="fab fa-discord"></i></a>
                </div>
            </div>
            <div class="footer-links">
                <h4>Categorias</h4>
                <a href="<?= APP_URL ?>/pages/produtos.php?cat=placas-de-video">Placas de Vídeo</a>
                <a href="<?= APP_URL ?>/pages/produtos.php?cat=processadores">Processadores</a>
                <a href="<?= APP_URL ?>/pages/produtos.php?cat=monitores">Monitores</a>
                <a href="<?= APP_URL ?>/pages/produtos.php?cat=teclados">Teclados</a>
                <a href="<?= APP_URL ?>/pages/produtos.php?cat=mouses">Mouses</a>
                <a href="<?= APP_URL ?>/pages/produtos.php?cat=headsets">Headsets</a>
            </div>
            <div class="footer-links">
                <h4>Minha Conta</h4>
                <a href="<?= APP_URL ?>/pages/login.php">Entrar</a>
                <a href="<?= APP_URL ?>/pages/cadastro.php">Criar Conta</a>
                <a href="<?= APP_URL ?>/pages/minha-conta.php">Meus Pedidos</a>
                <a href="<?= APP_URL ?>/pages/carrinho.php">Carrinho</a>
            </div>
            <div class="footer-links">
                <h4>Atendimento</h4>
                <a href="#">Sobre Nós</a>
                <a href="#">Política de Trocas</a>
                <a href="#">Fale Conosco</a>
                <div class="footer-payment">
                    <i class="fab fa-cc-visa"></i>
                    <i class="fab fa-cc-mastercard"></i>
                    <i class="fas fa-barcode"></i>
                    <i class="fab fa-pix"></i>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© <?= date('Y') ?> VisualTech. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>
<script src="<?= APP_URL ?>/js/main.js"></script>
</body>
</html>
