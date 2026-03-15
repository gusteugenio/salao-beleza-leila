<?php

namespace App\Http\Controllers;

use App\Http\Services\BusinessHourService;
use App\Models\BusinessHour;
use Illuminate\Http\Request;

class BusinessHourController extends Controller
{

  private $service;

  public function __construct(BusinessHourService $service)
  {
    $this->service = $service;
  }

  /**
   * Lista horários
   */
  public function index()
  {
    return response()->json(
      $this->service->all()
    );
  }

  /**
   * Cria horário
   */
  public function store(Request $request)
  {
    return response()->json(
      $this->service->create($request->all()),
      201
    );
  }

  /**
   * Atualiza horário
   */
  public function update(Request $request, BusinessHour $businessHour)
  {
    return response()->json(
      $this->service->update(
        $businessHour,
        $request->all()
      )
    );
  }
}
