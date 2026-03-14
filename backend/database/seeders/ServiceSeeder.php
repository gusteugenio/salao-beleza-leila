<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
  public function run(): void
  {
    Service::create([
      'name' => 'Corte de Cabelo',
      'duration' => 60,
      'price' => 50.00,
    ]);

    Service::create([
      'name' => 'Manicure',
      'duration' => 45,
      'price' => 30.00,
    ]);

    Service::create([
      'name' => 'Pedicure',
      'duration' => 45,
      'price' => 35.00,
    ]);
  }
}
