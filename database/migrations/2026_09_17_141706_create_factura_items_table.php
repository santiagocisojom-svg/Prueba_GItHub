<?php

use App\Models\Bebida;
use App\Models\Factura;
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
        Schema::create('factura_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Factura::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Bebida::class)->constrained()->cascadeOnDelete();
            $table->unsignedInteger('cantidad');
            $table->decimal('precio_unitario', 8, 2)->unsigned(); // Congela el precio al momento de la venta
            $table->decimal('subtotal', 10, 2)->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factura_items');
    }
};
