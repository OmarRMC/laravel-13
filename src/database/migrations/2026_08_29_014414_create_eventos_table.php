<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo',150);
            $table->string('slug')->unique();
            $table->text('descripcion');
            $table->foreignId('categoria_id')->constrained('categorias', 'id')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();

            $table->dateTime('inicia_el');
            $table->dateTime('termina_el')->nullable();
            $table->string('lugar',150);
            $table->enum('modalidad', ['presencial', 'virtual', 'hibrido'])->default('presencial');
            $table->unsignedInteger('cupo')->default(15);
            $table->decimal('precio', 8,2)->nullable();
            $table->boolean('es_gratuito')->default(true);
            $table->string('afiche')->nullable();
            $table->enum('estado', ['borrador', 'publicado', 'cerrado','cancelado'])->default('borrador');

            $table->timestamps();
            $table->index(['estado', 'inicia_el']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
