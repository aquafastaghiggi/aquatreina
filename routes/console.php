<?php

declare(strict_types=1);

use App\Jobs\ConferirProgressoDiario;
use App\Models\Matricula;
use App\Models\Usuario;
use App\Notifications\ResumoSemanalAdmin;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('treina:conferir-progresso', function (): int {
    ConferirProgressoDiario::dispatchSync();
    $this->info('Progresso conferido.');

    return self::SUCCESS;
})->purpose('Recalcula e confere a conclusão de todas as matrículas');

Artisan::command('treina:resumo-semanal', function (): int {
    $desde = now()->subWeek();
    $dados = [
        'usuarios' => Usuario::query()->where('created_at', '>=', $desde)->count(),
        'matriculas' => Matricula::query()->where('matriculado_em', '>=', $desde)->count(),
        'conclusoes' => Matricula::query()->where('concluido_em', '>=', $desde)->count(),
    ];
    Usuario::role('admin')->each(fn (Usuario $usuario) => $usuario->notify(new ResumoSemanalAdmin($dados)));
    $this->info('Resumo semanal enviado aos administradores.');

    return self::SUCCESS;
})->purpose('Envia o resumo semanal aos administradores');

Schedule::command('treina:conferir-progresso')->dailyAt('02:00')->withoutOverlapping();
Schedule::command('treina:resumo-semanal')->mondays()->at('08:00')->withoutOverlapping();
