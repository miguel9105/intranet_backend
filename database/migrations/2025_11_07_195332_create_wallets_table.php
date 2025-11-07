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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id(); 
            $table->string('type_document', 50); // Ej: Factura, Contrato
            $table->string('reference', 255)->unique(); // Número de factura/referencia
            $table->decimal('debt amount', 10, 2);
            $table->date('expiration_date');
            $table->string('payment status', 50)->default('Pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
