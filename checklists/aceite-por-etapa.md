# Aceite por etapa — visão condensada

Um lugar só para acompanhar o avanço. O detalhe está em cada arquivo de
`etapas/`. Marque só depois de **executar**.

---

## Etapa 0 — Fundação

- [ ] `migrate:fresh --seed` roda do zero
- [ ] Cadastro → e-mail → verificação → `/app` funciona
- [ ] `aprovacao_manual` leva a `/aguardando-aprovacao`; admin aprova e libera
- [ ] Reset de senha funciona ponta a ponta
- [ ] `/admin` bloqueia aluno, libera admin
- [ ] Nenhuma referência à tabela `users`
- [ ] Pint + testes verdes

## Etapa 1 — Domínio e admin

- [ ] Curso completo montado pela interface
- [ ] Colar link do YouTube preenche título e duração
- [ ] Arrastar e soltar persiste após recarregar
- [ ] Publicar curso incompleto lista todas as pendências de uma vez
- [ ] Material em disco privado, sem acesso por URL direta
- [ ] Instrutor só vê os cursos dele
- [ ] Pint + testes verdes

## Etapa 2 — Vitrine, catálogo e matrícula

- [ ] Visitante encontra o curso e vê a ementa
- [ ] Aula de amostra toca sem login
- [ ] Inscrição em um clique
- [ ] Dois cliques rápidos não duplicam matrícula
- [ ] Busca e filtro funcionam, com estado vazio
- [ ] Utilizável a 400px
- [ ] Pint + testes verdes

## Etapa 3 — Sala de aula e progresso

- [ ] Aula assistida inteira é concluída sozinha
- [ ] Arrastar a barra até o fim **não** conclui
- [ ] Fechar e voltar retoma do ponto certo
- [ ] Última aula concluída conclui o curso
- [ ] Sala de aula utilizável a 400px, índice abaixo do player
- [ ] Ping não passa por Livewire
- [ ] Pint + testes verdes

## Etapa 4 — Materiais e perguntas

- [ ] Download funciona com matrícula, dá 403 sem ela
- [ ] URL direta no storage não funciona
- [ ] Pergunta → resposta → notificação + e-mail
- [ ] Curso com moderação segura a pergunta até aprovar
- [ ] Fila de perguntas sem resposta é o primeiro widget do painel
- [ ] Pint + testes verdes

## Etapa 5 — Relatórios e operação

- [ ] XLSX exportado com números corretos
- [ ] CSV com linhas inválidas importa o resto e relata as falhas
- [ ] Anonimização limpa o pessoal e preserva a estatística
- [ ] Configuração muda comportamento sem deploy
- [ ] `/saude` responde 200
- [ ] Pint + testes verdes

## Etapa 6 — Entrega

- [ ] Usuário real percorre o fluxo em produção sem ajuda
- [ ] E-mail chega na caixa de entrada
- [ ] Fila processa e reinicia sozinha
- [ ] Backup restaurado em ambiente de teste
- [ ] `APP_DEBUG=false`, nada sensível versionado
- [ ] `docs/operacao.md` permite alguém de fora operar

---

## Decisões em aberto — acompanhamento

| # | Pergunta | Trava | Respondida? |
|---|---|---|---|
| A-01 | Existe conteúdo confidencial? | etapa 3 | [ ] |
| A-02 | Conta nova: aprovada ou pendente? | etapa 2 | [ ] |
| A-03 | Quem grava? Qual o curso piloto? | etapa 1 | [ ] |
| A-04 | Certificado é diferencial? | pós-MVP | [ ] |
| A-05 | Vínculo com empresa já na v1? | etapa 2 | [ ] |
| A-06 | Existe manual de marca? | etapa 2 | [ ] |
| A-07 | Onde hospedar? | etapa 6 | [ ] |
