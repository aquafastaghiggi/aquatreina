# 07 — Definition of Done

Uma etapa só está concluída quando **todos** os itens abaixo são verdadeiros.
Não é checklist de formalidade: é o que separa "a IA disse que terminou" de
"terminou".

## Funcional

- [ ] Todos os critérios de aceite da etapa foram verificados **executando**, não lendo código
- [ ] O caminho feliz funciona no navegador, de ponta a ponta
- [ ] Os estados de erro têm mensagem em português compreensível
- [ ] Os estados vazios estão implementados conforme `docs/03-mapa-de-telas-e-rotas.md`

## Código

- [ ] `vendor/bin/pint --test` sem apontamento
- [ ] `php artisan test` verde, incluindo os testes mínimos da etapa
- [ ] Nenhum número mágico: tudo em `config/treina.php`
- [ ] Nenhuma regra de negócio dentro de controller ou componente Livewire
- [ ] Nenhum `dd()`, `dump()`, `var_dump()` ou `console.log` esquecido
- [ ] Nenhum TODO sem issue correspondente

## Banco

- [ ] `php artisan migrate:fresh --seed` roda limpo do zero
- [ ] `down()` de toda migration nova funciona
- [ ] Índices e `UNIQUE` de `docs/02-modelo-de-dados.md` presentes
- [ ] Nenhuma migration antiga reescrita

## Segurança

- [ ] Toda rota nova tem middleware adequado
- [ ] Toda ação sensível tem Policy, verificada no backend
- [ ] Nenhum dado sensível em log
- [ ] Nada novo em `public/` que devesse ser privado

## Interface

- [ ] Funciona a 400px de largura
- [ ] Foco de teclado visível
- [ ] Nenhum texto em inglês vazando para a tela

## Entrega

- [ ] Commits conforme `padroes/06-git-e-commits.md`
- [ ] Relatório no formato de `AGENTS.md` §3, com a seção **Divergências** preenchida
- [ ] `.env.example` atualizado se surgiu variável nova
- [ ] Parou e esperou validação

---

**Regra de ouro:** se a seção "Divergências" veio vazia em várias etapas
seguidas, ela está sendo omitida, não ausente. Toda implementação real encontra
buraco de especificação.
