<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cursos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->foreignId('responsavel_id')->constrained('usuarios')->restrictOnDelete();
            $table->string('titulo', 180);
            $table->string('slug', 200)->unique();
            $table->string('subtitulo')->nullable();
            $table->longText('descricao')->nullable();
            $table->string('capa_caminho')->nullable()->comment('se vazio, usa a thumb da primeira aula');
            $table->string('nivel', 20)->default('basico')->comment('basico|intermediario|avancado');
            $table->string('situacao', 20)->default('rascunho')->comment('rascunho|publicado|arquivado');
            $table->boolean('moderar_comentarios')->default(false);
            $table->boolean('liberacao_sequencial')->default(false)->comment('sempre 0 na v1 (D-06)');
            $table->unsignedInteger('total_aulas')->default(0)->comment('cache');
            $table->unsignedInteger('minutos_estimados')->default(0)->comment('cache');
            $table->integer('posicao')->default(0);
            $table->timestamp('publicado_em')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['situacao', 'publicado_em']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};
