<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\Role::create([
            'nama' => 'Admin'
        ]);

        \App\Models\Role::create([
            'nama' => 'User'
        ]);

        \App\Models\User::create([
            'nama' => 'admin',
            'no_telp' => '081234567890',
            'tanggal_lahir' => '2000-01-01',
            'password' => bcrypt('admin'),
            'role_id' => 1
        ]);
    }
}
