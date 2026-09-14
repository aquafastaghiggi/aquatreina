<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matriculas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('curso_id')->constrained('cursos')->cascadeOnDelete();
            $table->string('origem', 20)->default('aluno')->comment('aluno|admin|importacao');
            $table->string('situacao', 20)->default('ativa')->comment('ativa|concluida|cancelada');
            $table->unsignedTinyInteger('percentual_progresso')->default(0)->comment('cache (D-05)');
            $table->foreignId('ultima_aula_id')->nullable()->constrained('aulas')->nullOnDelete();
            $table->timestamp('matriculado_em')->nullable();
            $table->timestamp('concluido_em')->nullable();
            $table->timestamps();

            $table->unique(['usuario_id', 'curso_id']);
            $table->index(['curso_id', 'situacao']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matriculas');
    }
};
