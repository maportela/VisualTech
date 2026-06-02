-- MySQL 8.0+
CREATE DATABASE IF NOT EXISTS visualtech
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
 
USE visualtech;
 
-- 
-- CATEGORIAS
-- 
CREATE TABLE categorias (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    nome      VARCHAR(100) NOT NULL,
    slug      VARCHAR(100) NOT NULL UNIQUE,
    descricao TEXT,
    icone     VARCHAR(50)  DEFAULT 'fa-box',
    ativo     TINYINT(1)   DEFAULT 1,
    criado_em TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
 
-- 
-- PRODUTOS
-- 
CREATE TABLE produtos (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id      INT            NOT NULL,
    nome              VARCHAR(200)   NOT NULL,
    slug              VARCHAR(200)   NOT NULL UNIQUE,
    descricao         TEXT,
    especificacoes    TEXT,
    preco             DECIMAL(10,2)  NOT NULL,
    preco_promocional DECIMAL(10,2)  DEFAULT NULL,
    estoque           INT            DEFAULT 0,
    estoque_minimo    INT            DEFAULT 5,
    imagem            VARCHAR(500)   DEFAULT NULL,
    marca             VARCHAR(100)   DEFAULT NULL,
    destaque          TINYINT(1)     DEFAULT 0,
    ativo             TINYINT(1)     DEFAULT 1,
    criado_em         TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    atualizado_em     TIMESTAMP      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
 
-- 
-- CLIENTES
-- 
CREATE TABLE clientes (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    nome      VARCHAR(150) NOT NULL,
    email     VARCHAR(200) NOT NULL UNIQUE,
    senha     VARCHAR(255) NOT NULL,
    cpf       VARCHAR(14)  DEFAULT NULL,
    telefone  VARCHAR(20)  DEFAULT NULL,
    ativo     TINYINT(1)   DEFAULT 1,
    criado_em TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
 
-- 
-- ENDEREÇOS DOS CLIENTES
-- 
CREATE TABLE enderecos (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id  INT          NOT NULL,
    apelido     VARCHAR(50)  DEFAULT 'Casa',
    cep         VARCHAR(9)   NOT NULL,
    rua         VARCHAR(200) NOT NULL,
    numero      VARCHAR(10)  NOT NULL,
    complemento VARCHAR(100) DEFAULT NULL,
    bairro      VARCHAR(100) NOT NULL,
    cidade      VARCHAR(100) NOT NULL,
    estado      CHAR(2)      NOT NULL,
    principal   TINYINT(1)   DEFAULT 0,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
) ENGINE=InnoDB;
 
-- 
-- VENDEDORES / ADMINS
-- 
CREATE TABLE vendedores (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    nome      VARCHAR(150) NOT NULL,
    email     VARCHAR(200) NOT NULL UNIQUE,
    senha     VARCHAR(255) NOT NULL,
    cargo     VARCHAR(100) DEFAULT 'Vendedor',
    nivel     ENUM('admin','gerente','vendedor') DEFAULT 'vendedor',
    ativo     TINYINT(1)   DEFAULT 1,
    criado_em TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
 
-- 
-- PEDIDOS
-- 
CREATE TABLE pedidos (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id     INT                   NOT NULL,
    vendedor_id    INT                   DEFAULT NULL,
    endereco_id    INT                   NOT NULL,
    status         ENUM(
                     'pendente',
                     'confirmado',
                     'em_separacao',
                     'enviado',
                     'entregue',
                     'cancelado'
                   )                     DEFAULT 'pendente',
    subtotal       DECIMAL(10,2)         NOT NULL,
    frete          DECIMAL(10,2)         DEFAULT 0.00,
    desconto       DECIMAL(10,2)         DEFAULT 0.00,
    total          DECIMAL(10,2)         NOT NULL,
    forma_pagamento ENUM(
                     'pix',
                     'cartao_credito',
                     'cartao_debito',
                     'boleto'
                    )                    NOT NULL,
    parcelas        TINYINT              DEFAULT 1,
    observacoes     TEXT                 DEFAULT NULL,
    criado_em       TIMESTAMP            DEFAULT CURRENT_TIMESTAMP,
    atualizado_em   TIMESTAMP            DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id)  REFERENCES clientes(id)   ON DELETE RESTRICT,
    FOREIGN KEY (vendedor_id) REFERENCES vendedores(id) ON DELETE SET NULL,
    FOREIGN KEY (endereco_id) REFERENCES enderecos(id)  ON DELETE RESTRICT
) ENGINE=InnoDB;
 
-- 
-- ITENS DO PEDIDO
-- 
CREATE TABLE itens_pedido (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id      INT           NOT NULL,
    produto_id     INT           NOT NULL,
    nome_produto   VARCHAR(200)  NOT NULL,
    quantidade     INT           NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    subtotal       DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id)  REFERENCES pedidos(id)  ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
 
-- 
-- CARRINHO (PERSISTENTE)
-- 
CREATE TABLE carrinho (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id   INT          DEFAULT NULL,
    sessao_id    VARCHAR(100) DEFAULT NULL,
    produto_id   INT          NOT NULL,
    quantidade   INT          DEFAULT 1,
    adicionado_em TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id)  REFERENCES clientes(id)  ON DELETE CASCADE,
    FOREIGN KEY (produto_id)  REFERENCES produtos(id)  ON DELETE CASCADE
) ENGINE=InnoDB;
 
-- 
-- AVALIAÇÕES
-- 
CREATE TABLE avaliacoes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    produto_id  INT       NOT NULL,
    cliente_id  INT       NOT NULL,
    nota        TINYINT   NOT NULL CHECK (nota BETWEEN 1 AND 5),
    titulo      VARCHAR(100) DEFAULT NULL,
    comentario  TEXT         DEFAULT NULL,
    aprovado    TINYINT(1)   DEFAULT 0,
    criado_em   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_avaliacao (produto_id, cliente_id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)  ON DELETE CASCADE,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)  ON DELETE CASCADE
) ENGINE=InnoDB;
 
