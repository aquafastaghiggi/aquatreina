<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::view('/', 'publico.inicio')->name('vitrine');
Route::view('/termos', 'publico.termos')->name('termos');
Route::view('/privacidade', 'publico.privacidade')->name('privacidade');
Route::view('/aguardando-aprovacao', 'conta.aguardando')->middleware(['auth', 'verified'])->name('conta.pendente');

Route::middleware(['auth', 'verified', 'garantir.ativo', 'registrar.acesso'])
    ->prefix('app')
    ->name('app.')
    ->group(function (): void {
        Route::view('/', 'aluno.painel')->name('painel');
    });
