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
      'name' => 'Cliente 1',
      'email' => 'cliente1@salao.com',
      'password' => Hash::make('password123'),
    ]);

    User::create([
      'name' => 'Cliente 2',
      'email' => 'cliente2@salao.com',
      'password' => Hash::make('password123'),
    ]);
  }
}
