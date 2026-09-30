<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Categoria;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuario Administrador (Gestión de bebidas y catálogo)
        User::factory()->create([
            'name'     => 'Administrador',
            'email'    => 'admin@bebidas.com',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);

        // Usuario Mesero/Operador (Pedidos y Facturación)[cite: 1]
        User::factory()->create([
            'name'     => 'Mesero Juan',
            'email'    => 'mesero@bebidas.com',
            'password' => Hash::make('password123'),
            'role'     => 'mesero',
        ]);

        $categorias = ['Refrescos', 'Cervezas', 'Licores', 'Juegos y Aguas', 'Cocteles'];

        foreach ($categorias as $nombre) {
            Categoria::create([
                'nombre' => $nombre,
                'descripcion' => 'Descripción de la categoría ' . $nombre,
            ]);
        }
    }
}
