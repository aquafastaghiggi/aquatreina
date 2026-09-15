# 05 — Testes

Pest. Teste é entregável: etapa sem os testes listados nela não está concluída.

## O que testar

Prioridade, de cima para baixo:

1. **Ações** (`app/Acoes/`) — é onde a regra mora. Teste unitário.
2. **Regras de negócio identificadas** (RN-01 a RN-11) — cada uma com pelo
   menos um teste citando o código no nome.
3. **Autorização** — toda Policy, no caminho negado.
4. **Rotas críticas** — teste de feature: matricular, assistir, concluir,
   baixar material, comentar.
5. **Serviços de vídeo** — extração de ID com tabela de casos.

O que **não** testar: getter de model, rótulo de enum, view renderizando texto
estático, configuração do Filament.

## Nomes

Arquivo: `tests/Unit/Acoes/concluir_aula_test.php`,
`tests/Feature/Aluno/sala_de_aula_test.php`.

Descrição em português, citando a regra:

```php
it('conclui a aula automaticamente ao atingir 90% do video (RN-01)', function () {
    // ...
});

it('nao soma tempo assistido quando o aluno arrasta a barra (RN-02)', function () {
    // ...
});

it('nao deixa matricular em curso que nao esta publicado (RN-05)', function () {
    // ...
});
```

## Estrutura

```
tests/
├── Unit/
│   ├── Acoes/
│   └── Servicos/
└── Feature/
    ├── Publico/
    ├── Aluno/
    └── Admin/
```

## Mínimo por etapa

| Etapa | Testes obrigatórios |
|---|---|
| 0 | login, cadastro pendente, aprovação administrativa, bloqueio por `situacao` (RN-08) |
| 1 | extração de ID de vídeo (tabela de casos), CRUD de curso via Policy, publicação com RN-09 |
| 2 | inscrição idempotente (RN-05), catálogo só lista publicados, aula rascunho dá 404 (RN-03) |
| 3 | RN-01 nos dois caminhos, RN-02 nos cinco cenários, RN-04 nos dois sentidos |
| 4 | download só com matrícula (RN-07), moderação (RN-06), notificação de resposta |
| 5 | relatório com números corretos, importação CSV com linha inválida, anonimização |
| 6 | smoke de todas as rotas públicas e do aluno |

## Cenários que não podem faltar em RN-02

1. avanço normal → soma e atualiza posição
2. aluno volta no vídeo → ignora, não diminui `posicao_maxima`
3. salto de barra para frente → atualiza posição, **não** soma tempo
4. posição maior que a duração → 422
5. ping sem matrícula ativa → 403

## Regras de escrita

- Cada teste monta o próprio cenário com factory. Sem dependência de ordem.
- `RefreshDatabase` em feature; unit sem banco quando der.
- Sem chamada de rede real. `Http::fake()` para a API do YouTube.
- Sem `sleep()`. `travel()` para tempo.
- Teste que falha intermitentemente é bug do teste: conserte ou apague.

## Comandos

```bash
php artisan test                 # tudo
php artisan test --filter=RN-02  # uma regra
vendor/bin/pint --test           # estilo, sem alterar
```

Ambos verdes antes de reportar qualquer etapa como concluída.
