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
        Schema::create('help_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Creador del ticket
            $table->string('title_help_table', 255);
            $table->text('description_help_table');
            $table->string('state-help_table', 50)->default('Abierto');
            $table->string('priority', 50)->default('Media');
            
            // Asignado a un usuario (puede ser NULL)
            $table->unsignedBigInteger('asignado_a_user_id')->nullable(); 
            $table->foreign('asignado_a_user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('help_tables');
    }
};
