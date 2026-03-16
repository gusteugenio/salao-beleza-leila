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

    $servicesPivot = DB::table('appointment_service')
      ->join('appointments', 'appointments.id', '=', 'appointment_service.appointment_id')
      ->join('services', 'services.id', '=', 'appointment_service.service_id')
      ->whereBetween('appointments.scheduled_at', [$startOfWeek, $endOfWeek])
      ->select('services.price', 'appointment_service.status', 'appointments.status as app_status')
      ->get();

    $totalRevenue = 0;
    $finishedServices = 0;
    $cancellations = 0;

    foreach ($servicesPivot as $row) {
      if ($row->app_status === 'Cancelado' || $row->status === 'Cancelado') {
        $cancellations++;
        continue;
      }

      if ($row->status === 'Finalizado') {
        $finishedServices++;
        $totalRevenue += $row->price;
      }
    }

    return [
      'start_of_week' => $startOfWeek->toDateString(),
      'end_of_week' => $endOfWeek->toDateString(),
      'total_revenue' => $totalRevenue,
      'finished_services' => $finishedServices,
      'cancellations' => $cancellations,
    ];
  }
}
