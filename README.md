# Aquafast Treina

Plataforma Laravel de treinamentos para distribuidores, representantes e
clientes da Aquafast. A v1 oferece cadastro com aprovação, catálogo, cursos em
vídeo, progresso confiável, materiais privados, perguntas e administração.

## Requisitos

- PHP 8.3 ou superior
- MySQL 8
- Composer 2
- Node.js 20 ou superior

## Instalação local

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Em outro terminal, mantenha as filas em execução:

```bash
php artisan queue:work database --queue=emails,default --tries=3
```

O seed administrativo exige `ADMIN_EMAIL` e `ADMIN_PASSWORD` preenchidos no
`.env`. O curso demonstrativo só é criado nos ambientes `local` e `testing`.

## Qualidade

```bash
vendor/bin/pint --test
php artisan test
php artisan test --configuration phpunit.production.xml
npm run build
```

## Deploy

O destino planejado é uma VPS Linux isolada da intranet. Os modelos de Nginx,
Supervisor, cron, logrotate, deploy e backup estão em `deploy/`. Use
`.env.production.example` como inventário de variáveis, nunca como arquivo com
credenciais reais.

O repositório oficial é `https://github.com/aquafastaghiggi/aquatreina.git`.
Produção acompanha exclusivamente o branch `main`, usando atualização
fast-forward para impedir que alterações manuais da VPS sejam sobrescritas.

O procedimento completo de publicação, operação e recuperação está em
[`docs/operacao.md`](docs/operacao.md).

## Escopo

Quiz, certificado, PWA, trilhas e SSO estão fora da v1. Consulte
`etapas/99-backlog-pos-mvp.md` antes de planejar funcionalidades pós-MVP.
