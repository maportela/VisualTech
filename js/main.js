/* VisualTech — main.js */
"use strict";
// Mobile menu
const toggle = document.getElementById("menuToggle");
const mobileNav = document.getElementById("mobileNav");
if (toggle && mobileNav) {
  toggle.addEventListener("click", () => {
    const open = !mobileNav.hidden;
    mobileNav.hidden = open;
    toggle.classList.toggle("open", !open);
    toggle.setAttribute("aria-expanded", String(!open));
  });
}
// Flash auto-dismiss
const flash = document.getElementById("flashMsg");
if (flash) setTimeout(() => flash.remove(), 4500);
// Sticky header shadow
const header = document.getElementById("header");
if (header)
  window.addEventListener(
    "scroll",
    () => {
      header.style.boxShadow =
        window.scrollY > 10 ? "0 2px 20px rgba(0,0,0,.6)" : "none";
    },
    { passive: true },
  );
// Quantity controls
document.querySelectorAll(".qty-control").forEach((ctrl) => {
  const input = ctrl.querySelector(".qty-input");
  ctrl.querySelector(".qty-minus")?.addEventListener("click", () => {
    if (+input.value > 1) {
      input.value = +input.value - 1;
      input.dispatchEvent(new Event("change"));
    }
  });
  ctrl.querySelector(".qty-plus")?.addEventListener("click", () => {
    const max = +input.max || 99;
    if (+input.value < max) {
      input.value = +input.value + 1;
      input.dispatchEvent(new Event("change"));
    }
  });
});
// Add to cart AJAX
document.addEventListener("click", (e) => {
  const btn = e.target.closest("[data-add-cart]");
  if (!btn) return;
  e.preventDefault();
  const id = btn.dataset.addCart;
  const qty = document.getElementById("qty")?.value || 1;
  btn.disabled = true;
  const orig = btn.innerHTML;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adicionando...';
  fetch(window._vtUrl + "/api/carrinho.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "acao=adicionar&produto_id=" + id + "&quantidade=" + qty,
  })
    .then((r) => r.json())
    .then((data) => {
      if (data.sucesso) {
        btn.innerHTML = '<i class="fas fa-check"></i> Adicionado!';
        btn.style.background = "var(--green)";
        document.querySelectorAll(".cart-badge").forEach((b) => {
          b.textContent = data.quantidade_total;
        });
        setTimeout(() => {
          btn.innerHTML = orig;
          btn.style.background = "";
          btn.disabled = false;
        }, 2000);
      } else {
        btn.innerHTML = orig;
        btn.disabled = false;
        alert(data.erro || "Erro.");
      }
    })
    .catch(() => {
      btn.innerHTML = orig;
      btn.disabled = false;
    });
});
// Remove from cart
document.addEventListener("click", (e) => {
  const btn = e.target.closest("[data-remove-cart]");
  if (!btn) return;
  e.preventDefault();
  if (!confirm("Remover este item?")) return;
  fetch(window._vtUrl + "/api/carrinho.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "acao=remover&item_id=" + btn.dataset.removeCart,
  })
    .then((r) => r.json())
    .then((d) => {
      if (d.sucesso) location.reload();
    });
});
// Update cart qty
document.addEventListener("change", (e) => {
  const input = e.target.closest("[data-cart-qty]");
  if (!input) return;
  fetch(window._vtUrl + "/api/carrinho.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body:
      "acao=atualizar&item_id=" +
      input.dataset.cartQty +
      "&quantidade=" +
      input.value,
  })
    .then((r) => r.json())
    .then((d) => {
      if (d.sucesso) location.reload();
    });
});
// CEP lookup
const cepInput = document.getElementById("cep");
if (cepInput) {
  cepInput.addEventListener("blur", () => {
    const cep = cepInput.value.replace(/\D/g, "");
    if (cep.length !== 8) return;
    fetch(window._vtUrl + "/api/cep.php?cep=" + cep)
      .then((r) => r.json())
      .then((d) => {
        if (!d.erro) {
          ["rua", "bairro", "cidade", "estado"].forEach((id) => {
            const el = document.getElementById(id);
            const val = {
              rua: d.logradouro,
              bairro: d.bairro,
              cidade: d.localidade,
              estado: d.uf,
            }[id];
            if (el && val) el.value = val;
          });
          document.getElementById("numero")?.focus();
        }
      })
      .catch(() => {});
  });
  cepInput.addEventListener("input", () => {
    let v = cepInput.value.replace(/\D/g, "").slice(0, 8);
    if (v.length > 5) v = v.slice(0, 5) + "-" + v.slice(5);
    cepInput.value = v;
  });
}
// CPF mask
document.getElementById("cpf")?.addEventListener("input", (e) => {
  let v = e.target.value.replace(/\D/g, "").slice(0, 11);
  if (v.length > 9)
    v =
      v.slice(0, 3) +
      "." +
      v.slice(3, 6) +
      "." +
      v.slice(6, 9) +
      "-" +
      v.slice(9);
  else if (v.length > 6)
    v = v.slice(0, 3) + "." + v.slice(3, 6) + "." + v.slice(6);
  else if (v.length > 3) v = v.slice(0, 3) + "." + v.slice(3);
  e.target.value = v;
});
// Phone mask
document.getElementById("telefone")?.addEventListener("input", (e) => {
  let v = e.target.value.replace(/\D/g, "").slice(0, 11);
  if (v.length > 6)
    v = "(" + v.slice(0, 2) + ") " + v.slice(2, 7) + "-" + v.slice(7);
  else if (v.length > 2) v = "(" + v.slice(0, 2) + ") " + v.slice(2);
  e.target.value = v;
});
