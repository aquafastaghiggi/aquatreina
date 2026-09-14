<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizacoes', function (Blueprint $table): void {
            $table->id();
            $table->string('nome', 160);
            $table->string('cnpj', 14)->nullable();
            $table->string('tipo', 20)->comment('distribuidor|representante|cliente');
            $table->char('uf', 2)->nullable();
            $table->boolean('ativa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizacoes');
    }
};
