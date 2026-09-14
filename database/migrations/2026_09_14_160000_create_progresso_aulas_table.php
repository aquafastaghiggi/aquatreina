<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progresso_aulas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('matricula_id')->constrained('matriculas')->cascadeOnDelete();
            $table->foreignId('aula_id')->constrained('aulas')->cascadeOnDelete();
            $table->unsignedInteger('segundos_assistidos')->default(0);
            $table->unsignedInteger('posicao_maxima')->default(0);
            $table->timestamp('primeira_visualizacao_em')->nullable();
            $table->timestamp('concluido_em')->nullable()->index();
            $table->timestamps();

            $table->unique(['matricula_id', 'aula_id']);
            $table->index('aula_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progresso_aulas');
    }
};
