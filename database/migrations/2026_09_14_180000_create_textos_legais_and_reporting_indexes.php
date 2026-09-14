<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('textos_legais', function (Blueprint $table): void {
            $table->id();
            $table->string('tipo', 20);
            $table->unsignedInteger('versao');
            $table->longText('conteudo');
            $table->timestamp('publicado_em');
            $table->timestamps();
            $table->unique(['tipo', 'versao']);
            $table->index(['tipo', 'publicado_em']);
        });

        Schema::table('usuarios', function (Blueprint $table): void {
            $table->unsignedInteger('termos_versao_aceita')->nullable()->after('termos_aceitos_em');
            $table->index('empresa');
            $table->index('created_at');
        });
        Schema::table('matriculas', fn (Blueprint $table) => $table->index('matriculado_em'));
    }

    public function down(): void
    {
        Schema::table('matriculas', fn (Blueprint $table) => $table->dropIndex(['matriculado_em']));
        Schema::table('usuarios', function (Blueprint $table): void {
            $table->dropIndex(['empresa']);
            $table->dropIndex(['created_at']);
            $table->dropColumn('termos_versao_aceita');
        });
        Schema::dropIfExists('textos_legais');
    }
};
