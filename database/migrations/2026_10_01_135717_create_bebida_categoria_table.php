<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bebida_categoria', function (Blueprint $table) {
            $table->foreignId('bebida_id')->constrained()->cascadeOnDelete();
            $table->foreignId('categoria_id')->constrained()->cascadeOnDelete();
            $table->primary(['bebida_id', 'categoria_id']);
        });

        // Migrar datos existentes de la relación one-to-many a many-to-many
        $bebidasConCategoria = DB::table('bebidas')
            ->whereNotNull('categoria_id')
            ->get(['id', 'categoria_id']);

        foreach ($bebidasConCategoria as $bebida) {
            DB::table('bebida_categoria')->insert([
                'bebida_id' => $bebida->id,
                'categoria_id' => $bebida->categoria_id,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bebida_categoria');
    }
};
