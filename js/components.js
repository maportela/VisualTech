/* =====================================================
   VisualTech — Componentes compartilhados (header e footer)
   Injeta o header e footer em todas as páginas do site.
   ===================================================== */

/* Detecta se estamos dentro de uma subpasta (pages/, admin/) */
const _base = (() => {
  const p = location.pathname;
  if (p.includes("/admin/")) return "../../";
  if (p.includes("/pages/")) return "../";
  return "./";
})();

/* ── HTML do Header ── */
const _headerHTML = `
<header class="header" id="header">
  <div class="container">
    <a href="${_base}index.html" class="logo" aria-label="VisualTech - Página Inicial">
      <span class="logo-vt" aria-hidden="true">VT</span>
      <span class="logo-text">Visual<strong>Tech</strong></span>
    </a>

    <nav class="nav-cats" aria-label="Categorias">
      <a href="${_base}pages/produtos.html?cat=placas-de-video">GPUs</a>
      <a href="${_base}pages/produtos.html?cat=processadores">CPUs</a>
      <a href="${_base}pages/produtos.html?cat=monitores">Monitores</a>
      <a href="${_base}pages/produtos.html?cat=teclados">Teclados</a>
      <a href="${_base}pages/produtos.html?cat=mouses">Mouses</a>
      <a href="${_base}pages/produtos.html?cat=headsets">Headsets</a>
      <a href="${_base}pages/produtos.html" class="all-link">Ver Tudo</a>
    </nav>

    <div class="header-search">
      <form action="${_base}pages/produtos.html" role="search">
        <input type="text" name="q" placeholder="Buscar produtos, marcas..." autocomplete="off" aria-label="Buscar produtos">
        <button type="submit" aria-label="Buscar"><i class="fas fa-search"></i></button>
      </form>
    </div>

    <div class="header-actions">
      <a href="${_base}pages/login.html" class="btn-icon" title="Entrar / Cadastrar">
        <i class="fas fa-user"></i>
        <span class="btn-icon-label">Entrar</span>
      </a>
      <a href="${_base}pages/carrinho.html" class="btn-icon" title="Carrinho">
        <i class="fas fa-shopping-cart"></i>
        <span class="btn-icon-label">Carrinho</span>
      </a>
    </div>

    <button class="menu-toggle" id="menuToggle" aria-label="Abrir menu" aria-expanded="false" aria-controls="mobileNav">
      <span></span><span></span><span></span>
    </button>
  </div>

  <nav class="mobile-nav" id="mobileNav" aria-label="Menu mobile" hidden>
    <div class="mobile-nav-inner">
      <form action="${_base}pages/produtos.html" class="mobile-search">
        <input type="text" name="q" placeholder="Buscar..." autocomplete="off">
        <button type="submit"><i class="fas fa-search"></i></button>
      </form>
      <div class="mobile-nav-section">
        <span class="mobile-nav-title">Categorias</span>
        <a href="${_base}pages/produtos.html?cat=placas-de-video"><i class="fas fa-microchip"></i> Placas de Vídeo</a>
        <a href="${_base}pages/produtos.html?cat=processadores"><i class="fas fa-cpu"></i> Processadores</a>
        <a href="${_base}pages/produtos.html?cat=monitores"><i class="fas fa-desktop"></i> Monitores</a>
        <a href="${_base}pages/produtos.html?cat=teclados"><i class="fas fa-keyboard"></i> Teclados</a>
        <a href="${_base}pages/produtos.html?cat=mouses"><i class="fas fa-computer-mouse"></i> Mouses</a>
        <a href="${_base}pages/produtos.html?cat=headsets"><i class="fas fa-headphones"></i> Headsets</a>
        <a href="${_base}pages/produtos.html"><i class="fas fa-th-large"></i> Ver Todos</a>
      </div>
      <div class="mobile-nav-section">
        <span class="mobile-nav-title">Conta</span>
        <a href="${_base}pages/login.html"><i class="fas fa-sign-in-alt"></i> Entrar</a>
        <a href="${_base}pages/cadastro.html"><i class="fas fa-user-plus"></i> Cadastrar</a>
        <a href="${_base}pages/carrinho.html"><i class="fas fa-shopping-cart"></i> Carrinho</a>
      </div>
    </div>
  </nav>
</header>
`;

/* ── HTML do Footer ── */
const _footerHTML = `
<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="${_base}index.html" class="logo footer-logo">
          <span class="logo-vt">VT</span>
          <span class="logo-text">Visual<strong>Tech</strong></span>
        </a>
        <p>A melhor loja de periféricos, eletrônicos e produtos gamer.</p>
        <div class="footer-social">
          <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
          <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
          <a href="#" aria-label="Discord"><i class="fab fa-discord"></i></a>
        </div>
      </div>
      <div class="footer-links">
        <h4>Categorias</h4>
        <a href="${_base}pages/produtos.html?cat=placas-de-video">Placas de Vídeo</a>
        <a href="${_base}pages/produtos.html?cat=processadores">Processadores</a>
        <a href="${_base}pages/produtos.html?cat=monitores">Monitores</a>
        <a href="${_base}pages/produtos.html?cat=teclados">Teclados</a>
        <a href="${_base}pages/produtos.html?cat=mouses">Mouses</a>
        <a href="${_base}pages/produtos.html?cat=headsets">Headsets</a>
      </div>
      <div class="footer-links">
        <h4>Minha Conta</h4>
        <a href="${_base}pages/login.html">Entrar</a>
        <a href="${_base}pages/cadastro.html">Criar Conta</a>
        <a href="${_base}pages/minha-conta.html">Meus Pedidos</a>
        <a href="${_base}pages/carrinho.html">Carrinho</a>
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
      <p>© 2025 VisualTech. Todos os direitos reservados.</p>
    </div>
  </div>
</footer>
`;

/* ── Injeta header e footer no DOM ── */
document.addEventListener("DOMContentLoaded", () => {
  /* Insere o header antes do <main> */
  const main = document.querySelector("main");
  if (main) main.insertAdjacentHTML("beforebegin", _headerHTML);

  /* Insere o footer depois do <main> */
  if (main) main.insertAdjacentHTML("afterend", _footerHTML);

  /* Ativa o toggle do menu mobile */
  const toggle = document.getElementById("menuToggle");
  const mobileNav = document.getElementById("mobileNav");
  if (toggle && mobileNav) {
    toggle.addEventListener("click", () => {
      const aberto = !mobileNav.hidden;
      mobileNav.hidden = aberto;
      toggle.setAttribute("aria-expanded", String(!aberto));
      toggle.classList.toggle("open", !aberto);
    });
  }

  /* Marca o link ativo da nav conforme a URL atual */
  document.querySelectorAll(".nav-cats a").forEach((link) => {
    if (link.href === location.href) link.classList.add("active");
  });
});
