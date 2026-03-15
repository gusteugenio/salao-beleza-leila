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
  public function all()
  {
    return Appointment::with(['services', 'user'])->get();
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
    return Appointment::where('user_id', $userId)
      ->whereBetween('scheduled_at', [
        $date->copy()->startOfWeek(),
        $date->copy()->endOfWeek()
      ])
      ->first();
  }

  /**
   * Buscar agendamentos do dia
   */
  public function findByDate($date)
  {
    return Appointment::with('services')
      ->whereDate('scheduled_at', $date)
      ->where('status', 'Agendado')
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
