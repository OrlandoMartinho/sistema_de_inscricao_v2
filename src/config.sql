-- Criar o banco de dados (ajuste o nome se desejar)
CREATE DATABASE IF NOT EXISTS sistema_de_inscricao_v2;
USE sistema_de_inscricao_v2;

-- Tabela: Usuarios
CREATE TABLE IF NOT EXISTS Usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255),
    senha TEXT,
    token_de_acesso TEXT,
    email VARCHAR(255),
    data_de_criacao DATETIME
);

-- Tabela: Notificacoes
CREATE TABLE IF NOT EXISTS Notificacoes (
    id_notificacao INT PRIMARY KEY AUTO_INCREMENT,
    data_da_notificacao VARCHAR(100),
    descricao VARCHAR(255),
    id_usuario INT,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario)
);

-- Tabela: Cursos
CREATE TABLE IF NOT EXISTS Cursos (
    id_curso INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255),
    descricao VARCHAR(255),
    duracao INT,
    data_de_criacao DATETIME
);

-- Tabela: Incricoes
CREATE TABLE IF NOT EXISTS Incricoes (
    id_incricao INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255),
    sexo VARCHAR(255),
    tipo_de_identificacao VARCHAR(255),
    data_de_validade DATE,
    arquivo_de_identificacao LONGBLOB,
    data_de_criacao DATETIME,
    aprovacao INT,
    id_curso INT,
    data_da_incricao DATETIME,
    FOREIGN KEY (id_curso) REFERENCES Cursos(id_curso)
);

-- Tabela: Eventos
CREATE TABLE IF NOT EXISTS Eventos (
    id_evento INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(255),
    data_do_evento DATE,
    descricao VARCHAR(255),
    data_de_criacao DATETIME,
    foto LONGBLOB
);

-- Tabela: Contactos
CREATE TABLE IF NOT EXISTS Contactos (
    id_contacto INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255),
    email VARCHAR(255),
    assunto VARCHAR(255),
    mensagem VARCHAR(255),
    respondido INT,
    data_de_resposta DATETIME,
    data_de_criacao DATETIME
);


