<?php

declare(strict_types=1);

use App\Http\Controllers\AmostraController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\DadosPessoaisController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MatriculaController;
use App\Http\Controllers\OperacaoController;
use App\Http\Controllers\ProgressoController;
use App\Http\Controllers\TextoLegalController;
use App\Livewire\Aluno\Catalogo;
use App\Livewire\Aluno\Notificacoes;
use App\Livewire\Aluno\Painel;
use App\Livewire\Aluno\Perfil;
use App\Livewire\Aluno\SalaDeAula;
use App\Livewire\Publico\PaginaCurso;
use App\Livewire\Publico\Vitrine;
use Illuminate\Support\Facades\Route;

Route::get('/', Vitrine::class)->name('vitrine');
Route::get('/cursos/{curso:slug}', PaginaCurso::class)->name('cursos.mostrar');
Route::get('/cursos/{curso:slug}/amostra/{aula:slug}', [AmostraController::class, 'mostrar'])
    ->withoutScopedBindings()
    ->name('cursos.amostra');
Route::get('/termos', [TextoLegalController::class, 'termos'])->name('termos');
Route::get('/privacidade', [TextoLegalController::class, 'privacidade'])->name('privacidade');
Route::get('/saude', [OperacaoController::class, 'saude'])->name('saude');
Route::view('/aguardando-aprovacao', 'conta.aguardando')->middleware(['auth', 'verified'])->name('conta.pendente');
Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/aceitar-termos', [TextoLegalController::class, 'aceite'])->name('termos.aceite');
    Route::post('/aceitar-termos', [TextoLegalController::class, 'aceitar'])->name('termos.aceitar');
});
Route::middleware(['auth', 'verified', 'can:acessar-admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/usuarios/modelo-importacao', [OperacaoController::class, 'modeloImportacao'])->name('usuarios.modelo');
    Route::get('/exportacoes/{arquivo}', [OperacaoController::class, 'baixarExportacao'])->name('exportacoes.baixar');
});

Route::middleware(['auth', 'verified', 'garantir.ativo', 'registrar.acesso'])
    ->prefix('app')
    ->name('app.')
    ->group(function (): void {
        Route::get('/', Painel::class)->name('painel');
        Route::get('/catalogo', Catalogo::class)->name('catalogo');
        Route::post('/catalogo/{curso}/inscrever', [MatriculaController::class, 'inscrever'])->name('inscrever');
        Route::get('/c/{curso:slug}', [CursoController::class, 'entrar'])->name('curso');
        Route::get('/c/{curso:slug}/a/{aula:slug}', SalaDeAula::class)
            ->withoutScopedBindings()
            ->name('aula');
        Route::post('/progresso', [ProgressoController::class, 'registrar'])
            ->middleware('throttle:120,1')
            ->name('progresso');
        Route::get('/materiais/{material}/baixar', [MaterialController::class, 'baixar'])
            ->name('materiais.baixar');
        Route::get('/perfil', Perfil::class)->name('perfil');
        Route::get('/perfil/dados.json', [DadosPessoaisController::class, 'exportar'])->name('dados.exportar');
        Route::get('/notificacoes', Notificacoes::class)->name('notificacoes');
    });
