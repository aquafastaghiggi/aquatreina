# AGENTS.md — contrato de trabalho

Você vai construir o **Aquafast Treina**: uma plataforma de treinamentos em
Laravel para o público externo da Aquafast. Este arquivo manda em tudo. Quando
ele conflitar com seu hábito, ele vence.

---

## 1. Antes de escrever qualquer linha

Leia, nesta ordem:

1. `docs/00-visao-geral.md`
2. `docs/01-decisoes.md`
3. `docs/02-modelo-de-dados.md`
4. `padroes/01-codigo-e-nomenclatura.md`
5. `padroes/02-laravel.md`
6. A etapa que você foi mandado executar, em `etapas/`

Não leia todas as etapas de uma vez. Leia só a sua.

---

## 2. Regras que não se negociam

**Uma etapa por vez.** Você executa exatamente a etapa pedida. Ao terminar,
pare, apresente o resultado contra os critérios de aceite daquela etapa, e
espere confirmação. Não comece a etapa seguinte por iniciativa própria.

**Não invente escopo.** Se algo não está em `docs/` ou na etapa atual, não
existe. Quiz, certificado, gamificação, fórum, chat e integração com ERP estão
fora da v1 — mesmo que pareçam uma boa ideia.

**Não invente dados.** Nomes de tabela, coluna, rota e enum estão fixados em
`docs/02-modelo-de-dados.md` e `artefatos/schema.sql`. Copie, não reinterprete.
Se um campo que você precisa não existe, isso é uma divergência a reportar,
não a resolver sozinho.

**Português no domínio.** Tabelas, colunas, models, métodos, variáveis, rotas e
interface em PT-BR sem acento. A exceção é a API do framework
(`created_at`, `up()`, `handle()`, `render()`). Detalhe em
`padroes/01-codigo-e-nomenclatura.md`.

**Nada de dado sensível no repositório.** Nenhuma chave, senha ou token no
código. Tudo em `.env`, com espelho em `.env.example` usando valores falsos.

**Teste é entregável.** Uma etapa sem os testes listados nela não está
concluída. `padroes/05-testes.md` define o mínimo.

**Migration não se reescreve.** Depois que uma migration foi versionada, mudança
vira nova migration. Vale a partir da etapa 1.

---

## 3. Como reportar ao fim de cada etapa

Produza, nesta forma:

```
## Etapa N — <nome>

### Feito
- <item> → <arquivos tocados>

### Critérios de aceite
- [x] <critério copiado do arquivo da etapa> — como verifiquei
- [ ] <critério não atendido> — por quê

### Divergências e decisões
- <algo que a spec não cobria, e o que eu assumi>

### Como verificar manualmente
1. <passo>
```

A seção **Divergências** é a mais importante. Se ela vier vazia em toda etapa,
você provavelmente está escondendo suposições em vez de reportá-las.

---

## 4. Stack fixada

| Camada | Escolha | Observação |
|---|---|---|
| Framework | Laravel 12, PHP 8.3+ | |
| Auth | Laravel Fortify | Sem Jetstream. Verificação de e-mail obrigatória. |
| Front do aluno | Livewire 3 + Alpine + Tailwind | |
| Admin | Filament 4 em `/admin` | |
| Banco | MySQL 8 | |
| Fila | driver `database` | Redis só se o volume exigir. Não exige na v1. |
| Testes | Pest | |
| Lint | Laravel Pint (preset `laravel`) | |

Lista completa de pacotes em `artefatos/composer-packages.md`. Não acrescente
dependência fora dessa lista sem reportar como divergência.

---

## 5. Fluxo por etapa

1. Ler o arquivo da etapa inteiro antes de começar.
2. Conferir os pré-requisitos. Se a etapa anterior não passou nos critérios de
   aceite dela, pare e reporte.
3. Implementar na ordem das tarefas.
4. Rodar `vendor/bin/pint` e `php artisan test`. Ambos verdes.
5. Commitar conforme `padroes/06-git-e-commits.md`.
6. Reportar no formato da seção 3.

---

## 6. Armadilhas conhecidas deste projeto

- **Progresso de vídeo não se confia ao cliente.** O navegador manda a posição;
  o servidor só aceita avanço coerente. Regra exata em `docs/05-integracao-youtube.md`.
- **Material de apoio nunca em `public/`.** Download sempre por rota autenticada
  que valida matrícula. Ver `docs/06-seguranca-e-lgpd.md`.
- **A tabela de usuários chama `usuarios`.** Isso exige ajuste em
  `config/auth.php` e nas migrations do framework. Está detalhado em
  `padroes/04-banco-e-migrations.md` §2. É o erro mais provável da etapa 0.
- **`percentual_progresso` é cache**, recalculado por evento. Nunca leia
  progresso somando na hora dentro de um componente Livewire.
- **Vídeo do YouTube "não listado" não é privado.** O campo é `provedor` +
  `video_id`, genérico de propósito. Não acople o código ao YouTube fora de
  `app/Servicos/Video/`.
