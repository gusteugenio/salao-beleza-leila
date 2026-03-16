<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Service;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
  public function run(): void
  {
    $cliente1 = User::where('email', 'cliente1@salao.com')->first();
    $cliente2 = User::where('email', 'cliente2@salao.com')->first();

    $corte = Service::where('name', 'Corte de Cabelo')->first();
    $manicure = Service::where('name', 'Manicure')->first();
    $pedicure = Service::where('name', 'Pedicure')->first();

    $appt1 = Appointment::create([
      'user_id' => $cliente1->id,
      'scheduled_at' => Carbon::now()->next(Carbon::MONDAY)->setTime(9, 30),
      'status' => 'Pendente',
    ]);
    $appt1->services()->attach([
      $corte->id => ['status' => 'Pendente'],
      $manicure->id => ['status' => 'Pendente']
    ]);
  }
}
