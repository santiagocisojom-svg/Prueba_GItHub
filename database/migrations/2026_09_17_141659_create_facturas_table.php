<?php

use App\Models\User;
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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete(); // Emisor/Mesero
            $table->string('numero_factura')->unique(); // Ej: FAC-2026-0001
            $table->string('cliente_nombre');
            $table->string('cliente_identificacion')->nullable();
            $table->decimal('subtotal', 10, 2)->unsigned();
            $table->decimal('impuesto', 10, 2)->unsigned();
            $table->decimal('total', 10, 2)->unsigned();
            $table->string('estado')->default('pagada'); // pagada, anulada
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
