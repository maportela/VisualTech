# VisualTech — E-commerce de Periféricos & Eletrônicos Gamer

> Projeto acadêmico de e-commerce desenvolvido em PHP + MySQL com design moderno e responsivo.

![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-CSS3-E34F26?style=flat&logo=html5&logoColor=white)

---

## Funcionalidades

- ✅ Cadastro e login de clientes com senha criptografada (bcrypt)
- ✅ Painel administrativo para vendedores (gerenciar produtos, pedidos e usuários)
- ✅ Catálogo de produtos com filtros, busca e paginação
- ✅ Carrinho de compras persistente (por sessão ou por conta)
- ✅ Fluxo completo de pedidos com múltiplas formas de pagamento
- ✅ Gerenciamento de estoque com alertas de estoque baixo
- ✅ Avaliações de produtos por clientes
- ✅ Dashboard com métricas em tempo real
- ✅ Design responsivo (mobile-first)
- ✅ Banco de dados normalizado com views e integridade referencial

---

## Estrutura de Pastas

```
visualtech/
├── index.php               # Página inicial (home)
├── database.sql            # Script SQL completo
├── .env                    # Variáveis de ambiente (NÃO versionar)
├── .env.example            # Modelo das variáveis de ambiente
├── .gitignore
├── README.md
│
├── includes/               # Núcleo do sistema
│   ├── config.php          # Conexão PDO + carrega .env
│   ├── functions.php       # Funções de auth, carrinho, helpers
│   ├── header.php          # Cabeçalho HTML
│   └── footer.php          # Rodapé HTML
│
├── pages/                  # Páginas do cliente
│   ├── produtos.php        # Catálogo com filtros
│   ├── produto.php         # Detalhe do produto
│   ├── carrinho.php        # Carrinho de compras
│   ├── checkout.php        # Finalizar compra
│   ├── pedido-confirmado.php
│   ├── login.php
│   ├── cadastro.php
│   ├── logout.php
│   └── minha-conta.php
│
├── admin/                  # Painel administrativo
│   ├── index.php           # Login do admin
│   ├── dashboard.php       # Dashboard com métricas
│   ├── includes/
│   │   ├── auth.php        # Proteção de páginas admin
│   │   ├── header.php      # Header do admin
│   │   └── footer.php      # Footer do admin
│   └── pages/
│       ├── produtos.php    # Listar produtos
│       ├── produto-form.php # Criar/Editar produto
│       ├── pedidos.php     # Listar pedidos
│       ├── pedido-detalhe.php
│       ├── clientes.php    # Listar clientes
│       ├── vendedores.php  # Listar vendedores
│       └── estoque.php     # Controle de estoque
│
├── api/                    # Endpoints AJAX
│   ├── carrinho.php        # Adicionar/remover do carrinho
│   └── cep.php             # Consulta CEP (ViaCEP)
│
├── css/
│   └── style.css           # Estilos globais
│
└── js/
    └── main.js             # Scripts globais
```

---

## Pré-requisitos

| Ferramenta      | Versão mínima    |
| --------------- | ---------------- |
| PHP             | 8.1+             |
| MySQL / MariaDB | 8.0+ / 10.6+     |
| Apache / Nginx  | Qualquer recente |

> **Recomendado:** XAMPP (Windows/Linux/Mac) ou Laragon (Windows)

---

## Instalação e Configuração Local

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/visualtech.git
cd visualtech
```

### 2. Mova para a pasta do servidor

**XAMPP (Windows):**

```
C:\xampp\htdocs\visualtech\
```

**XAMPP (Linux/Mac):**

```bash
sudo mv visualtech /opt/lampp/htdocs/
```

**Laragon:**

```
C:\laragon\www\visualtech\
```

### 3. Configure as variáveis de ambiente

```bash
cp .env.example .env
```

Edite o arquivo `.env` com suas credenciais:

```env
DB_HOST=localhost
DB_USER=root
DB_PASS=          # deixe em branco se não tem senha (XAMPP padrão)
DB_NAME=visualtech
```

### 4. Crie o banco de dados

**Via phpMyAdmin:**

1. Acesse `http://localhost/phpmyadmin`
2. Clique em "Novo" → crie o banco `visualtech`
3. Selecione o banco → aba "SQL" → cole e execute o conteúdo de `database.sql`

**Via terminal:**

```bash
mysql -u root -p < database.sql
```

### 5. Acesse o projeto

| URL                                 | Descrição       |
| ----------------------------------- | --------------- |
| `http://localhost/visualtech`       | Loja (frontend) |
| `http://localhost/visualtech/admin` | Painel Admin    |

---

## Credenciais Padrão

### Admin

| Campo  | Valor                  |
| ------ | ---------------------- |
| E-mail | `admin@visualtech.com` |
| Senha  | `Admin@123`            |

> ⚠️ Troque a senha após o primeiro acesso!

---

## Tecnologias Utilizadas

| Tecnologia           | Uso                                      |
| -------------------- | ---------------------------------------- |
| PHP 8.1              | Backend / lógica de negócio              |
| MySQL 8              | Banco de dados relacional                |
| PDO                  | Abstração de banco (prepared statements) |
| HTML5 / CSS3         | Estrutura e estilo                       |
| JavaScript (Vanilla) | Interatividade frontend                  |
| Font Awesome 6       | Ícones                                   |
| Google Fonts         | Tipografia (Rajdhani + Inter)            |

---

## Segurança Implementada

- Senhas criptografadas com `password_hash()` (bcrypt, custo 12)
- Prepared statements (PDO) em todas as queries — proteção contra SQL Injection
- Sanitização de entrada com `htmlspecialchars()` e `strip_tags()`
- Verificação de sessão em todas as páginas protegidas
- Separação de autenticação cliente/admin

---

## Deploy na AWS (guia básico)

1. **EC2** — crie uma instância Ubuntu 22.04, instale Apache + PHP + MySQL
2. **RDS** — use o Amazon RDS para o banco MySQL (altere as variáveis do `.env`)
3. **S3** — para armazenar imagens de produtos (opcional)
4. **Route 53** — para domínio personalizado (opcional)

---

## 👥 Autores

Projeto desenvolvido para a disciplina de **Banco de Dados / Desenvolvimento Web**

---

## 📄 Licença

Projeto acadêmico, uso livre para fins educacionais.
