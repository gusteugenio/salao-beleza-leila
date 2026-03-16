<?php

namespace App\Http\Repositories;

use App\Models\Appointment;

class AppointmentRepository
{

  /**
   * Cria um agendamento
   */
  public function create(array $data)
  {
    return Appointment::create($data);
  }

  /**
   * Lista todos
   */
  public function all($user = null, array $filters = [])
  {
    $query = Appointment::with(['services', 'user']);

    if ($user && $user->role !== 'admin') {
      $query->where('user_id', $user->id);
    }

    if (!empty($filters['start_date'])) {
      $query->whereDate('scheduled_at', '>=', $filters['start_date']);
    }

    if (!empty($filters['end_date'])) {
      $query->whereDate('scheduled_at', '<=', $filters['end_date']);
    }

    return $query->get();
  }

  /**
   * Busca por id
   */
  public function find($id)
  {
    return Appointment::with(['services', 'user'])->findOrFail($id);
  }

  /**
   * Buscar agendamento na mesma semana
   */
  public function findUserAppointmentInWeek($userId, $date)
  {
    $appointments = Appointment::where('user_id', $userId)
      ->where('status', '<>', 'Cancelado')
      ->whereBetween('scheduled_at', [
        $date->copy()->startOfWeek(),
        $date->copy()->endOfWeek()
      ])
      ->get();

    return $appointments->first(function ($appointment) {
      $hasPending = $appointment->services->contains(function ($service) {
        return $service->pivot->status === 'Pendente';
      });

      return $hasPending;
    });
  }

  /**
   * Buscar agendamentos do dia
   */
  public function findByDate($date)
  {
    return Appointment::with('services')
      ->whereDate('scheduled_at', $date)
      ->whereIn('status', ['Pendente', 'Confirmado'])
      ->get();
  }

  /**
   * Atualiza agendamento
   */
  public function update(Appointment $appointment, array $data)
  {
    $appointment->update($data);

    return $appointment;
  }

  /**
   * Remove
   */
  public function delete(Appointment $appointment)
  {
    return $appointment->delete();
  }
}
