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
    // Busca todos os clientes (excluindo o admin)
    $clients = User::where('role', '!=', 'admin')->inRandomOrder()->get();

    // Busca todos os serviços
    $services = Service::all()->keyBy('name');
    
    // Agendamentos históricos (passados)
    $this->createHistoricalAppointments($clients, $services);
    
    // Agendamentos atuais (hoje)
    $this->createCurrentAppointments($clients, $services);
    
    // Agendamentos futuros (próximos dias)
    $this->createFutureAppointments($clients, $services);
  }

  private function createHistoricalAppointments($clients, $services)
  {
    // 3 dias atrás
    $baseDate = Carbon::now()->subDays(3);
    
    $appt1 = Appointment::create([
      'user_id' => $clients[0]->id,
      'scheduled_at' => $baseDate->copy()->setTime(10, 0),
      'status' => 'Confirmado',
    ]);
    $this->attachServices($appt1, [
      $services['Corte de Cabelo'],
      $services['Coloração']
    ], $baseDate->copy()->setTime(10, 0), 'Finalizado');

    // 2 dias atrás
    $baseDate = Carbon::now()->subDays(2);
    
    $appt2 = Appointment::create([
      'user_id' => $clients[1]->id,
      'scheduled_at' => $baseDate->copy()->setTime(14, 0),
      'status' => 'Confirmado',
    ]);
    $this->attachServices($appt2, [
      $services['Manicure'],
      $services['Pedicure']
    ], $baseDate->copy()->setTime(14, 0), 'Finalizado');

    // 1 dia atrás
    $baseDate = Carbon::now()->subDays(1);
    
    $appt3 = Appointment::create([
      'user_id' => $clients[2]->id,
      'scheduled_at' => $baseDate->copy()->setTime(9, 0),
      'status' => 'Cancelado',
    ]);
    $this->attachServices($appt3, [
      $services['Limpeza de Pele']
    ], $baseDate->copy()->setTime(9, 0), 'Cancelado');

    $appt4 = Appointment::create([
      'user_id' => $clients[3]->id,
      'scheduled_at' => $baseDate->copy()->setTime(16, 30),
      'status' => 'Confirmado',
    ]);
    $this->attachServices($appt4, [
      $services['Design de Sobrancelha'],
      $services['Massagem Facial']
    ], $baseDate->copy()->setTime(16, 30), 'Finalizado');
  }

  private function createCurrentAppointments($clients, $services)
  {
    $today = Carbon::now();

    // Morning appointment
    $appt1 = Appointment::create([
      'user_id' => $clients[0]->id,
      'scheduled_at' => $today->copy()->setTime(9, 30),
      'status' => 'Confirmado',
    ]);
    $this->attachServices($appt1, [
      $services['Corte de Cabelo'],
      $services['Manicure']
    ], $today->copy()->setTime(9, 30), 'Pendente');

    // Midday appointment
    $appt2 = Appointment::create([
      'user_id' => $clients[1]->id,
      'scheduled_at' => $today->copy()->setTime(11, 0),
      'status' => 'Pendente',
    ]);
    $this->attachServices($appt2, [
      $services['Hidratação Profunda']
    ], $today->copy()->setTime(11, 0), 'Pendente');

    // Afternoon appointment
    $appt3 = Appointment::create([
      'user_id' => $clients[2]->id,
      'scheduled_at' => $today->copy()->setTime(14, 0),
      'status' => 'Confirmado',
    ]);
    $this->attachServices($appt3, [
      $services['Escova Progressiva']
    ], $today->copy()->setTime(14, 0), 'Pendente');

    // Late afternoon appointment
    $appt4 = Appointment::create([
      'user_id' => $clients[4]->id,
      'scheduled_at' => $today->copy()->setTime(16, 0),
      'status' => 'Pendente',
    ]);
    $this->attachServices($appt4, [
      $services['Pedicure'],
      $services['Design de Sobrancelha']
    ], $today->copy()->setTime(16, 0), 'Pendente');
  }

  private function createFutureAppointments($clients, $services)
  {
    $tomorrow = Carbon::tomorrow();
    
    // Tomorrow morning
    $appt1 = Appointment::create([
      'user_id' => $clients[1]->id,
      'scheduled_at' => $tomorrow->copy()->setTime(10, 0),
      'status' => 'Confirmado',
    ]);
    $this->attachServices($appt1, [
      $services['Limpeza de Pele'],
      $services['Massagem Facial']
    ], $tomorrow->copy()->setTime(10, 0), 'Pendente');

    // Tomorrow afternoon
    $appt2 = Appointment::create([
      'user_id' => $clients[2]->id,
      'scheduled_at' => $tomorrow->copy()->setTime(15, 0),
      'status' => 'Pendente',
    ]);
    $this->attachServices($appt2, [
      $services['Coloração']
    ], $tomorrow->copy()->setTime(15, 0), 'Pendente');

    // Day after tomorrow
    $dayAfter = Carbon::now()->addDays(2);
    
    $appt3 = Appointment::create([
      'user_id' => $clients[3]->id,
      'scheduled_at' => $dayAfter->copy()->setTime(9, 0),
      'status' => 'Pendente',
    ]);
    $this->attachServices($appt3, [
      $services['Corte de Cabelo'],
      $services['Manicure']
    ], $dayAfter->copy()->setTime(9, 0), 'Pendente');

    // 3 days from now
    $daysAhead = Carbon::now()->addDays(3);
    
    $appt4 = Appointment::create([
      'user_id' => $clients[4]->id,
      'scheduled_at' => $daysAhead->copy()->setTime(13, 30),
      'status' => 'Pendente',
    ]);
    $this->attachServices($appt4, [
      $services['Manicure'],
      $services['Pedicure'],
      $services['Design de Sobrancelha']
    ], $daysAhead->copy()->setTime(13, 30), 'Pendente');

    $appt5 = Appointment::create([
      'user_id' => $clients[0]->id,
      'scheduled_at' => $daysAhead->copy()->setTime(17, 0),
      'status' => 'Cancelado',
    ]);
    $this->attachServices($appt5, [
      $services['Escova Progressiva'],
      $services['Hidratação Profunda']
    ], $daysAhead->copy()->setTime(17, 0), 'Cancelado');

    // 4 days from now
    $daysAhead = Carbon::now()->addDays(4);
    
    $appt6 = Appointment::create([
      'user_id' => $clients[1]->id,
      'scheduled_at' => $daysAhead->copy()->setTime(11, 0),
      'status' => 'Pendente',
    ]);
    $this->attachServices($appt6, [
      $services['Limpeza de Pele']
    ], $daysAhead->copy()->setTime(11, 0), 'Pendente');

    // 5 days from now (cancelled)
    $daysAhead = Carbon::now()->addDays(5);
    
    $appt7 = Appointment::create([
      'user_id' => $clients[2]->id,
      'scheduled_at' => $daysAhead->copy()->setTime(10, 30),
      'status' => 'Cancelado',
    ]);
    $this->attachServices($appt7, [
      $services['Corte de Cabelo']
    ], $daysAhead->copy()->setTime(10, 30), 'Cancelado');

    // Next week
    $nextWeek = Carbon::now()->addWeek();
    
    $appt8 = Appointment::create([
      'user_id' => $clients[3]->id,
      'scheduled_at' => $nextWeek->copy()->setTime(9, 0),
      'status' => 'Pendente',
    ]);
    $this->attachServices($appt8, [
      $services['Manicure'],
      $services['Massagem Facial']
    ], $nextWeek->copy()->setTime(9, 0), 'Pendente');

    $appt9 = Appointment::create([
      'user_id' => $clients[4]->id,
      'scheduled_at' => $nextWeek->copy()->setTime(14, 0),
      'status' => 'Pendente',
    ]);
    $this->attachServices($appt9, [
      $services['Coloração'],
      $services['Hidratação Profunda']
    ], $nextWeek->copy()->setTime(14, 0), 'Pendente');
  }

  private function attachServices($appointment, $servicesList, $startDateTime, $status)
  {
    $currentTime = $startDateTime->copy();
    $syncData = [];

    foreach ($servicesList as $service) {
      $endTime = $currentTime->copy()->addMinutes($service->duration);
      
      $syncData[$service->id] = [
        'status' => $status,
        'start_at' => $currentTime->toDateTimeString(),
        'end_at' => $endTime->toDateTimeString()
      ];

      $currentTime = $endTime->copy();
    }

    $appointment->services()->attach($syncData);
  }
}

