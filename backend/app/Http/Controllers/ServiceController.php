<?php

namespace App\Http\Controllers;

use App\Http\Services\ServiceService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
  private $service;

  public function __construct(ServiceService $service)
  {
    $this->service = $service;
  }

  /**
   * Retorna todos os serviços.
   */
  public function index()
  {
    return response()->json($this->service->all());
  }

  /**
   * Retorna um serviço pelo ID.
   */
  public function show($id)
  {
    return response()->json($this->service->find($id));
  }

  /**
   * Cria um novo serviço.
   */
  public function store(Request $request)
  {
    $service = $this->service->create($request->all());
    return response()->json($service, 201);
  }

  /**
   * Atualiza um serviço existente.
   */
  public function update(Request $request, $id)
  {
    return response()->json($this->service->update($id, $request->all()));
  }

  /**
   * Deleta um serviço.
   */
  public function destroy($id)
  {
    $this->service->delete($id);
    return response()->json(['message' => 'Serviço deletado']);
  }
}
