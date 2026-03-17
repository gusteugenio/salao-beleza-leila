<?php

namespace App\Http\Repositories;

use App\Models\Appointment;
use Carbon\Carbon;

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
    if (isset($data['scheduled_at']) && $data['scheduled_at'] !== $appointment->scheduled_at) {
      $appointment->load('services');
      $services = $appointment->services;
      
      // Recalcula os horários sequenciais a partir da nova data
      $currentTime = Carbon::parse($data['scheduled_at']);
      $syncData = [];
      
      foreach ($services->sortBy('id') as $service) {
        $endTime = $currentTime->copy()->addMinutes($service->duration);
        
        $syncData[$service->id] = [
          'start_at' => $currentTime->format('Y-m-d H:i:s'),
          'end_at' => $endTime->format('Y-m-d H:i:s'),
          'status' => $service->pivot->status
        ];
        
        $currentTime = $endTime;
      }
      
      $appointment->update(['scheduled_at' => $data['scheduled_at']]);
      $appointment->services()->sync($syncData);
    } else {
      $appointment->update($data);
    }

    return $appointment->load('services');
  }

  /**
   * Remove
   */
  public function delete(Appointment $appointment)
  {
    return $appointment->delete();
  }

  
  /**
   * Obtém todos os agendamentos com filtros (admin)
   */
  public function getAllAppointments(array $filters = [])
  {
    $query = Appointment::with(['services', 'user']);

    $query->orderBy('scheduled_at', 'asc');

    if (!empty($filters['status']) && $filters['status'] !== 'Todos') {
      $query->where('status', $filters['status']);
    }

    if (!empty($filters['start_date'])) {
      $query->whereDate('scheduled_at', '>=', $filters['start_date']);
    }

    if (!empty($filters['end_date'])) {
      $query->whereDate('scheduled_at', '<=', $filters['end_date']);
    }

    if (!empty($filters['search_client'])) {
      $query->whereHas('user', function ($q) use ($filters) {
        $q->where('name', 'like', '%' . $filters['search_client'] . '%');
      });
    }

    return $query->get();
  }
}
