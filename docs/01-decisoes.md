# 01 — Decisões de arquitetura

Formato curto de ADR. Cada decisão tem contexto, escolha e consequência. Se a
IA executora precisar contrariar alguma, isso é divergência a reportar — não a
resolver sozinha.

---

## D-01 · Aplicação Laravel separada, não módulo do Portal v2

**Contexto.** A Aquafast já tem o Portal v2 (intranet) em Laravel. Seria
tentador acrescentar mais um módulo lá.

**Escolha.** Aplicação nova, domínio próprio (`treinamentos.aquafast.com.br`),
banco próprio, deploy independente.

**Por quê.** O público é externo. Expor a intranet à internet para servir
distribuidor é ampliar a superfície de ataque do sistema que roda a produção.
Separar também deixa o ciclo de release independente do portal.

**Consequência.** Não há SSO com o portal na v1. Instrutor e admin terão login
próprio aqui. Aceito.

---

## D-02 · Vídeo no YouTube, mas o código não conhece o YouTube

**Contexto.** Custo zero, CDN global, player que funciona em qualquer celular.

**Escolha.** Vídeos "não listados" no canal da Aquafast, embed por
`youtube-nocookie.com`. Porém, no banco, o campo é genérico:
`aulas.provedor` + `aulas.video_id`.

**Por quê.** Vídeo não listado **não é privado**: qualquer um com o link
assiste, sem login, fora da plataforma. Para treinamento de produto isso é
aceitável. Para política comercial, tabela de preço ou margem, não é. Quando
esse conteúdo aparecer, será preciso um hospedador com link assinado.

**Consequência.** Toda a integração fica isolada em `app/Servicos/Video/`.
Trocar de provedor num curso específico é acrescentar uma implementação, não
reescrever a sala de aula.

---

## D-03 · Catálogo aberto com portaria

**Contexto.** O aluno se inscreve sozinho; não há matrícula manual no caminho
feliz.

**Escolha.** Cadastro público sem verificação de e-mail. Toda conta nova entra
como `pendente` e somente um administrador pode aprová-la no painel.

**Por quê.** Formulário aberto na internet enche de cadastro falso em semanas, e
aí o relatório de "alunos ativos" não significa nada.

**Consequência.** Não existe caminho de autoaprovação. O e-mail continua sendo
usado para login, notificações e recuperação de senha.

---

## D-04 · Filament no admin

**Contexto.** O admin tem 6 CRUDs, filtros, upload, exportação e moderação.

**Escolha.** Filament 4 em `/admin`, usando as mesmas Policies do app.

**Por quê.** É a maior economia do projeto. Escrever esse admin à mão custaria
mais que todo o resto.

**Consequência.** Uma tela não cabe no Filament padrão: o construtor de
currículo (arrastar e soltar módulos e aulas). Vira `Filament\Pages` customizada
com Livewire dentro. Previsto na etapa 1.

---

## D-05 · Progresso denormalizado em `matriculas`

**Contexto.** A tela `/app` mostra barra de progresso de N cursos.

**Escolha.** `matriculas.percentual_progresso` é cache, atualizado por evento
quando uma aula é concluída e por job noturno de conferência.

**Por quê.** Calcular na hora é um `COUNT` por card, e Livewire re-renderiza
mais do que se espera.

**Consequência.** Existe uma fonte de verdade (`progresso_aulas`) e um cache.
O job noturno existe para quando divergirem. Publicar aula nova muda o
denominador — o evento precisa tratar isso.

---

## D-06 · Navegação livre dentro do curso

**Contexto.** Plataformas de infoproduto liberam aula por gotejamento.

**Escolha.** Todas as aulas do curso matriculado ficam abertas. O campo
`cursos.liberacao_sequencial` existe, mas nasce `false` e sem tela na v1.

**Por quê.** Treinamento técnico é consultado fora de ordem — o representante
precisa da aula 7 no meio de um atendimento. Travar gera ticket, não aprendizado.

---

## D-07 · Português no domínio

**Contexto.** Time pequeno, todo o resto do parque (ControlePCP V2) já é assim.

**Escolha.** Tabelas, colunas, models, métodos, rotas e interface em PT-BR sem
acento. API do framework permanece em inglês.

**Consequência.** A tabela de autenticação é `usuarios`, o que exige ajuste em
`config/auth.php` e nas migrations padrão do Laravel. Detalhado em
`padroes/04-banco-e-migrations.md`.

---

## D-08 · Sem quiz e sem certificado na v1

**Escolha.** Ficam no backlog. O modelo de dados já prevê as tabelas, mas elas
não recebem migration agora.

**Por quê.** Nenhum dos dois é necessário para provar que a plataforma funciona.
Certificado, em particular, arrasta a discussão de CPF — que se evita enquanto
puder.

---

## Decisões ainda em aberto

Nenhuma bloqueia a etapa 0. Precisam de resposta antes das etapas indicadas.

| # | Pergunta | Trava a etapa |
|---|---|---|
| A-01 | Algum curso previsto trata de política comercial, preço ou margem? Se sim, esse curso não vai para o YouTube. | 3 |
| A-02 | Conta nova entra aprovada ou pendente? (padrão do pacote: pendente) | 2 |
| A-03 | Quem grava os vídeos, e qual é o curso piloto? | 1 |
| A-04 | Certificado é diferencial de adesão para esse público? Se for, sobe do backlog e o CPF volta à mesa. | pós-MVP |
| A-05 | Precisa amarrar aluno à empresa já na v1 (relatório "quantos da Distribuidora X concluíram")? | 2 |
| A-06 | Existe manual de marca da Aquafast (cores, tipografia, uso do logo)? | 2 |
| A-07 | Hospedagem: vhost no servidor da intranet ou VPS isolada? | 6 |
