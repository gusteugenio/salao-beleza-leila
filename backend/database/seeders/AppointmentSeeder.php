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

    $scheduledAt = Carbon::now()->setTime(9, 30);

    $appt1 = Appointment::create([
      'user_id' => $cliente1->id,
      'scheduled_at' => $scheduledAt,
      'status' => 'Pendente',
    ]);

    // Calcula horários sequenciais dos serviços
    $corteStart = $scheduledAt->copy();
    $corteEnd = $corteStart->copy()->addMinutes($corte->duration);
    
    $manicureStart = $corteEnd->copy();
    $manicureEnd = $manicureStart->copy()->addMinutes($manicure->duration);

    $appt1->services()->attach([
      $corte->id => [
        'status' => 'Pendente',
        'start_at' => $corteStart->toDateTimeString(),
        'end_at' => $corteEnd->toDateTimeString()
      ],
      $manicure->id => [
        'status' => 'Pendente',
        'start_at' => $manicureStart->toDateTimeString(),
        'end_at' => $manicureEnd->toDateTimeString()
      ]
    ]);
  }
}
