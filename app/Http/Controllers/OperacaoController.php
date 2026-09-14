<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class OperacaoController
{
    public function saude(): JsonResponse
    {
        try {
            DB::select('SELECT 1');

            return response()->json(['versao' => config('app.version'), 'banco' => 'ok']);
        } catch (\Throwable) {
            return response()->json(['versao' => config('app.version'), 'banco' => 'indisponivel'], 503);
        }
    }

    public function modeloImportacao(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            echo "nome,email,telefone,empresa,cargo,organizacao,cursos\n";
            echo "Maria Silva,maria@example.com,11999999999,Aquafast,Analista,Parceiros,curso-basico;curso-avancado\n";
        }, 'modelo-importacao-usuarios.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function baixarExportacao(string $arquivo): BinaryFileResponse
    {
        abort_if($arquivo !== basename($arquivo), 404);
        $caminho = 'exportacoes/'.$arquivo;
        abort_unless(Storage::disk('local')->exists($caminho), 404);

        return response()->download(Storage::disk('local')->path($caminho))->deleteFileAfterSend();
    }
}
