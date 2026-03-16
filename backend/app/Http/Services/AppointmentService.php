<?php

namespace App\Http\Services;

use App\Http\Repositories\AppointmentRepository;
use App\Models\BusinessHour;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AppointmentService
{
  private $appointmentRepository;

  public function __construct(AppointmentRepository $appointmentRepository)
  {
    $this->appointmentRepository = $appointmentRepository;
  }

  /**
   * Valida tentativa de criação
   */
  public function validateCreation(array $data)
  {
    $services = $this->getServices($data['services']);

    $this->validateSchedule($data['scheduled_at'], $services);

    $start = Carbon::parse($data['scheduled_at']);

    $existing = $this->appointmentRepository
      ->findUserAppointmentInWeek($data['user_id'], $start);

    if ($existing) {
      return [
        'status' => 'ASK_UNIFY',
        'existing_appointment' => $existing
      ];
    }

    return [
      'status' => 'OK'
    ];
  }

  /**
   * Cria agendamento com cálculo sequencial de horários
   */
  public function create(array $data)
  {
    $services = $this->getServices($data['services']);

    $appointment = $this->appointmentRepository->create([
      'user_id' => $data['user_id'],
      'scheduled_at' => $data['scheduled_at'],
      'status' => 'Pendente'
    ]);

    // Calcula horários sequenciais para cada serviço
    $serviceTimings = $this->calculateSequentialTimes($data['scheduled_at'], $services);

    $syncData = [];
    foreach ($serviceTimings as $serviceId => $timing) {
      $syncData[$serviceId] = [
        'start_at' => $timing['start_at'],
        'end_at' => $timing['end_at'],
        'status' => 'Pendente'
      ];
    }

    $appointment->services()->sync($syncData);

    return $appointment->load('services');
  }

  /**
   * Adiciona serviços a um agendamento existente
   */
  public function addServices($appointment, $servicesIds, $scheduledAt, $user)
  {
    if ($user->role !== 'admin' && $appointment->user_id !== $user->id) {
      abort(403, 'Você não pode alterar este agendamento.');
    }

    $newServices = $this->getServices($servicesIds);

    $existingServices = $appointment->services;

    $newServicesStartTime = $scheduledAt;

    $newServiceTimings = $this->calculateSequentialTimes($newServicesStartTime, $newServices);

    $syncData = [];

    foreach ($existingServices as $service) {
      $syncData[$service->id] = [
        'start_at' => $service->pivot->start_at,
        'end_at' => $service->pivot->end_at,
        'status' => $service->pivot->status
      ];
    }

    foreach ($newServiceTimings as $serviceId => $timing) {
      $syncData[$serviceId] = [
        'start_at' => $timing['start_at'],
        'end_at' => $timing['end_at'],
        'status' => 'Pendente'
      ];
    }

    $allServices = $existingServices->merge($newServices);
    $this->validateScheduleWithTimings($syncData, $allServices, $appointment->id);

    $appointment->services()->sync($syncData);

    return $appointment->load('services');
  }

  /**
   * Calcula horários sequenciais para cada serviço
   */
  private function calculateSequentialTimes($scheduledAt, Collection $services): array
  {
    $currentTime = Carbon::parse($scheduledAt);
    $serviceTimings = [];

    // Ordena serviços por ID para garantir consistência
    $sortedServices = $services->sortBy('id');

    foreach ($sortedServices as $service) {
      $startTime = $currentTime->copy();
      $endTime = $startTime->copy()->addMinutes($service->duration);

      $serviceTimings[$service->id] = [
        'start_at' => $startTime->toDateTimeString(),
        'end_at' => $endTime->toDateTimeString()
      ];

      $currentTime = $endTime;
    }

    return $serviceTimings;
  }

  /**
   * Obtém serviços válidos
   */
  private function getServices($servicesIds)
  {
    $services = Service::whereIn('id', $servicesIds)->get();

    if ($services->isEmpty()) {
      abort(422, 'Serviços inválidos');
    }

    return $services;
  }

  /**
   * Validação completa de horário
   */
  private function validateSchedule($scheduledAt, $services)
  {
    $start = Carbon::parse($scheduledAt);

    $duration = $services->sum('duration');

    $end = $start->copy()->addMinutes($duration);

    $businessHour = BusinessHour::where(
      'day_of_week',
      $start->dayOfWeek
    )->first();

    if (!$businessHour) {
      abort(422, 'Salão fechado neste dia.');
    }

    $this->validateBusinessHours($start, $end, $businessHour);

    $this->validateLunchTime($start, $end, $businessHour);

    $this->validateConflict($start, $end);
  }

  /**
   * Valida horários com timings já calculados
   */
  private function validateScheduleWithTimings($syncData, $services, $excludeAppointmentId = null)
  {
    foreach ($syncData as $serviceId => $timing) {
      $start = Carbon::parse($timing['start_at']);
      $end = Carbon::parse($timing['end_at']);

      $businessHour = BusinessHour::where(
        'day_of_week',
        $start->dayOfWeek
      )->first();

      if (!$businessHour) {
        abort(422, 'Salão fechado neste dia.');
      }

      $this->validateBusinessHours($start, $end, $businessHour);

      $this->validateLunchTime($start, $end, $businessHour);
    }

    foreach ($syncData as $serviceId => $timing) {
      $start = Carbon::parse($timing['start_at']);
      $end = Carbon::parse($timing['end_at']);

      $appointments = $this->appointmentRepository->findByDate($start->toDateString());

      foreach ($appointments as $appointment) {
        if ($excludeAppointmentId && $appointment->id === $excludeAppointmentId) {
          continue;
        }

        foreach ($appointment->services as $service) {
          // Ignora serviços cancelados
          if ($service->pivot->status === 'Cancelado') {
            continue;
          }

          $existingStart = Carbon::parse($service->pivot->start_at);
          $existingEnd = Carbon::parse($service->pivot->end_at);

          if ($start->lt($existingEnd) && $end->gt($existingStart)) {
            abort(422, 'Horário conflita com outro agendamento.');
          }
        }
      }
    }
  }

  /**
   * Valida horário de funcionamento
   */
  private function validateBusinessHours($start, $end, $businessHour)
  {
    $open = $start->copy()
      ->setTimeFromTimeString($businessHour->open_time);

    $close = $start->copy()
      ->setTimeFromTimeString($businessHour->close_time);

    if ($start->lt($open) || $end->gt($close)) {
      abort(422, 'Horário fora do funcionamento do salão.');
    }
  }

  /**
   * Valida horário de almoço
   */
  private function validateLunchTime($start, $end, $businessHour)
  {
    $lunchStart = $start->copy()
      ->setTimeFromTimeString($businessHour->lunch_start);

    $lunchEnd = $start->copy()
      ->setTimeFromTimeString($businessHour->lunch_end);

    if ($start->lt($lunchEnd) && $end->gt($lunchStart)) {
      abort(422, 'Horário sobrepõe o intervalo de almoço.');
    }
  }

  /**
   * Valida conflito com outros agendamentos
   */
  private function validateConflict($start, $end)
  {
    $appointments = $this->appointmentRepository->findByDate($start->toDateString());

    foreach ($appointments as $appointment) {
      foreach ($appointment->services as $service) {
        // Ignora serviços cancelados
        if ($service->pivot->status === 'Cancelado') {
          continue;
        }

        $existingStart = Carbon::parse($service->pivot->start_at);
        $existingEnd = Carbon::parse($service->pivot->end_at);

        if ($start->lt($existingEnd) && $end->gt($existingStart)) {
          abort(422, 'Horário conflita com outro agendamento.');
        }
      }
    }
  }

  /**
   * Obtém horários disponíveis
   */
  public function getAvailableTimes($date, $durationMinutes, $user)
  {
    $date = Carbon::parse($date);

    $businessHour = BusinessHour::where('day_of_week', $date->dayOfWeek)->first();

    if (!$businessHour) {
      return [];
    }

    $times = [];
    $businessOpen = $date->copy()->setTimeFromTimeString($businessHour->open_time);
    $businessClose = $date->copy()->setTimeFromTimeString($businessHour->close_time);
    $lunchStart = $date->copy()->setTimeFromTimeString($businessHour->lunch_start);
    $lunchEnd = $date->copy()->setTimeFromTimeString($businessHour->lunch_end);

    $currentSlot = $businessOpen->copy();

    while ($currentSlot->copy()->addMinutes($durationMinutes)->lte($businessClose)) {
      $slotStart = $currentSlot->copy();
      $slotEnd = $slotStart->copy()->addMinutes($durationMinutes);

      // Verifica se sobrepõe almoço
      if (!($slotStart->lt($lunchEnd) && $slotEnd->gt($lunchStart))) {
        // Verifica se há conflito com agendamentos existentes
        $conflicts = false;

        $appointments = $this->appointmentRepository->findByDate($date->toDateString());

        foreach ($appointments as $appointment) {
          foreach ($appointment->services as $service) {
            // Ignora serviços cancelados
            if ($service->pivot->status === 'Cancelado') {
              continue;
            }

            $existingStart = Carbon::parse($service->pivot->start_at);
            $existingEnd = Carbon::parse($service->pivot->end_at);

            if ($slotStart->lt($existingEnd) && $slotEnd->gte($existingStart)) {
              $conflicts = true;
              break 2;
            }
          }
        }

        if (!$conflicts) {
          $times[] = $slotStart->format('H:i:s');
        }
      }

      $currentSlot->addMinutes(30);
    }

    return $times;
  }

  /**
   * Lista todos os agendamentos
   */
  public function all($user = null, array $filters = [])
  {
    return $this->appointmentRepository->all($user, $filters);
  }

  /**
   * Busca um agendamento por ID
   */
  public function find($id)
  {
    return $this->appointmentRepository->find($id);
  }

  /**
   * Atualiza um agendamento
   */
  public function update($appointment, $data, $user)
  {
    if ($user->role !== 'admin' && $appointment->user_id !== $user->id) {
      abort(403, 'Você não pode alterar este agendamento.');
    }

    if (isset($data['scheduled_at'])) {
      $newScheduledAt = $data['scheduled_at'];
      
      if ($user->role !== 'admin') {
        $now = Carbon::now();
        $scheduled = Carbon::parse($newScheduledAt);
        $nowStart = $now->copy()->startOfDay();
        $scheduledStart = $scheduled->copy()->startOfDay();
        $daysUntil = $scheduledStart->diffInDays($nowStart, true); // true = valor absoluto
        
        if ($daysUntil < 2) {
          abort(403, 'Agendamentos podem ser alterados apenas com 2 ou mais dias de antecedência.');
        }
      }
      
      $appointment->load('services');
      $this->validateSchedule($newScheduledAt, $appointment->services);
    } else {
      if ($user->role !== 'admin') {
        $now = Carbon::now();
        $scheduled = Carbon::parse($appointment->scheduled_at);
        $nowStart = $now->copy()->startOfDay();
        $scheduledStart = $scheduled->copy()->startOfDay();
        $daysUntil = $scheduledStart->diffInDays($nowStart, true); // true = valor absoluto
        
        if ($daysUntil < 2) {
          abort(403, 'Agendamentos podem ser alterados apenas com 2 ou mais dias de antecedência.');
        }
      }
    }

    return $this->appointmentRepository->update($appointment, $data);
  }

  /**
   * Confirma um agendamento
   */
  public function confirm($appointment)
  {
    return $this->appointmentRepository->update($appointment, [
      'status' => 'Confirmado'
    ]);
  }

  /**
   * Atualiza status de um serviço específico
   */
  public function updateServiceStatus($appointment, $serviceId, $status, $user = null)
  {
    // Validar permissão
    if ($user && $user->role !== 'admin' && $appointment->user_id !== $user->id) {
      abort(403, 'Você não pode alterar este agendamento.');
    }

    $appointment->services()->updateExistingPivot($serviceId, [
      'status' => $status
    ]);

    $appointment = $appointment->load('services');

    if ($status === 'Cancelado') {
      $allServicesCancelled = $appointment->services
        ->every(function ($service) {
          return $service->pivot->status === 'Cancelado';
        });

      // Se todos os serviços estão cancelados, cancela o agendamento
      if ($allServicesCancelled) {
        return $this->cancel($appointment);
      }
    }

    return $appointment;
  }

  /**
   * Remove um serviço específico
   */
  public function removeService($appointment, $serviceId, $user)
  {
    if ($user->role !== 'admin' && $appointment->user_id !== $user->id) {
      abort(403, 'Você não pode alterar este agendamento.');
    }

    $appointment->services()->detach($serviceId);

    $appointment = $appointment->load('services');

    // Se não há mais serviços, cancela o agendamento
    if ($appointment->services()->count() === 0) {
      return $this->cancel($appointment);
    }

    return $appointment;
  }

  /**
   * Cancela um agendamento
   */
  public function cancel($appointment, $user = null)
  {
    // Validar permissão
    if ($user && $user->role !== 'admin' && $appointment->user_id !== $user->id) {
      abort(403, 'Você não pode alterar este agendamento.');
    }

    if ($user && $user->role !== 'admin') {
      $now = Carbon::now();
      $scheduled = Carbon::parse($appointment->scheduled_at);
      $nowStart = $now->copy()->startOfDay();
      $scheduledStart = $scheduled->copy()->startOfDay();
      $daysUntil = $scheduledStart->diffInDays($nowStart, true); // true = valor absoluto
      
      if ($daysUntil < 2) {
        abort(403, 'Agendamentos podem ser cancelados apenas com 2 ou mais dias de antecedência.');
      }
    }

    $appointment->services()->update(['appointment_service.status' => 'Cancelado']);

    return $this->appointmentRepository->update($appointment, [
      'status' => 'Cancelado'
    ]);
  }
}
