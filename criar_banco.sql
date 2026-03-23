CREATE DATABASE IF NOT EXISTS delivery
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE delivery;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    data_nascimento DATE,
    sexo ENUM('M','F','O') NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    preco_venda DECIMAL(10,2) NOT NULL,
    detalhes TEXT,
    desconto DECIMAL(5,2) DEFAULT 0.00,
    quantidade INT NOT NULL DEFAULT 0,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    status VARCHAR(50) NOT NULL DEFAULT 'sucesso',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
) ENGINE=InnoDB;

CREATE INDEX idx_produtos_nome ON produtos (nome);
CREATE INDEX idx_pedidos_usuario ON pedidos (usuario_id);

ALTER TABLE pedidos MODIFY status VARCHAR(50) DEFAULT 'pendente';