-- 
-- LOG DE ESTOQUE
-- 
CREATE TABLE log_estoque (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    produto_id   INT          NOT NULL,
    vendedor_id  INT          DEFAULT NULL,
    tipo         ENUM('entrada','saida','ajuste') NOT NULL,
    quantidade   INT          NOT NULL,
    motivo       VARCHAR(200) DEFAULT NULL,
    criado_em    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id)  REFERENCES produtos(id)   ON DELETE CASCADE,
    FOREIGN KEY (vendedor_id) REFERENCES vendedores(id) ON DELETE SET NULL
) ENGINE=InnoDB;
 
-- 
-- CATEGORIAS
-- 
INSERT INTO categorias (nome, slug, descricao, icone) VALUES
('Placas de Vídeo',  'placas-de-video',  'GPUs para gaming e trabalho profissional',          'fa-microchip'),
('Processadores',    'processadores',    'CPUs Intel e AMD para todos os perfis',              'fa-cpu'),
('Monitores',        'monitores',        'Monitores gaming, 4K e profissionais',               'fa-desktop'),
('Teclados',         'teclados',         'Teclados mecânicos, membrana e sem fio',             'fa-keyboard'),
('Mouses',           'mouses',           'Mouses gaming de alta precisão',                     'fa-computer-mouse'),
('Headsets',         'headsets',         'Headsets e fones para gaming e home office',         'fa-headphones'),
('Memória RAM',      'memoria-ram',      'Módulos DDR4 e DDR5 para desktop e notebook',       'fa-memory'),
('SSDs e HDs',       'ssds-hds',         'Armazenamento rápido SSD NVMe, SATA e HDs',         'fa-hard-drive'),
('Cadeiras Gamer',   'cadeiras-gamer',   'Cadeiras ergonômicas para longas jornadas',          'fa-chair'),
('Gabinetes',        'gabinetes',        'Cases ATX, mATX e ITX com ótimo airflow',            'fa-box-open');
 
-- 
-- PRODUTOS (descrição e etc)
-- 
INSERT INTO produtos
  (categoria_id, nome, slug, descricao, especificacoes, preco, preco_promocional, estoque, estoque_minimo, marca, destaque)
VALUES
-- GPUs
(1, 'RTX 4070 Super MSI Gaming X Trio 12GB',
    'rtx-4070-super-msi-gaming-x-trio',
    'Placa de vídeo top de linha para 1440p e 4K gaming com DLSS 3 e Ray Tracing.',
    'VRAM: 12GB GDDR6X | Clock Boost: 2550MHz | TDP: 220W | Conectores: 3x DisplayPort 1.4, 1x HDMI 2.1',
    4299.90, 3999.90, 5, 2, 'MSI', 1),
 
