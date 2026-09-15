# Mapa de rotas

Nomes em português, ponto separando o grupo. Copiável para `routes/web.php`.

## Pública

```php
Route::get('/', Vitrine::class)->name('vitrine');
Route::get('/cursos/{curso:slug}', PaginaCurso::class)->name('cursos.mostrar');
Route::get('/cursos/{curso:slug}/amostra/{aula:slug}', [AmostraController::class, 'mostrar'])
     ->name('cursos.amostra');
Route::view('/termos', 'publico.termos')->name('termos');
Route::view('/privacidade', 'publico.privacidade')->name('privacidade');
```

## Autenticação (Fortify, com rotas renomeadas)

| Método | URI | Nome |
|---|---|---|
| GET/POST | `/entrar` | `login` |
| POST | `/sair` | `logout` |
| GET/POST | `/cadastrar` | `register` |
| GET/POST | `/senha/esqueci` | `password.request` |
| GET/POST | `/senha/redefinir/{token}` | `password.reset` |
| GET | `/email/verificar/{id}/{hash}` | `verification.verify` |
| POST | `/email/reenviar` | `verification.send` |
| GET | `/aguardando-aprovacao` | `conta.pendente` |

Os nomes `login`, `register`, `password.*` e `verification.*` são **do
framework** e não mudam — só as URIs ficam em português. Middleware e
redirecionamentos do Laravel dependem desses nomes.

## Aluno

```php
Route::middleware(['auth', 'verified', 'garantir.ativo', 'registrar.acesso'])
    ->prefix('app')->name('app.')->group(function () {

    Route::get('/', Painel::class)->name('painel');
    Route::get('/catalogo', Catalogo::class)->name('catalogo');
    Route::post('/catalogo/{curso}/inscrever', [MatriculaController::class, 'inscrever'])
         ->name('inscrever');

    Route::get('/c/{curso:slug}', [CursoController::class, 'entrar'])->name('curso');
    Route::get('/c/{curso:slug}/a/{aula:slug}', SalaDeAula::class)->name('aula');
    Route::post('/c/{curso}/a/{aula}/concluir', [ProgressoController::class, 'concluir'])
         ->name('aula.concluir');

    Route::post('/progresso', [ProgressoController::class, 'registrar'])
         ->middleware('throttle:120,1')->name('progresso');

    Route::get('/materiais/{material}/baixar', [MaterialController::class, 'baixar'])
         ->name('material.baixar');

    Route::post('/a/{aula}/comentarios', [ComentarioController::class, 'criar'])
         ->middleware('throttle:10,1')->name('comentarios.criar');

    Route::get('/perfil', Perfil::class)->name('perfil');
    Route::get('/notificacoes', Notificacoes::class)->name('notificacoes');
    Route::get('/meus-dados/exportar', [PerfilController::class, 'exportar'])
         ->name('perfil.exportar');
});
```

## Admin

Gerenciadas pelo Filament em `/admin`, protegidas pelo Gate `acessar-admin`.
Rota customizada fora do padrão do Resource:

```
/admin/cursos/{registro}/curriculo   → ConstrutorCurriculo
```

## Saúde

```php
Route::get('/saude', [SaudeController::class, 'verificar'])->name('saude');
```

Retorna 200 com versão da aplicação e status do banco. Sem autenticação, sem
dado sensível.

## Resumo dos throttles

| Rota | Limite | Motivo |
|---|---|---|
| `/entrar` | 5/min por e-mail+IP | força bruta |
| `/cadastrar` | 5/h por IP | cadastro em massa |
| `app.progresso` | 120/min | ping de 10s dá 6/min; sobra margem |
| `app.comentarios.criar` | 10/min | RN-06 |
| `/email/reenviar` | 3/h | abuso de e-mail |
