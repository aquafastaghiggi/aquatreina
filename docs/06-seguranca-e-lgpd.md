# 06 — Segurança e LGPD

O app fica **exposto à internet**, com formulário de cadastro aberto, guardando
dados pessoais de gente que não é funcionária da Aquafast. Isso muda o padrão de
cuidado.

---

## 1. Portaria do cadastro

Três camadas, em ordem de custo:

1. **Honeypot + rate limit.** Campo escondido que só bot preenche; `throttle` de
   5 cadastros por IP por hora. Grátis.
2. **Verificação de e-mail obrigatória** antes do primeiro play. Já previsto.
3. **Aprovação manual** — configuração `aprovacao_manual`. Conta nasce
   `pendente` e o admin libera.

Padrão do pacote: **`aprovacao_manual = true`**. Desligar depois é uma linha;
limpar base já suja não é.

Não use CAPTCHA de terceiro na v1. As três camadas acima resolvem o volume
esperado, e CAPTCHA acrescenta um processador de dados externo à conversa de
LGPD sem necessidade.

## 2. Autorização

- Toda checagem no **backend**, por Policy. Esconder botão na Blade não é
  autorização.
- Filament usa as **mesmas** Policies. Nada de regra duplicada.
- Policies obrigatórias: `CursoPolicy`, `AulaPolicy`, `MaterialPolicy`,
  `ComentarioPolicy`, `MatriculaPolicy`.
- O acesso do aluno à aula depende de matrícula ativa **no curso da aula**,
  verificado pelo caminho `aula → modulo → curso`. Não confie no `curso` da URL.

## 3. Materiais

- Disco `materiais` privado, fora de `storage/app/public`.
- Download por `GET /app/materiais/{material}/baixar`: valida matrícula,
  incrementa contador, devolve `Storage::download()`.
- Nome do arquivo no disco é aleatório; o nome bonito vem de `materiais.titulo`.
- Limite de upload: 20 MB. Tipos aceitos: `pdf`, `xlsx`, `docx`, `pptx`, `png`,
  `jpg`, `zip`. Validado por MIME real, não por extensão.

Um PDF de política comercial vazando por URL direta é o tipo de coisa que só se
descobre tarde.

## 4. Dados pessoais coletados

Mínimo necessário:

| Campo | Por quê |
|---|---|
| nome | identificar o aluno |
| e-mail | login e notificação |
| telefone | contato do comercial |
| empresa, cargo | segmentar relatório |

**Não coletar CPF.** Só faria sentido com certificado nominal, que está no
backlog. Se A-04 mudar isso, a coleta entra com finalidade declarada.

Não coletar: data de nascimento, endereço, dado bancário, documento.

## 5. Consentimento

- Aceite de termos e política de privacidade **no cadastro**, com checkbox não
  pré-marcada.
- Grava `termos_aceitos_em` e `termos_ip`.
- Mudança material nos termos exige novo aceite no próximo login.
- `/termos` e `/privacidade` públicas e versionadas.

## 6. Direitos do titular

- **Acesso:** `/app/perfil` mostra tudo que o sistema guarda da pessoa.
- **Correção:** o próprio aluno edita nome, telefone, empresa e cargo.
- **Exclusão:** por **anonimização**, não `DELETE`. Nome vira `Usuario removido`,
  e-mail vira hash, telefone/empresa/cargo/avatar são limpos, `situacao` vira
  `bloqueado`. Matrículas e progresso permanecem para não furar a estatística
  agregada. Comentários são ocultados.
- Ação `AnonimizarUsuario`, disparada pelo admin, registrada em `activity_log`.

## 7. Cookies e rastreamento

- Só cookie de sessão e CSRF. Sem analytics de terceiro na v1.
- Embed por `youtube-nocookie.com`.
- Se um dia entrar analytics, entra banner de consentimento junto — não antes,
  não depois.

## 8. Cabeçalhos e transporte

- HTTPS obrigatório, HSTS ligado.
- `Content-Security-Policy` permitindo `youtube-nocookie.com` e
  `i.ytimg.com`; `frame-src` restrito a esses.
- `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`,
  `Referrer-Policy: strict-origin-when-cross-origin`.
- Cookies `secure`, `httponly`, `samesite=lax`.

## 9. Auditoria

`spatie/laravel-activitylog` registrando: publicar/arquivar curso, aprovar/
bloquear/anonimizar usuário, matricular à força, ocultar comentário, mudar
configuração.

Barato agora, caro de acrescentar depois.

## 10. Senhas e sessão

- Mínimo 8 caracteres, validação `Password::defaults()` com checagem de
  vazamento (`uncompromised()`).
- Throttle de login: 5 tentativas por minuto por e-mail+IP.
- Sessão de 120 minutos. `usuarios.situacao = bloqueado` derruba na próxima
  requisição, via middleware `garantir.ativo`.

## 11. Infraestrutura

- App e banco **isolados** da intranet. Não reaproveitar a instância do
  ControlePCP V2 nem do Portal v2.
- Banco sem porta exposta à internet.
- Backup diário, retenção 30 dias, com **restauração testada** pelo menos uma
  vez antes do go-live.
- `APP_DEBUG=false` em produção. Verificado na etapa 6.
