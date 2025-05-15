# Documentação do Banco de Dados - Sistema de Gestão Educacional

## Visão Geral do Sistema

Este banco de dados foi projetado para gerenciar um sistema completo de gestão educacional, incluindo:

- Cadastro de usuários e controle de acesso
- Gestão de cursos e calendários acadêmicos
- Processo de inscrições de alunos
- Sistema de notificações
- Galeria de eventos
- Gerenciamento de contatos

## Diagrama Entidade-Relacionamento (DER)

O sistema consiste em 7 entidades principais inter-relacionadas:


## Estrutura Detalhada das Tabelas

### 1. Tabela `Usuarios` (Controle de Acesso)

| Campo | Tipo | Descrição | Restrições |
|-------|------|-----------|------------|
| id_usuario | INT | Identificador único | PRIMARY KEY, AUTO_INCREMENT |
| nome | VARCHAR(255) | Nome completo do usuário | NOT NULL |
| senha | TEXT | Senha criptografada | NOT NULL |
| token_de_acesso | TEXT | Token para autenticação JWT | NULLABLE |
| email | VARCHAR(255) | E-mail do usuário | UNIQUE, NOT NULL |
| data_de_criacao | DATETIME | Data de registro | DEFAULT CURRENT_TIMESTAMP |

**Relacionamentos:**
- Tem muitas `Notificacoes`

### 2. Tabela `Notificacoes` (Sistema de Alertas)

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id_notificacao | INT | Chave primária |
| data_da_notificacao | VARCHAR(100) | Data/hora formatada |
| descricao | VARCHAR(255) | Conteúdo da mensagem |
| id_usuario | INT | Usuário destinatário |

**Relacionamentos:**
- Pertence a um `Usuario`

### 3. Tabela `Cursos` (Catálogo de Cursos)

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id_curso | INT | Identificador único |
| nome | VARCHAR(255) | Nome do curso |
| descricao | VARCHAR(255) | Ementa/resumo |
| area | VARCHAR(255) | Área de conhecimento |
| duracao | INT | Carga horária (horas) |
| numeros_de_vagas | INT | Limite de alunos |
| data_de_criacao | DATETIME | Data de cadastro |

**Relacionamentos:**
- Tem muitos `Calendarios`

### 4. Tabela `Calendarios` (Eventos Acadêmicos)

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id_calendario | INT | Chave primária |
| título_do_anuncio | VARCHAR(255) | Nome do evento |
| data_de_termino | DATE | Data final |
| descricao | VARCHAR(255) | Detalhes do evento |
| id_curso | INT | Curso relacionado |
| data_de_criacao | DATETIME | Data de registro |

**Relacionamentos:**
- Pertence a um `Curso`
- Tem muitas `Inscricoes`

### 5. Tabela `Inscricoes` (Matrículas)

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id_incricao | INT | Chave primária |
| idade | INT | Idade do candidato |
| genero | VARCHAR(255) | Gênero |
| numero_de_processo | INT | Número único |
| nome_completo | VARCHAR(255) | Nome do aluno |
| contacto_do_aluno | VARCHAR(20) | Telefone |
| contacto_do_encarregado | VARCHAR(255) | Telefone responsável |
| id_calendario | INT | Evento relacionado |
| data_de_nascimento | DATE | Data nascimento |
| natural_de | VARCHAR(255) | Naturalidade |
| provincia | VARCHAR(255) | Província |
| tipo_de_identificacao | VARCHAR(255) | Tipo documento |
| numero_de_identificacao | VARCHAR(255) | Nº documento |
| data_de_validade | DATE | Validade doc |
| arquivo_de_identificacao | LONGBLOB | Documento digitalizado |
| foto_tipo_passe | LONGBLOB | Foto 3x4 |
| classe | VARCHAR(255) | Turma/classe |
| turno | VARCHAR(255) | Período |
| data_de_criacao | DATETIME | Data registro |

### 6. Tabela `Contactos` (Mensagens)

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id_contacto | INT | Chave primária |
| nome | VARCHAR(255) | Remetente |
| email | VARCHAR(255) | E-mail |
| assunto | VARCHAR(255) | Assunto |
| mensagem | VARCHAR(255) | Conteúdo |
| respondido | INT | Status (0/1) |
| data_de_resposta | DATETIME | Data resposta |
| data_de_criacao | DATETIME | Data recebimento |

### 7. Tabela `galeria` (Eventos)

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id_galeria | INT | Chave primária |
| título | VARCHAR(255) | Título evento |
| data_do_evento | DATE | Data ocorrência |
| descricao | VARCHAR(255) | Detalhes |
| data_de_criacao | DATETIME | Data registro |

## Fluxos Principais

1. **Cadastro de Aluno**:
   Usuario → Curso → Calendario → Inscricao

2. **Notificação**:
   Evento (Calendario) → Notificacao → Usuario

3. **Galeria**:
   Evento (Calendario) → Registro (galeria)