<?php

namespace App\Http\Controllers;

use App\Http\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  private $dashboardService;

  public function __construct(DashboardService $dashboardService)
  {
    $this->dashboardService = $dashboardService;
  }

  /**
   * Retorna o desempenho semanal
   */
  public function weeklyPerformance(Request $request)
  {
    $date = $request->query('date', now()->toDateString());

    return response()->json(
      $this->dashboardService->getWeeklyPerformance($date)
    );
  }
}
