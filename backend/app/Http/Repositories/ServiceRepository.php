<?php

namespace App\Http\Repositories;

use App\Models\Service;

class ServiceRepository
{

  /**
   * Retorna todos os serviços.
   */
  public function all()
  {
    return Service::all();
  }

  /**
   * Encontra um serviço pelo ID.
   */
  public function find($id)
  {
    return Service::findOrFail($id);
  }

  /**
   * Cria um novo serviço.
   */
  public function create(array $data)
  {
    return Service::create($data);
  }

  /**
   * Atualiza um serviço existente.
   */
  public function update($id, array $data)
  {
    $service = $this->find($id);
    $service->update($data);
    return $service;
  }

  /**
   * Deleta um serviço.
   */
  public function delete($id)
  {
    $service = $this->find($id);
    $service->delete();
    return $service;
  }
}
