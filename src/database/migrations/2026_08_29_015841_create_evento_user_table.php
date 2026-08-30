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
        Schema::create('evento_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos', 'id')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();

            $table->string('codigo', 12)->unique();
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada'])->default('confirmada');
            $table->boolean('asistio')->default(false);

            $table->timestamps();
            $table->unique(['evento_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evento_user');
    }
};
