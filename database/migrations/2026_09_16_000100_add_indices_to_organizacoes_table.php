<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizacoes', function (Blueprint $table): void {
            $table->unique('cnpj', 'organizacoes_cnpj_unique');
            $table->index('ativa', 'organizacoes_ativa_index');
        });
    }

    public function down(): void
    {
        Schema::table('organizacoes', function (Blueprint $table): void {
            $table->dropUnique('organizacoes_cnpj_unique');
            $table->dropIndex('organizacoes_ativa_index');
        });
    }
};
