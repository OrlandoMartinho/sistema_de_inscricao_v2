-- Criar o banco de dados (ajuste o nome se desejar)
CREATE DATABASE IF NOT EXISTS sistema_de_inscricao_v2;
USE sistema_de_inscricao_v2;

-- Tabela: admin_users
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



-- Tabela: notificacoes
CREATE TABLE IF NOT EXISTS notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data_notificacao DATETIME NOT NULL,
    descricao VARCHAR(255),
    admin_id INT,
    FOREIGN KEY (admin_id) REFERENCES admin_users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- Tabela: cursos
CREATE TABLE IF NOT EXISTS cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
    duracao VARCHAR(50) NOT NULL,
    nivel VARCHAR(20) NOT NULL,
    instrutor VARCHAR(100) NOT NULL,
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela: inscricoes
CREATE TABLE IF NOT EXISTS inscricoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    bi_numero VARCHAR(50) NOT NULL,
    sexo ENUM('Masculino', 'Feminino') NOT NULL,
    curso_id INT NOT NULL,
    foto_passe LONGBLOB ,
    documento_bi LONGBLOB ,
    comprovativo LONGBLOB ,
    data_inscricao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pendente', 'aprovado', 'rejeitado') DEFAULT 'pendente',
    FOREIGN KEY (curso_id) REFERENCES cursos(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Tabela: eventos
CREATE TABLE  IF NOT EXISTS eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    data_evento DATETIME NOT NULL,
    local VARCHAR(255) NOT NULL,
    descricao TEXT,
    imagem VARCHAR(255), -- caminho da imagem ou nome do arquivo salvo
    status ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- Tabela: contactos
CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    assunto VARCHAR(200) NOT NULL,
    mensagem TEXT NOT NULL,
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('não lido', 'lido', 'respondido') DEFAULT 'não lido'
);

CREATE TABLE IF NOT EXISTS atividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    acao VARCHAR(255) NOT NULL,
    usuario VARCHAR(100) NOT NULL,
    data_acao DATETIME NOT NULL
);


ALTER TABLE `inscricoes` ADD `observacoes` VARCHAR(255) NOT NULL AFTER `status`;

ALTER TABLE `inscricoes` ADD `curso` VARCHAR(255) NOT NULL AFTER `observacoes`;