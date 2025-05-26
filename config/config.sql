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

-- Inserir um usuário admin padrão (senha: admin123 - hash gerado com bcrypt)
INSERT INTO admin_users (username, password) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

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
    foto_passe LONGBLOB NOT NULL,
    documento_bi LONGBLOB NOT NULL,
    comprovativo LONGBLOB NOT NULL,
    data_inscricao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pendente', 'aprovado', 'rejeitado') DEFAULT 'pendente',
    FOREIGN KEY (curso_id) REFERENCES cursos(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Tabela: eventos
CREATE TABLE IF NOT EXISTS eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT,
    data_evento DATETIME,
    local VARCHAR(100),
    foto LONGBLOB,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
