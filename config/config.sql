-- Criar o banco de dados (ajuste o nome se desejar)
CREATE DATABASE IF NOT EXISTS sistema_de_inscricao_v2;
USE sistema_de_inscricao_v2;

CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserir um usuário admin padrão (senha: admin123)
INSERT INTO admin_users (username, password) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Tabela: Notificacoes
CREATE TABLE IF NOT EXISTS Notificacoes (
    id_notificacao INT PRIMARY KEY AUTO_INCREMENT,
    data_da_notificacao VARCHAR(100),
    descricao VARCHAR(255),
    id_usuario INT,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
);



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



CREATE TABLE IF NOT EXISTS inscricoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    bi_numero VARCHAR(50) NOT NULL,
    sexo ENUM('Masculino', 'Feminino') NOT NULL,
    curso VARCHAR(50) NOT NULL,
    foto_passe LONGBLOB NOT NULL,
    documento_bi LONGBLOB NOT NULL,
    comprovativo LONGBLOB NOT NULL,
    data_inscricao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pendente', 'aprovado', 'rejeitado') DEFAULT 'pendente'
);

--Tabela: Eventos

CREATE TABLE IF NOT EXISTS eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT,
    data_evento DATETIME,
    local VARCHAR(100),
    foto LONGBLOB,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



CREATE TABLE IF NOT EXISTS Contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    assunto VARCHAR(200) NOT NULL,
    mensagem TEXT NOT NULL,
    data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('não lido', 'lido', 'respondido') DEFAULT 'não lido'
);

