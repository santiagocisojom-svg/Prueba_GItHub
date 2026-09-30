<?php

use App\Models\Categoria;
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
        Schema::create('bebidas', function (Blueprint $table) {
            /* $table->id(); */
            // Claves foráneas estrictas con cascadeOnDelete[cite: 1]
            $table->foreignIdFor(Categoria::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();

            $table->string('nombre')->unique();
            $table->string('slug')->unique(); // Para URLs limpias (ej: /bebidas/coca-cola)[cite: 1]
            $table->string('tipo'); // Se casteará a Enum en el modelo[cite: 1]
            $table->decimal('precio', 8, 2)->unsigned(); // Evita problemas de precisión decimal[cite: 1]
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bebidas');
    }
};
