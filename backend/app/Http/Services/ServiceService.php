<?php

namespace App\Http\Services;

use App\Http\Repositories\ServiceRepository;
use Illuminate\Validation\ValidationException;

class ServiceService
{

  private $repository;

  public function __construct(ServiceRepository $repository)
  {
    $this->repository = $repository;
  }

  /**
   * Retorna todos os serviços.
   */
  public function all()
  {
    return $this->repository->all();
  }

  /**
   * Retorna um serviço pelo ID.
   */
  public function find($id)
  {
    return $this->repository->find($id);
  }

  /**
   * Cria um novo serviço.
   */
  public function create(array $data)
  {
    $this->validate($data);
    return $this->repository->create($data);
  }

  /**
   * Atualiza um serviço existente.
   */
  public function update($id, array $data)
  {
    $this->validate($data, false);
    return $this->repository->update($id, $data);
  }

  /**
   * Deleta um serviço.
   */
  public function delete($id)
  {
    return $this->repository->delete($id);
  }

  /**
   * Valida os dados do serviço.
   */
  private function validate(array $data, bool $isNew = true)
  {
    $rules = [
      'name' => 'required|string|max:255',
      'duration' => 'required|integer|min:1',
      'price' => 'required|numeric|min:0',
    ];

    $validator = \Validator::make($data, $rules);

    if ($validator->fails()) {
      throw ValidationException::withMessages($validator->errors()->toArray());
    }
  }
}
