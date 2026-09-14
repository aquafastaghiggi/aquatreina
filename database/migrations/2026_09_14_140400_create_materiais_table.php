<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materiais', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('aula_id')->constrained('aulas')->cascadeOnDelete();
            $table->string('titulo', 180)->comment('nome exibido ao aluno');
            $table->string('disco', 40)->default('materiais');
            $table->string('caminho')->comment('nome aleatorio no disco');
            $table->string('mime', 120)->nullable();
            $table->unsignedBigInteger('tamanho_bytes')->default(0);
            $table->unsignedInteger('total_downloads')->default(0);
            $table->integer('posicao')->default(0);
            $table->timestamps();

            $table->index(['aula_id', 'posicao']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materiais');
    }
};
