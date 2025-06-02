CREATE DATABASE IF NOT EXISTS `inscricaosistema`;
USE `inscricaosistema`;

CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin_users` (`id`, `username`, `password`, `created_at`) VALUES
(2, 'admin', '$2y$10$rnaXLEKVQkGO8qS.FzL2I.HfimOZvpbHqOH8QWKJGzYuXqWYQL7Bi', '2025-05-27 04:57:22');

CREATE TABLE IF NOT EXISTS `atividades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acao` varchar(255) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `data_acao` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `contactos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `assunto` varchar(200) NOT NULL,
  `mensagem` text NOT NULL,
  `data_envio` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('não lido','lido','respondido') DEFAULT 'não lido',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `cursos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text NOT NULL,
  `duracao` varchar(50) NOT NULL,
  `nivel` varchar(20) NOT NULL,
  `instrutor` varchar(100) NOT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `eventos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `data_evento` datetime DEFAULT NULL,
  `local` varchar(100) DEFAULT NULL,
  `foto` longblob DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(10) NOT NULL DEFAULT 'ativo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `inscricoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_completo` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `bi_numero` varchar(50) NOT NULL,
  `sexo` enum('Masculino','Feminino') NOT NULL,
  `curso_id` int(11) DEFAULT NULL,
  `foto_passe` longblob DEFAULT NULL,
  `documento_bi` longblob DEFAULT NULL,
  `comprovativo` longblob DEFAULT NULL,
  `data_inscricao` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pendente','aprovado','rejeitado') DEFAULT 'pendente',
  `observacoes` varchar(255) NOT NULL,
  `data_de_nascimento` DATE DEFAULT NULL,
  `curso` varchar(255) NOT NULL,
  `rup` varchar(100) DEFAULT NULL,
  `foto_passe_type` VARCHAR(50),
  `documento_bi_type` VARCHAR(50),
  `comprovativo_type` VARCHAR(50),
  PRIMARY KEY (`id`),
  KEY `curso_id` (`curso_id`),
  CONSTRAINT `inscricoes_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `notificacoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_notificacao` datetime NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `notificacoes_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
