<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comentarios', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('aula_id')->constrained('aulas')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('comentario_pai_id')->nullable()->constrained('comentarios')->cascadeOnDelete();
            $table->text('corpo');
            $table->string('situacao', 20)->default('aprovado')->comment('pendente|aprovado|oculto');
            $table->boolean('e_resposta')->default(false);
            $table->boolean('fixado')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['aula_id', 'situacao', 'created_at']);
            $table->index('usuario_id');
            $table->index('comentario_pai_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};
