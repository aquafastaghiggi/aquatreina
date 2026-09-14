# Backlog pós-MVP

**Nada aqui entra na v1.** Esta lista existe para que as decisões da v1 não
fechem portas, e para que a IA executora saiba reconhecer o que está fora de
escopo quando a ideia aparecer no meio do caminho.

Ordem sugerida, não obrigatória. Reavaliar depois dos 30 dias de acompanhamento
da etapa 6.

---

## B-01 · Quiz por módulo

Tabelas previstas: `questionarios`, `questoes`, `alternativas`, `tentativas`,
`respostas`.

Decisões pendentes: nota mínima por questionário ou global; número de tentativas;
se reprovar trava o avanço (conflita com D-06); se o resultado aparece no
relatório.

Impacto no modelo atual: nenhum. `aulas` ganha relação opcional com questionário.

## B-02 · Certificado em PDF

Tabela `certificados` (`matricula_id`, `codigo` unique, `emitido_em`,
`pdf_caminho`) + rota pública `/certificado/{codigo}` para validação.

Depende de A-04. Se entrar, a discussão de coleta de CPF volta — e com ela uma
finalidade nova na política de privacidade.

Impacto: emissão pendura no evento `CursoConcluido`, que já existe.

## B-03 · Gestor do parceiro

Quarto papel: vê o progresso dos alunos da própria organização, sem editar nada.

Depende de `organizacoes` estar povoada e de os alunos estarem vinculados —
motivo pelo qual a tabela existe desde a v1.

Impacto: uma Policy nova e uma tela de relatório filtrada. Barato, se o vínculo
tiver sido mantido em dia.

## B-04 · Liberação sequencial

`cursos.liberacao_sequencial` já existe no schema, sempre `false`.

Ligar exige: tela no admin, bloqueio na sala de aula, ajuste no redirecionamento
de `/app/c/{slug}` e tratamento do caso "aula do meio foi despublicada".

Só faça se a operação pedir. Ver D-06 antes.

## B-05 · PWA instalável

O público é de campo, com conexão irregular. Instalável na tela inicial faz
sentido; **offline de vídeo não**, porque o vídeo é do YouTube.

Escopo realista: manifest, service worker para o casco da aplicação, e página
offline decente. Não prometa aula offline.

## B-06 · Provedor de vídeo alternativo

Se A-01 trouxer conteúdo confidencial. A interface `ProvedorVideo` já prevê:
acrescentar `BunnyProvedor` (ou similar) e permitir escolher o provedor por aula
no construtor de currículo.

O trabalho real não é o código — é migrar os vídeos e revisar quem já tinha o
link antigo.

## B-07 · SSO com o Portal v2

Deixado de fora por D-01. Se instrutor e admin reclamarem de duas senhas, vale
um OAuth simples com o portal como provedor, mantendo o login próprio para o
público externo.

## B-08 · Trilhas

Agrupar cursos numa sequência ("Integração do novo distribuidor" = 3 cursos).
Tabela `trilhas` + pivô. Só faz sentido com catálogo maior que uns 10 cursos.

## B-09 · Busca melhor

Busca por `LIKE` atende até algumas dezenas de cursos. Depois disso, busca
full-text do MySQL ou Meilisearch, incluindo o texto das descrições de aula.

## B-10 · Legendas e transcrição

O YouTube gera legenda automática. Puxar a transcrição para dentro da plataforma
tornaria o conteúdo buscável e acessível — bom para quem assiste sem som no
depósito.
