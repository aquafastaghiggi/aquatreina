<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfiguracaoSeeder extends Seeder
{
    public function run(): void
    {
        $agora = now();

        DB::table('configuracoes')->upsert([
            ['chave' => 'aprovacao_manual', 'valor' => 'true', 'tipo' => 'bool', 'created_at' => $agora, 'updated_at' => $agora],
            ['chave' => 'percentual_conclusao', 'valor' => '90', 'tipo' => 'int', 'created_at' => $agora, 'updated_at' => $agora],
            ['chave' => 'intervalo_ping', 'valor' => '10', 'tipo' => 'int', 'created_at' => $agora, 'updated_at' => $agora],
            ['chave' => 'texto_boas_vindas', 'valor' => 'Bem-vindo ao Aquafast Treina.', 'tipo' => 'string', 'created_at' => $agora, 'updated_at' => $agora],
        ], ['chave'], ['valor', 'tipo', 'updated_at']);
    }
}
