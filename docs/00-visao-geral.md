# 00 — Visão geral

## O problema

A Aquafast precisa treinar gente que **não trabalha na Aquafast**: distribuidores
que revendem o produto, representantes comerciais e clientes de maior porte.
Hoje esse conhecimento circula por PDF solto, visita presencial e grupo de
WhatsApp. Não dá para saber quem viu o quê, o material fica desatualizado em
versões paralelas, e cada novo distribuidor recomeça do zero.

## A solução

Uma plataforma própria onde:

- o conteúdo é **vídeo hospedado no YouTube**, organizado em curso → módulo → aula;
- o aluno **se cadastra sozinho**, escolhe no catálogo aberto e assiste no ritmo dele;
- cada aula pode ter **materiais de apoio** (PDF, ficha técnica, checklist);
- o aluno **pergunta na aula** e o instrutor responde;
- o admin vê **quem assistiu o quê**, por aluno e por empresa.

## O que isso não é

- Não é LMS de compliance interno. Colaborador Aquafast usa o Portal v2.
- Não é comunidade nem rede social. Perguntas ficam presas à aula.
- Não é plataforma de venda de curso. Tudo é gratuito para quem tem acesso.

## Público

| Perfil | Contexto de uso |
|---|---|
| Distribuidor | Equipe de loja e entrega. Celular, conexão irregular, pouco tempo. |
| Representante comercial | Precisa de argumentário e ficha técnica antes da visita. |
| Cliente corporativo | Consulta pontual: como armazenar, como devolver vasilhame. |

Isso define três coisas de projeto: **mobile importa mais que desktop**, a aula
precisa ser **consultável fora de ordem**, e a busca precisa funcionar.

## Métricas de sucesso da v1

1. Taxa de conclusão por curso acima de 40%.
2. Mediana de tempo entre cadastro e primeira aula concluída abaixo de 48h.
3. Nenhuma pergunta sem resposta há mais de 3 dias úteis.
4. Pelo menos 3 cursos publicados no primeiro trimestre.

## Escopo da v1 (MVP)

Dentro:

- Cadastro público com aprovação obrigatória pelo administrador
- Catálogo, página de curso e inscrição em um clique
- Sala de aula com player, índice, progresso e conclusão automática
- Materiais de apoio com download autenticado
- Perguntas e respostas por aula, com moderação
- Admin completo: cursos, módulos, aulas, alunos, moderação, relatórios

Fora (ver `etapas/99-backlog-pos-mvp.md`):

- Quiz e nota mínima
- Certificado em PDF
- Papel de gestor do parceiro
- Trilha com liberação sequencial ativa
- Aplicativo instalável (PWA)

## Glossário

| Termo | Significa |
|---|---|
| **Curso** | Unidade que o aluno se inscreve. Tem módulos. |
| **Módulo** | Agrupador de aulas dentro de um curso. Sem inscrição própria. |
| **Aula** | Um vídeo + descrição + materiais + perguntas. Unidade de progresso. |
| **Matrícula** | Vínculo aluno ↔ curso. Guarda o progresso. |
| **Amostra** | Aula marcada como gratuita, visível sem login na página do curso. |
| **Organização** | Empresa do aluno (distribuidora, representação, cliente). |
