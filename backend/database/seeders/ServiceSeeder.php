<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
  public function run(): void
  {
    // Serviços de Cabelo
    Service::create([
      'name' => 'Corte de Cabelo',
      'duration' => 60,
      'price' => 50.00,
    ]);

    Service::create([
      'name' => 'Coloração',
      'duration' => 90,
      'price' => 80.00,
    ]);

    Service::create([
      'name' => 'Alisamento',
      'duration' => 120,
      'price' => 120.00,
    ]);

    Service::create([
      'name' => 'Escova Progressiva',
      'duration' => 90,
      'price' => 100.00,
    ]);

    Service::create([
      'name' => 'Hidratação Profunda',
      'duration' => 45,
      'price' => 60.00,
    ]);

    Service::create([
      'name' => 'Tratamento Capilar',
      'duration' => 60,
      'price' => 55.00,
    ]);

    // Serviços de Mãos
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

    Service::create([
      'name' => 'Manicure + Pedicure',
      'duration' => 90,
      'price' => 60.00,
    ]);

    Service::create([
      'name' => 'Esmaltação em Gel',
      'duration' => 50,
      'price' => 45.00,
    ]);

    // Serviços de Rosto
    Service::create([
      'name' => 'Limpeza de Pele',
      'duration' => 45,
      'price' => 50.00,
    ]);

    Service::create([
      'name' => 'Massagem Facial',
      'duration' => 60,
      'price' => 70.00,
    ]);

    Service::create([
      'name' => 'Peeling',
      'duration' => 40,
      'price' => 55.00,
    ]);

    // Serviços de Sobrancelha
    Service::create([
      'name' => 'Design de Sobrancelha',
      'duration' => 30,
      'price' => 25.00,
    ]);

    Service::create([
      'name' => 'Sobrancelha com Henna',
      'duration' => 40,
      'price' => 35.00,
    ]);

    // Pacotes Especiais
    Service::create([
      'name' => 'Pacote Noiva',
      'duration' => 180,
      'price' => 250.00,
    ]);

    Service::create([
      'name' => 'Day Spa',
      'duration' => 150,
      'price' => 200.00,
    ]);
  }
}

