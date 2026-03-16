<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
  /**
   * Retorna métricas de desempenho semanal
   */
  public function getWeeklyPerformance($date)
  {
    $startOfWeek = Carbon::parse($date)->startOfWeek();
    $endOfWeek = Carbon::parse($date)->endOfWeek();

    // Busca todos os serviços da semana
    $servicesPivot = DB::table('appointment_service')
      ->join('appointments', 'appointments.id', '=', 'appointment_service.appointment_id')
      ->join('services', 'services.id', '=', 'appointment_service.service_id')
      ->whereBetween('appointments.scheduled_at', [$startOfWeek, $endOfWeek])
      ->select('services.price', 'appointment_service.status', 'appointments.status as app_status', 'appointments.id as appointment_id')
      ->get();

    // Conta agendamentos únicos
    $totalAppointments = $servicesPivot->pluck('appointment_id')->unique()->count();

    $totalRevenue = 0;
    $finishedServices = 0;
    $pendingServices = 0;
    $cancellations = 0;

    foreach ($servicesPivot as $row) {
      // Se o agendamento foi cancelado, conta como cancelamento
      if ($row->app_status === 'Cancelado') {
        $cancellations++;
        continue;
      }

      // Se o serviço foi cancelado, conta como cancelamento
      if ($row->status === 'Cancelado') {
        $cancellations++;
        continue;
      }

      // Se o serviço foi finalizado, conta como finalizado e soma receita
      if ($row->status === 'Finalizado' || $row->app_status === 'Finalizado') {
        $finishedServices++;
        $totalRevenue += $row->price;
        continue;
      }

      // Caso contrário, conta como pendente
      $pendingServices++;
    }

    return [
      'start_of_week' => $startOfWeek->toDateString(),
      'end_of_week' => $endOfWeek->toDateString(),
      'total_revenue' => $totalRevenue,
      'finished_services' => $finishedServices,
      'pending_services' => $pendingServices,
      'cancellations' => $cancellations,
      'total_appointments' => $totalAppointments,
    ];
  }
}
