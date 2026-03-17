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
      'scheduled_at' => 'required|date_format:Y-m-d H:i:s',
      'services' => 'required|array|min:1',
      'services.*' => 'integer|exists:services,id'
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
      'scheduled_at' => 'required|date_format:Y-m-d H:i:s',
      'services' => 'required|array|min:1',
      'services.*' => 'integer|exists:services,id'
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
      'services' => 'required|array|min:1',
      'services.*' => 'integer|exists:services,id',
      'scheduled_at' => 'required|date_format:Y-m-d H:i:s'
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
  public function index(Request $request)
  {
    return response()->json(
      $this->appointmentService->all($request->user(), $request->all())
    );
  }

  /**
   * Listar todos os agendamentos
   */
  public function allAppointments(Request $request)
  {
    if (!$request->user() || $request->user()->role !== 'admin') {
      return response()->json(['error' => 'Acesso não autorizado'], 403);
    }

    return response()->json(
      $this->appointmentService->getAllAppointments($request->all())
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
      'scheduled_at' => 'sometimes|date_format:Y-m-d H:i:s',
      'services' => 'sometimes|array|min:1',
      'services.*' => 'integer|exists:services,id'
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
   * Confirmar agendamento
   */
  public function confirm(Appointment $appointment)
  {
    return response()->json(
      $this->appointmentService->confirm($appointment)
    );
  }

  /**
   * Atualiza status de um serviço específico
   */
  public function updateServiceStatus(Request $request, Appointment $appointment, $serviceId)
  {
    $data = $request->validate([
      'status' => 'required|in:Pendente,Finalizado,Cancelado'
    ]);

    return response()->json(
      $this->appointmentService->updateServiceStatus($appointment, $serviceId, $data['status'], $request->user())
    );
  }

  /**
   * Remove um serviço específico
   */
  public function removeService(Request $request, Appointment $appointment, $serviceId)
  {
    return response()->json(
      $this->appointmentService->removeService($appointment, $serviceId, $request->user())
    );
  }

  /**
   * Cancelar
   */
  public function cancel(Request $request, Appointment $appointment)
  {
    return response()->json(
      $this->appointmentService->cancel($appointment, $request->user())
    );
  }

  /**
   * Obtém horários disponíveis para uma data específica
   */
  public function getAvailableTimes(Request $request)
  {
    $data = $request->validate([
      'date' => 'required|date_format:Y-m-d',
      'duration' => 'required|integer|min:30'
    ]);

    return response()->json(
      $this->appointmentService->getAvailableTimes(
        $data['date'],
        $data['duration'],
        $request->user()
      )
    );
  }
}
