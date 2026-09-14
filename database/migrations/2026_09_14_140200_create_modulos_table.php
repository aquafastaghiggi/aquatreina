<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modulos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('curso_id')->constrained('cursos')->cascadeOnDelete();
            $table->string('titulo', 180);
            $table->text('descricao')->nullable();
            $table->integer('posicao')->default(0);
            $table->timestamps();

            $table->index(['curso_id', 'posicao']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modulos');
    }
};