(1, 'RX 7800 XT Sapphire Pulse 16GB',
    'rx-7800-xt-sapphire-pulse',
    'GPU AMD com 16GB de VRAM, ideal para 1440p e com suporte a FSR 3.',
    'VRAM: 16GB GDDR6 | Clock Boost: 2430MHz | TDP: 263W | Conectores: 2x DisplayPort 2.1, 2x HDMI 2.1',
    3199.90, NULL, 7, 2, 'Sapphire', 0),
 
-- CPUs
(2, 'Intel Core i7-14700K',
    'intel-core-i7-14700k',
    '20 núcleos (8P+12E), até 5.6GHz de boost. Ideal para gaming e produtividade.',
    'Núcleos: 20 (8P+12E) | Boost: 5.6GHz | Socket: LGA1700 | TDP: 125W | Cache: 33MB L3',
    2199.90, 1899.90, 10, 3, 'Intel', 1),
 
(2, 'AMD Ryzen 7 7800X3D',
    'amd-ryzen-7-7800x3d',
    'O melhor processador para gaming do mercado com tecnologia 3D V-Cache.',
    'Núcleos: 8 | Boost: 5.0GHz | Socket: AM5 | TDP: 120W | Cache: 96MB L3 (3D V-Cache)',
    2499.90, NULL, 8, 3, 'AMD', 1),
 
-- Monitores
(3, 'LG UltraGear 27" 165Hz QHD IPS',
    'lg-ultragear-27-165hz-qhd',
    'Monitor gaming QHD com painel IPS, 165Hz, 1ms GTG e G-Sync Compatible.',
    'Resolução: 2560x1440 | Taxa: 165Hz | Tempo Resposta: 1ms | Painel: IPS | HDR: HDR400',
    1999.90, NULL, 15, 5, 'LG', 1),
 
(3, 'Samsung Odyssey G5 32" 144Hz Curvo',
    'samsung-odyssey-g5-32-144hz',
    'Monitor curvo 1000R VA com 144Hz e FreeSync Premium para uma imersão total.',
    'Resolução: 2560x1440 | Taxa: 144Hz | Curvatura: 1000R | Painel: VA | FreeSync Premium',
    1899.90, 1599.90, 8, 3, 'Samsung', 0),
 
-- Teclados
(4, 'Teclado Redragon Kumara K552 RGB Mecânico',
    'redragon-kumara-k552-rgb',
    'Teclado mecânico TKL com switches Red, RGB por tecla e anti-ghosting completo.',
    'Layout: TKL (87 teclas) | Switch: Red (Linear) | RGB: Per-Key | Cabo: USB-A trançado | Garantia: 1 ano',
    399.90, 319.90, 42, 10, 'Redragon', 1),
 
(4, 'Corsair K70 RGB MK.2 Cherry MX Red',
    'corsair-k70-rgb-mk2-cherry-mx-red',
    'Teclado mecânico full-size premium com switches Cherry MX originais e USB passthrough.',
    'Layout: Full (104 teclas) | Switch: Cherry MX Red | RGB: Per-Key | USB Passthrough | Palm rest incluso',
    1299.90, NULL, 12, 3, 'Corsair', 0),
 
-- Mouses
(5, 'Logitech G502 HERO 25K DPI',
    'logitech-g502-hero-25k',
    'Mouse gaming com sensor HERO 25K, 11 botões programáveis e peso ajustável.',
    'Sensor: HERO 25K | DPI: 100-25600 | Botões: 11 | Peso: 121g (ajustável) | RGB: LIGHTSYNC',
    499.90, 399.90, 35, 8, 'Logitech', 1),
 
(5, 'Razer DeathAdder V3 Ultra-Leve',
    'razer-deathadder-v3',
    'Mouse ergonômico ultra-leve 59g com sensor Focus Pro 30K para máxima precisão.',
    'Sensor: Focus Pro 30K | DPI: 100-30000 | Botões: 6 | Peso: 59g | Switches: Ópticos Gen-3',
    699.90, NULL, 18, 5, 'Razer', 1),
 
-- Headsets
(6, 'HyperX Cloud II 7.1 Surround',
    'hyperx-cloud-ii-71-surround',
    'Headset com som surround 7.1 virtual, drivers de 53mm e microfone removível.',
    'Drivers: 53mm | Frequência: 15Hz-25KHz | Microfone: Removível, cancelamento de ruído | Conector: USB + P2',
    599.90, 479.90, 25, 8, 'HyperX', 1),
 
