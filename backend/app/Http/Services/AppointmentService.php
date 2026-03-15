<?php

namespace App\Http\Services;

use App\Http\Repositories\AppointmentRepository;
use App\Models\BusinessHour;
use App\Models\Service;
use Carbon\Carbon;

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
   * Cria agendamento
   */
  public function create(array $data)
  {
    $services = $this->getServices($data['services']);

    $appointment = $this->appointmentRepository->create([
      'user_id' => $data['user_id'],
      'scheduled_at' => $data['scheduled_at'],
      'status' => 'Agendado'
    ]);

    $appointment->services()->sync(
      $services->pluck('id')
    );

    return $appointment->load('services');
  }

  /**
   * Adiciona serviços
   */
  public function addServices($appointment, $servicesIds, $scheduledAt, $user)
  {
    if ($appointment->user_id !== $user->id) {
      abort(403, 'Você não pode alterar este agendamento.');
    }

    $services = $this->getServices($servicesIds);

    $this->validateSchedule($scheduledAt, $services);

    $appointment->services()->syncWithoutDetaching(
      $services->pluck('id')
    );

    return $appointment->load('services');
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
      abort(422, 'Horário conflita com o horário de almoço.');
    }
  }

  /**
   * Valida conflito com outros agendamentos
   */
  private function validateConflict($start, $end)
  {
    $appointments = $this->appointmentRepository
      ->findByDate($start->toDateString());

    foreach ($appointments as $appointment) {

      $existingStart = Carbon::parse($appointment->scheduled_at);

      $duration = $appointment->services->sum('duration');

      $existingEnd = $existingStart
        ->copy()
        ->addMinutes($duration);

      if ($start->lt($existingEnd) && $end->gt($existingStart)) {
        abort(422, 'Horário já possui agendamento.');
      }
    }
  }

  /**
   * Lista todos
   */
  public function all()
  {
    return $this->appointmentRepository->all();
  }

  /**
   * Busca um
   */
  public function find($id)
  {
    return $this->appointmentRepository->find($id);
  }

  /**
   * Atualiza agendamento
   */
  public function update($appointment, $data, $user)
  {
    $scheduled = Carbon::parse($appointment->scheduled_at);

    if ($user->role !== 'admin') {

      if ($appointment->user_id !== $user->id) {
        abort(403, 'Você não pode alterar este agendamento.');
      }

      if (Carbon::now()->diffInDays($scheduled) < 2) {
        abort(403, 'Alteração permitida somente até 2 dias antes.');
      }
    }

    return $this->appointmentRepository
      ->update($appointment, $data);
  }

  /**
   * Cancela agendamento
   */
  public function cancel($appointment)
  {
    $appointment->update([
      'status' => 'Cancelado'
    ]);

    return $appointment;
  }
}
