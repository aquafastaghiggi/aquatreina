<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aulas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('modulo_id')->constrained('modulos')->cascadeOnDelete();
            $table->string('titulo', 180);
            $table->string('slug', 200);
            $table->longText('descricao')->nullable();
            $table->string('provedor', 20)->default('youtube')->comment('youtube|bunny|externo');
            $table->string('video_id', 64)->nullable()->comment('no YouTube, 11 caracteres');
            $table->unsignedInteger('duracao_segundos')->default(0);
            $table->boolean('amostra_gratuita')->default(false);
            $table->string('situacao', 20)->default('rascunho')->comment('rascunho|publicada');
            $table->integer('posicao')->default(0);
            $table->timestamp('publicada_em')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['modulo_id', 'slug']);
            $table->index(['modulo_id', 'posicao']);
            $table->index('situacao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aulas');
    }
};