(6, 'Logitech G733 Wireless RGB',
    'logitech-g733-wireless-rgb',
    'Headset sem fio leve com RGB, LIGHTSPEED e bateria de até 29 horas.',
    'Drivers: PRO-G 40mm | Wireless: LIGHTSPEED 2.4GHz | Bateria: 29h | RGB: LIGHTSYNC | Peso: 278g',
    899.90, NULL, 9, 3, 'Logitech', 0),
 
-- RAM
(7, 'Kingston Fury Beast DDR5 32GB 6000MHz RGB',
    'kingston-fury-beast-ddr5-32gb-6000mhz',
    'Kit 2x16GB DDR5-6000 com RGB e suporte a Intel XMP 3.0 e AMD EXPO.',
    'Capacidade: 32GB (2x16GB) | Velocidade: DDR5-6000 | Latência: CL40 | RGB: Sim | XMP 3.0 / EXPO',
    899.90, 749.90, 20, 5, 'Kingston', 0),
 
-- SSDs
(8, 'Samsung 990 Pro 1TB NVMe PCIe 4.0',
    'samsung-990-pro-1tb-nvme',
    'SSD M.2 NVMe de altíssima velocidade com leitura de até 7450 MB/s.',
    'Capacidade: 1TB | Interface: PCIe 4.0 NVMe | Leitura: 7450 MB/s | Escrita: 6900 MB/s | Form Factor: M.2 2280',
    699.90, 599.90, 22, 5, 'Samsung', 1),
 
-- Cadeiras
(9, 'ThunderX3 TC5 Ergonômica RGB',
    'thunderx3-tc5-ergonomica-rgb',
    'Cadeira gamer ergonômica com apoio lombar magnético, braços 4D e estrutura metálica.',
    'Capacidade: até 120kg | Braços: 4D ajustáveis | Lombar: Magnético | Inclinação: 165° | RGB: Integrado',
    1599.90, 1299.90, 6, 2, 'ThunderX3', 1),
 
-- Gabinetes
(10, 'NZXT H7 Flow Mid-Tower ATX',
    'nzxt-h7-flow-mid-tower-atx',
    'Gabinete com excelente airflow, painel de vidro temperado e 4 fans RGB inclusos.',
    'Form Factor: Mid-Tower ATX/mATX/ITX | Painel: Vidro Temperado | Fans: 4x120mm inclusos | USB-C Frontal',
    1099.90, NULL, 11, 3, 'NZXT', 0);
 
-- 
-- ADMIN PADRÃO
-- Senha: Admin@123  (bcrypt)
-- 
INSERT INTO vendedores (nome, email, senha, cargo, nivel) VALUES
('Administrador', 'admin@visualtech.com',
 '$2y$12$K8GxL3kqPZ7Rt5nDvXj2OuT9mA1cBW6eHfNpQsYoVIZuJdlXRyMKC',
 'Administrador', 'admin');
 
-- 
-- VIEWS ÚTEIS
-- 
CREATE OR REPLACE VIEW vw_produtos_completo AS
SELECT
    p.*,
    c.nome  AS categoria_nome,
    c.slug  AS categoria_slug,
    COALESCE(AVG(a.nota), 0) AS media_avaliacoes,
    COUNT(a.id)              AS total_avaliacoes
FROM produtos p
JOIN categorias c ON c.id = p.categoria_id
LEFT JOIN avaliacoes a ON a.produto_id = p.id AND a.aprovado = 1
GROUP BY p.id;
 
CREATE OR REPLACE VIEW vw_pedidos_completo AS
SELECT
    p.*,
    c.nome  AS cliente_nome,
    c.email AS cliente_email,
    v.nome  AS vendedor_nome,
    e.cidade, e.estado
FROM pedidos p
JOIN clientes   c ON c.id = p.cliente_id
LEFT JOIN vendedores v ON v.id = p.vendedor_id
JOIN enderecos  e ON e.id = p.endereco_id;
 
CREATE OR REPLACE VIEW vw_estoque_baixo AS
SELECT id, nome, marca, estoque, estoque_minimo
FROM produtos
WHERE estoque <= estoque_minimo AND ativo = 1
ORDER BY estoque ASC;