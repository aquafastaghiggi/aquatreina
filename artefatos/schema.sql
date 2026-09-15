-- =====================================================================
-- Aquafast Treina - schema de dominio (MySQL 8)
--
-- Este arquivo e a REFERENCIA. As migrations do Laravel devem produzir
-- exatamente estas tabelas, colunas, tipos, indices e chaves.
--
-- NAO estao aqui as tabelas do framework e dos pacotes, criadas pelas
-- migrations proprias deles e mantidas com os nomes originais em ingles:
--   sessions, cache, cache_locks, jobs, job_batches, failed_jobs,
--   password_reset_tokens, notifications, activity_log, migrations,
--   roles, permissions, model_has_roles, model_has_permissions,
--   role_has_permissions
--
-- Charset: utf8mb4 / utf8mb4_unicode_ci em tudo.
-- Ordem de criacao respeita as chaves estrangeiras.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- 1. organizacoes
-- Empresa do aluno. Existe desde a v1 mesmo sem tela dedicada.
-- ---------------------------------------------------------------------
CREATE TABLE organizacoes (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome            VARCHAR(160) NOT NULL,
  cnpj            VARCHAR(18) NULL,
  tipo            VARCHAR(20) NOT NULL DEFAULT 'distribuidor'
                  COMMENT 'distribuidor|representante|cliente',
  uf              CHAR(2) NULL,
  ativa           TINYINT(1) NOT NULL DEFAULT 1,
  created_at      TIMESTAMP NULL,
  updated_at      TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY organizacoes_cnpj_unique (cnpj),
  KEY organizacoes_ativa_index (ativa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. usuarios
-- Substitui a tabela `users` do Laravel. Ver padroes/04 secao 2 para os
-- cinco ajustes necessarios no framework.
-- ---------------------------------------------------------------------
CREATE TABLE usuarios (
  id                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome               VARCHAR(160) NOT NULL,
  email              VARCHAR(190) NOT NULL,
  email_verified_at  TIMESTAMP NULL,
  password           VARCHAR(255) NOT NULL,
  telefone           VARCHAR(20) NULL,
  empresa            VARCHAR(160) NULL COMMENT 'texto livre digitado pelo aluno',
  cargo              VARCHAR(120) NULL,
  organizacao_id     BIGINT UNSIGNED NULL COMMENT 'vinculo formal, atribuido pelo admin',
  situacao           VARCHAR(20) NOT NULL DEFAULT 'pendente'
                     COMMENT 'pendente|ativo|bloqueado',
  avatar_caminho     VARCHAR(255) NULL,
  ultimo_acesso_em   TIMESTAMP NULL,
  termos_aceitos_em  TIMESTAMP NULL,
  termos_ip          VARCHAR(45) NULL COMMENT 'suporta IPv6',
  remember_token     VARCHAR(100) NULL,
  created_at         TIMESTAMP NULL,
  updated_at         TIMESTAMP NULL,
  deleted_at         TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY usuarios_email_unique (email),
  KEY usuarios_situacao_index (situacao),
  KEY usuarios_organizacao_id_index (organizacao_id),
  CONSTRAINT usuarios_organizacao_id_foreign FOREIGN KEY (organizacao_id)
    REFERENCES organizacoes (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. categorias
-- ---------------------------------------------------------------------
CREATE TABLE categorias (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nome        VARCHAR(120) NOT NULL,
  slug        VARCHAR(140) NOT NULL,
  cor         CHAR(7) NULL COMMENT 'hex, ex: #4FBFC6',
  posicao     INT NOT NULL DEFAULT 0,
  created_at  TIMESTAMP NULL,
  updated_at  TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY categorias_slug_unique (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. cursos
-- ---------------------------------------------------------------------
CREATE TABLE cursos (
  id                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  categoria_id          BIGINT UNSIGNED NULL,
  responsavel_id        BIGINT UNSIGNED NOT NULL COMMENT 'instrutor dono, base da CursoPolicy',
  titulo                VARCHAR(180) NOT NULL,
  slug                  VARCHAR(200) NOT NULL,
  subtitulo             VARCHAR(255) NULL,
  descricao             LONGTEXT NULL,
  capa_caminho          VARCHAR(255) NULL COMMENT 'se vazio, usa a thumb da primeira aula',
  nivel                 VARCHAR(20) NOT NULL DEFAULT 'basico'
                        COMMENT 'basico|intermediario|avancado',
  situacao              VARCHAR(20) NOT NULL DEFAULT 'rascunho'
                        COMMENT 'rascunho|publicado|arquivado',
  moderar_comentarios   TINYINT(1) NOT NULL DEFAULT 0,
  liberacao_sequencial  TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'sempre 0 na v1 (D-06)',
  total_aulas           INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'cache',
  minutos_estimados     INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'cache',
  posicao               INT NOT NULL DEFAULT 0,
  publicado_em          TIMESTAMP NULL,
  created_at            TIMESTAMP NULL,
  updated_at            TIMESTAMP NULL,
  deleted_at            TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY cursos_slug_unique (slug),
  KEY cursos_situacao_publicado_em_index (situacao, publicado_em),
  KEY cursos_categoria_id_index (categoria_id),
  KEY cursos_responsavel_id_index (responsavel_id),
  CONSTRAINT cursos_categoria_id_foreign FOREIGN KEY (categoria_id)
    REFERENCES categorias (id) ON DELETE SET NULL,
  CONSTRAINT cursos_responsavel_id_foreign FOREIGN KEY (responsavel_id)
    REFERENCES usuarios (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. modulos
-- Sem slug: modulo nao tem URL propria.
-- ---------------------------------------------------------------------
CREATE TABLE modulos (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  curso_id    BIGINT UNSIGNED NOT NULL,
  titulo      VARCHAR(180) NOT NULL,
  descricao   TEXT NULL,
  posicao     INT NOT NULL DEFAULT 0,
  created_at  TIMESTAMP NULL,
  updated_at  TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY modulos_curso_id_posicao_index (curso_id, posicao),
  CONSTRAINT modulos_curso_id_foreign FOREIGN KEY (curso_id)
    REFERENCES cursos (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 6. aulas
-- `provedor` + `video_id` sao genericos de proposito (decisao D-02).
-- `duracao_segundos` e o denominador da regra dos 90% (RN-01).
-- ---------------------------------------------------------------------
CREATE TABLE aulas (
  id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  modulo_id         BIGINT UNSIGNED NOT NULL,
  titulo            VARCHAR(180) NOT NULL,
  slug              VARCHAR(200) NOT NULL,
  descricao         LONGTEXT NULL,
  provedor          VARCHAR(20) NOT NULL DEFAULT 'youtube'
                    COMMENT 'youtube|bunny|externo',
  video_id          VARCHAR(64) NULL COMMENT 'no YouTube, 11 caracteres',
  duracao_segundos  INT UNSIGNED NOT NULL DEFAULT 0,
  amostra_gratuita  TINYINT(1) NOT NULL DEFAULT 0,
  situacao          VARCHAR(20) NOT NULL DEFAULT 'rascunho'
                    COMMENT 'rascunho|publicada',
  posicao           INT NOT NULL DEFAULT 0,
  publicada_em      TIMESTAMP NULL,
  created_at        TIMESTAMP NULL,
  updated_at        TIMESTAMP NULL,
  deleted_at        TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY aulas_modulo_id_slug_unique (modulo_id, slug),
  KEY aulas_modulo_id_posicao_index (modulo_id, posicao),
  KEY aulas_situacao_index (situacao),
  CONSTRAINT aulas_modulo_id_foreign FOREIGN KEY (modulo_id)
    REFERENCES modulos (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 7. materiais
-- Disco privado. Nada em storage/app/public.
-- ---------------------------------------------------------------------
CREATE TABLE materiais (
  id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  aula_id          BIGINT UNSIGNED NOT NULL,
  titulo           VARCHAR(180) NOT NULL COMMENT 'nome exibido ao aluno',
  disco            VARCHAR(40) NOT NULL DEFAULT 'materiais',
  caminho          VARCHAR(255) NOT NULL COMMENT 'nome aleatorio no disco',
  mime             VARCHAR(120) NULL,
  tamanho_bytes    BIGINT UNSIGNED NOT NULL DEFAULT 0,
  total_downloads  INT UNSIGNED NOT NULL DEFAULT 0,
  posicao          INT NOT NULL DEFAULT 0,
  created_at       TIMESTAMP NULL,
  updated_at       TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY materiais_aula_id_posicao_index (aula_id, posicao),
  CONSTRAINT materiais_aula_id_foreign FOREIGN KEY (aula_id)
    REFERENCES aulas (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 8. matriculas
-- O UNIQUE e de correcao, nao de desempenho: sem ele, dois cliques
-- rapidos em "Inscrever-me" criam matricula dupla.
-- ---------------------------------------------------------------------
CREATE TABLE matriculas (
  id                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario_id            BIGINT UNSIGNED NOT NULL,
  curso_id              BIGINT UNSIGNED NOT NULL,
  origem                VARCHAR(20) NOT NULL DEFAULT 'aluno'
                        COMMENT 'aluno|admin|importacao',
  situacao              VARCHAR(20) NOT NULL DEFAULT 'ativa'
                        COMMENT 'ativa|concluida|cancelada',
  percentual_progresso  TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'cache (D-05)',
  ultima_aula_id        BIGINT UNSIGNED NULL COMMENT 'continue de onde parou',
  matriculado_em        TIMESTAMP NULL,
  concluido_em          TIMESTAMP NULL,
  created_at            TIMESTAMP NULL,
  updated_at            TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY matriculas_usuario_id_curso_id_unique (usuario_id, curso_id),
  KEY matriculas_curso_id_situacao_index (curso_id, situacao),
  KEY matriculas_ultima_aula_id_index (ultima_aula_id),
  CONSTRAINT matriculas_usuario_id_foreign FOREIGN KEY (usuario_id)
    REFERENCES usuarios (id) ON DELETE CASCADE,
  CONSTRAINT matriculas_curso_id_foreign FOREIGN KEY (curso_id)
    REFERENCES cursos (id) ON DELETE CASCADE,
  CONSTRAINT matriculas_ultima_aula_id_foreign FOREIGN KEY (ultima_aula_id)
    REFERENCES aulas (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 9. progresso_aulas
-- Fonte de verdade do progresso. posicao_maxima nunca diminui (RN-02).
-- ---------------------------------------------------------------------
CREATE TABLE progresso_aulas (
  id                        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  matricula_id              BIGINT UNSIGNED NOT NULL,
  aula_id                   BIGINT UNSIGNED NOT NULL,
  segundos_assistidos       INT UNSIGNED NOT NULL DEFAULT 0,
  posicao_maxima            INT UNSIGNED NOT NULL DEFAULT 0,
  primeira_visualizacao_em  TIMESTAMP NULL,
  concluido_em              TIMESTAMP NULL,
  created_at                TIMESTAMP NULL,
  updated_at                TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY progresso_aulas_matricula_id_aula_id_unique (matricula_id, aula_id),
  KEY progresso_aulas_aula_id_index (aula_id),
  KEY progresso_aulas_concluido_em_index (concluido_em),
  CONSTRAINT progresso_aulas_matricula_id_foreign FOREIGN KEY (matricula_id)
    REFERENCES matriculas (id) ON DELETE CASCADE,
  CONSTRAINT progresso_aulas_aula_id_foreign FOREIGN KEY (aula_id)
    REFERENCES aulas (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 10. comentarios
-- Thread rasa de proposito: pergunta -> respostas. Sem resposta de resposta.
-- ---------------------------------------------------------------------
CREATE TABLE comentarios (
  id                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  aula_id            BIGINT UNSIGNED NOT NULL,
  usuario_id         BIGINT UNSIGNED NOT NULL,
  comentario_pai_id  BIGINT UNSIGNED NULL,
  corpo              TEXT NOT NULL,
  situacao           VARCHAR(20) NOT NULL DEFAULT 'aprovado'
                     COMMENT 'pendente|aprovado|oculto',
  e_resposta         TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'resposta oficial do instrutor',
  fixado             TINYINT(1) NOT NULL DEFAULT 0,
  created_at         TIMESTAMP NULL,
  updated_at         TIMESTAMP NULL,
  deleted_at         TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY comentarios_aula_id_situacao_created_at_index (aula_id, situacao, created_at),
  KEY comentarios_usuario_id_index (usuario_id),
  KEY comentarios_comentario_pai_id_index (comentario_pai_id),
  CONSTRAINT comentarios_aula_id_foreign FOREIGN KEY (aula_id)
    REFERENCES aulas (id) ON DELETE CASCADE,
  CONSTRAINT comentarios_usuario_id_foreign FOREIGN KEY (usuario_id)
    REFERENCES usuarios (id) ON DELETE CASCADE,
  CONSTRAINT comentarios_comentario_pai_id_foreign FOREIGN KEY (comentario_pai_id)
    REFERENCES comentarios (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 11. configuracoes
-- O que o admin muda sem deploy.
-- ---------------------------------------------------------------------
CREATE TABLE configuracoes (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  chave       VARCHAR(80) NOT NULL,
  valor       TEXT NULL,
  tipo        VARCHAR(20) NOT NULL DEFAULT 'string' COMMENT 'bool|int|string|json',
  descricao   VARCHAR(255) NULL,
  created_at  TIMESTAMP NULL,
  updated_at  TIMESTAMP NULL,
  PRIMARY KEY (id),
  UNIQUE KEY configuracoes_chave_unique (chave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Valores iniciais de configuracao
-- ---------------------------------------------------------------------
INSERT INTO configuracoes (chave, valor, tipo, descricao, created_at, updated_at) VALUES
 ('percentual_conclusao', '90', 'int',  'Percentual do video que conclui a aula automaticamente (RN-01)', NOW(), NOW()),
 ('intervalo_ping',       '10', 'int',  'Intervalo em segundos entre os pings de progresso (RN-02)', NOW(), NOW()),
 ('texto_boas_vindas',    '',   'string', 'Texto exibido no painel do aluno', NOW(), NOW());

-- =====================================================================
-- Backlog pos-MVP: NAO criar na v1.
-- Desenhadas aqui so para garantir que o modelo atual nao as impeca.
-- Ver etapas/99-backlog-pos-mvp.md
--
-- questionarios  (id, curso_id|modulo_id, titulo, nota_minima, tentativas_max)
-- questoes       (id, questionario_id, enunciado, posicao)
-- alternativas   (id, questao_id, texto, correta, posicao)
-- tentativas     (id, matricula_id, questionario_id, nota, finalizada_em)
-- respostas      (id, tentativa_id, questao_id, alternativa_id)
-- certificados   (id, matricula_id, codigo UNIQUE, emitido_em, pdf_caminho)
-- =====================================================================
