# Etapa 5 — Relatórios e operação

> Objetivo: o time consegue operar a plataforma sem pedir consulta no banco.

**Pré-requisitos:** etapa 4 aprovada.
**Duração estimada:** 1 semana.
**Leia antes:** `docs/06-seguranca-e-lgpd.md` §6, `padroes/04-banco-e-migrations.md` §6.

---

## Tarefas

### 5.1 Relatórios

- [ ] Página `/admin/relatorios`
- [ ] **Progresso por curso:** matriculados, concluídos, percentual médio,
      aula com maior abandono
- [ ] **Progresso por aluno:** filtro por empresa, situação e período; cursos,
      percentual, última atividade
- [ ] **Por organização:** matriculados e concluídos por empresa (atende A-05
      sem precisar do papel de gestor)
- [ ] Exportação XLSX de cada um, via `maatwebsite/excel`
- [ ] Exportação de mais de 1.000 linhas vai para a fila e chega por e-mail
- [ ] Consultas agregadas, indexadas e paginadas — nada de `all()`

### 5.2 Importação de alunos

- [ ] Upload de CSV em `/admin/usuarios`
- [ ] Colunas: `nome, email, telefone, empresa, cargo, organizacao, cursos`
      (cursos por slug, separados por `;`)
- [ ] Pré-visualização antes de confirmar, com as linhas inválidas destacadas
- [ ] Linha inválida não derruba o lote: importa o que dá e relata o resto
- [ ] Cria usuário com senha aleatória e dispara convite de definição de senha
- [ ] Matrícula criada com `origem = importacao`
- [ ] Processamento em fila, com relatório final
- [ ] Modelo de CSV para download

### 5.3 Configurações

- [ ] Página `/admin/configuracoes` lendo e gravando a tabela `configuracoes`
- [ ] `percentual_conclusao`, `intervalo_ping`, `texto_boas_vindas`
- [ ] Edição dos textos de `/termos` e `/privacidade`, com versionamento
- [ ] Mudança material nos termos exige novo aceite no próximo login

### 5.4 LGPD

- [ ] Ação `AnonimizarUsuario` conforme `docs/06-seguranca-e-lgpd.md` §6
- [ ] Botão na ficha do aluno, com confirmação explícita e irreversível
- [ ] Registro em `activity_log`
- [ ] Bloco "seus dados" em `/app/perfil` listando tudo que o sistema guarda
- [ ] Exportação dos próprios dados em JSON pelo aluno

### 5.5 Operação

- [ ] Comando `treina:conferir-progresso` agendado diariamente
- [ ] Comando `treina:resumo-semanal` — e-mail ao admin com números da semana
- [ ] Agendamento configurado no `routes/console.php`
- [ ] Página de saúde `/saude` retornando 200 com versão e status do banco

---

## Testes obrigatórios

- [ ] relatório de progresso por curso bate com dados montados na factory
- [ ] exportação XLSX gera arquivo com o número certo de linhas
- [ ] importação com uma linha inválida importa as demais e relata a falha
- [ ] importação não duplica usuário com e-mail já existente
- [ ] anonimização limpa os campos certos e preserva matrículas e progresso
- [ ] anonimização registra em `activity_log`
- [ ] cadastro novo permanece pendente até aprovação administrativa
- [ ] `treina:conferir-progresso` corrige `percentual_progresso` divergente

---

## Critérios de aceite

1. O admin exporta o progresso de um curso em XLSX e os números batem.
2. Um CSV com 50 alunos, sendo 3 linhas inválidas, importa 47 e relata as 3.
3. Anonimizar um aluno remove os dados pessoais sem furar as estatísticas.
4. Mudar uma configuração tem efeito sem deploy.
5. `/saude` responde 200.
6. Pint e testes verdes.

## Não faça nesta etapa

Deploy, hardening de produção, otimização fina. Etapa 6.
