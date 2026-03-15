<?php

namespace App\Http\Controllers;

use App\Http\Services\AppointmentService;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
  private $appointmentService;

  public function __construct(AppointmentService $appointmentService)
  {
    $this->appointmentService = $appointmentService;
  }

  /**
   * Valida criação de agendamento
   */
  public function validateCreation(Request $request)
  {
    $data = $request->validate([
      'scheduled_at' => 'required|date',
      'services' => 'required|array'
    ]);

    $data['user_id'] = $request->user()->id;

    return response()->json(
      $this->appointmentService->validateCreation($data)
    );
  }

  /**
   * Criar agendamento
   */
  public function store(Request $request)
  {
    $data = $request->validate([
      'scheduled_at' => 'required|date',
      'services' => 'required|array'
    ]);

    $data['user_id'] = $request->user()->id;

    return response()->json(
      $this->appointmentService->create($data),
      201
    );
  }

  /**
   * Adiciona serviços a um agendamento existente
   */
  public function addServices(Request $request, Appointment $appointment)
  {
    $data = $request->validate([
      'services' => 'required|array',
      'scheduled_at' => 'required|date'
    ]);

    return response()->json(
      $this->appointmentService->addServices(
        $appointment,
        $data['services'],
        $data['scheduled_at'],
        $request->user()
      )
    );
  }

  /**
   * Listar todos
   */
  public function index()
  {
    return response()->json(
      $this->appointmentService->all()
    );
  }

  /**
   * Mostrar um
   */
  public function show(Appointment $appointment)
  {
    return response()->json(
      $this->appointmentService->find($appointment->id)
    );
  }

  /**
   * Atualizar
   */
  public function update(Request $request, Appointment $appointment)
  {
    $data = $request->validate([
      'scheduled_at' => 'sometimes|date',
      'services' => 'sometimes|array'
    ]);

    return response()->json(
      $this->appointmentService->update(
        $appointment,
        $data,
        $request->user()
      )
    );
  }

  /**
   * Cancelar
   */
  public function cancel(Appointment $appointment)
  {
    return response()->json(
      $this->appointmentService->cancel($appointment)
    );
  }
}
