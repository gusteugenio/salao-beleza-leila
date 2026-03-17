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

    // Busca os serviços
    $corte = Service::where('name', 'Corte de Cabelo')->first();
    $manicure = Service::where('name', 'Manicure')->first();
    $pedicure = Service::where('name', 'Pedicure')->first();
    $coloracao = Service::where('name', 'Coloração')->first();
    $limpeza = Service::where('name', 'Limpeza de Pele')->first();
    $sobrancelha = Service::where('name', 'Design de Sobrancelha')->first();
    $escova = Service::where('name', 'Escova Progressiva')->first();
    $hidratacao = Service::where('name', 'Hidratação Profunda')->first();
    $massagem = Service::where('name', 'Massagem Facial')->first();

    // Agendamento 1 - Cliente 1 - Hoje às 09:30
    $scheduledAt = Carbon::now()->setTime(9, 30);
    $appt1 = Appointment::create([
      'user_id' => $cliente1->id,
      'scheduled_at' => $scheduledAt,
      'status' => 'Pendente',
    ]);

    $currentTime = $scheduledAt->copy();
    $syncData = [];

    // Corte + Manicure
    $corteEnd = $currentTime->copy()->addMinutes($corte->duration);
    $syncData[$corte->id] = [
      'status' => 'Pendente',
      'start_at' => $currentTime->toDateTimeString(),
      'end_at' => $corteEnd->toDateTimeString()
    ];

    $currentTime = $corteEnd->copy();
    $manicureEnd = $currentTime->copy()->addMinutes($manicure->duration);
    $syncData[$manicure->id] = [
      'status' => 'Pendente',
      'start_at' => $currentTime->toDateTimeString(),
      'end_at' => $manicureEnd->toDateTimeString()
    ];

    $appt1->services()->attach($syncData);

    // Agendamento 2 - Cliente 2 - Hoje às 11:30
    $scheduledAt = Carbon::now()->setTime(11, 30);
    $appt2 = Appointment::create([
      'user_id' => $cliente2->id,
      'scheduled_at' => $scheduledAt,
      'status' => 'Confirmado',
    ]);

    $currentTime = $scheduledAt->copy();
    $syncData = [];

    // Coloração
    $coloracao_end = $currentTime->copy()->addMinutes($coloracao->duration);
    $syncData[$coloracao->id] = [
      'status' => 'Confirmado',
      'start_at' => $currentTime->toDateTimeString(),
      'end_at' => $coloracao_end->toDateTimeString()
    ];

    $appt2->services()->attach($syncData);

    // Agendamento 3 - Cliente 1 - Amanhã às 10:00
    $scheduledAt = Carbon::tomorrow()->setTime(10, 0);
    $appt3 = Appointment::create([
      'user_id' => $cliente1->id,
      'scheduled_at' => $scheduledAt,
      'status' => 'Pendente',
    ]);

    $currentTime = $scheduledAt->copy();
    $syncData = [];

    // Limpeza de Pele + Massagem Facial
    $limpeza_end = $currentTime->copy()->addMinutes($limpeza->duration);
    $syncData[$limpeza->id] = [
      'status' => 'Pendente',
      'start_at' => $currentTime->toDateTimeString(),
      'end_at' => $limpeza_end->toDateTimeString()
    ];

    $currentTime = $limpeza_end->copy();
    $massagem_end = $currentTime->copy()->addMinutes($massagem->duration);
    $syncData[$massagem->id] = [
      'status' => 'Pendente',
      'start_at' => $currentTime->toDateTimeString(),
      'end_at' => $massagem_end->toDateTimeString()
    ];

    $appt3->services()->attach($syncData);

    // Agendamento 4 - Cliente 2 - Amanhã às 14:00
    $scheduledAt = Carbon::tomorrow()->setTime(14, 0);
    $appt4 = Appointment::create([
      'user_id' => $cliente2->id,
      'scheduled_at' => $scheduledAt,
      'status' => 'Pendente',
    ]);

    $currentTime = $scheduledAt->copy();
    $syncData = [];

    // Design de Sobrancelha + Pedicure
    $sobrancelha_end = $currentTime->copy()->addMinutes($sobrancelha->duration);
    $syncData[$sobrancelha->id] = [
      'status' => 'Pendente',
      'start_at' => $currentTime->toDateTimeString(),
      'end_at' => $sobrancelha_end->toDateTimeString()
    ];

    $currentTime = $sobrancelha_end->copy();
    $pedicure_end = $currentTime->copy()->addMinutes($pedicure->duration);
    $syncData[$pedicure->id] = [
      'status' => 'Pendente',
      'start_at' => $currentTime->toDateTimeString(),
      'end_at' => $pedicure_end->toDateTimeString()
    ];

    $appt4->services()->attach($syncData);

    // Agendamento 5 - Cliente 1 - Daqui 2 dias às 09:00
    $scheduledAt = Carbon::now()->addDays(2)->setTime(9, 0);
    $appt5 = Appointment::create([
      'user_id' => $cliente1->id,
      'scheduled_at' => $scheduledAt,
      'status' => 'Cancelado',
    ]);

    $currentTime = $scheduledAt->copy();
    $syncData = [];

    // Escova Progressiva
    $escova_end = $currentTime->copy()->addMinutes($escova->duration);
    $syncData[$escova->id] = [
      'status' => 'Cancelado',
      'start_at' => $currentTime->toDateTimeString(),
      'end_at' => $escova_end->toDateTimeString()
    ];

    $appt5->services()->attach($syncData);

    // Agendamento 6 - Cliente 2 - Daqui 3 dias às 15:00
    $scheduledAt = Carbon::now()->addDays(3)->setTime(15, 0);
    $appt6 = Appointment::create([
      'user_id' => $cliente2->id,
      'scheduled_at' => $scheduledAt,
      'status' => 'Pendente',
    ]);

    $currentTime = $scheduledAt->copy();
    $syncData = [];

    // Hidratação + Manicure
    $hidratacao_end = $currentTime->copy()->addMinutes($hidratacao->duration);
    $syncData[$hidratacao->id] = [
      'status' => 'Pendente',
      'start_at' => $currentTime->toDateTimeString(),
      'end_at' => $hidratacao_end->toDateTimeString()
    ];

    $currentTime = $hidratacao_end->copy();
    $manicure_end = $currentTime->copy()->addMinutes($manicure->duration);
    $syncData[$manicure->id] = [
      'status' => 'Pendente',
      'start_at' => $currentTime->toDateTimeString(),
      'end_at' => $manicure_end->toDateTimeString()
    ];

    $appt6->services()->attach($syncData);
  }
}

