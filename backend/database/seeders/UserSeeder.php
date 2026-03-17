<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  public function run(): void
  {
    // Admin
    User::create([
      'name' => 'Leila Admin',
      'email' => 'admin@salao.com',
      'password' => Hash::make('password123'),
      'role' => 'admin',
    ]);

    // Clientes de exemplo
    User::create([
      'name' => 'Ana Silva',
      'email' => 'ana@email.com',
      'password' => Hash::make('password123'),
    ]);

    User::create([
      'name' => 'Beatriz Costa',
      'email' => 'beatriz@email.com',
      'password' => Hash::make('password123'),
    ]);

    User::create([
      'name' => 'Carolina Santos',
      'email' => 'carolina@email.com',
      'password' => Hash::make('password123'),
    ]);

    User::create([
      'name' => 'Diana Oliveira',
      'email' => 'diana@email.com',
      'password' => Hash::make('password123'),
    ]);

    User::create([
      'name' => 'Eduarda Pereira',
      'email' => 'eduarda@email.com',
      'password' => Hash::make('password123'),
    ]);
  }
}
