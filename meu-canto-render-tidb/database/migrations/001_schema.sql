-- Meu Canto — esquema reduzido para TiDB Cloud / MySQL 8.
-- Entidades funcionais: usuarios, profissionais_saude e conteudos_site.
-- Conversas com a IA não são persistidas na base de dados.

CREATE TABLE IF NOT EXISTS usuarios (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    telefone VARCHAR(30) NULL,
    password_hash VARCHAR(255) NOT NULL,
    tipo ENUM('admin','utilizador') NOT NULL DEFAULT 'utilizador',
    estado ENUM('ativo','suspenso') NOT NULL DEFAULT 'ativo',
    ultimo_acesso DATETIME NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_usuarios_email (email),
    KEY idx_usuarios_tipo_estado (tipo, estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS profissionais_saude (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    usuario_id BIGINT UNSIGNED NULL,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    telefone VARCHAR(30) NOT NULL,
    cedula_profissional VARCHAR(80) NOT NULL,
    especialidade VARCHAR(140) NOT NULL,
    formacao TEXT NULL,
    experiencia_anos SMALLINT UNSIGNED NULL,
    biografia TEXT NOT NULL,
    foto_url VARCHAR(500) NULL,
    municipio VARCHAR(100) NULL,
    provincia VARCHAR(100) NOT NULL DEFAULT 'Luanda',
    modalidade ENUM('presencial','online','ambos') NOT NULL DEFAULT 'presencial',
    morada_consultorio VARCHAR(255) NULL,
    estado ENUM('pendente','aprovado','rejeitado','suspenso') NOT NULL DEFAULT 'pendente',
    nota_revisao TEXT NULL,
    revisor_id BIGINT UNSIGNED NULL,
    submetido_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    revisto_em DATETIME NULL,
    actualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_profissionais_cedula (cedula_profissional),
    KEY idx_profissionais_estado (estado),
    KEY idx_profissionais_especialidade (especialidade),
    KEY idx_profissionais_localizacao (provincia, municipio),
    CONSTRAINT fk_profissionais_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    CONSTRAINT fk_profissionais_revisor FOREIGN KEY (revisor_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS conteudos_site (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug VARCHAR(190) NOT NULL,
    tipo ENUM('artigo','recurso','pagina','destaque') NOT NULL DEFAULT 'artigo',
    titulo VARCHAR(190) NOT NULL,
    resumo VARCHAR(500) NULL,
    corpo MEDIUMTEXT NOT NULL,
    imagem_url VARCHAR(500) NULL,
    estado ENUM('rascunho','publicado','arquivado') NOT NULL DEFAULT 'rascunho',
    ordem INT NOT NULL DEFAULT 0,
    autor_id BIGINT UNSIGNED NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_conteudos_slug (slug),
    KEY idx_conteudos_estado_tipo (estado, tipo, ordem),
    CONSTRAINT fk_conteudos_autor FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
