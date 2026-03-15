<?php

namespace App\Http\Services;

use App\Http\Repositories\BusinessHourRepository;
use Illuminate\Support\Facades\Validator;

class BusinessHourService
{

  private $repository;

  public function __construct(BusinessHourRepository $repository)
  {
    $this->repository = $repository;
  }

  /**
   * Lista horários
   */
  public function all()
  {
    return $this->repository->all();
  }

  /**
   * Busca horário
   */
  public function find($id)
  {
    return $this->repository->find($id);
  }

  /**
   * Cria horário
   */
  public function create(array $data)
  {
    $validator = Validator::make($data, [
      'day_of_week' => 'required|integer|min:0|max:6',
      'open_time' => 'required',
      'close_time' => 'required',
      'lunch_start' => 'required',
      'lunch_end' => 'required'
    ]);

    if ($validator->fails()) {
      abort(422, $validator->errors()->first());
    }

    return $this->repository->create($data);
  }

  /**
   * Atualiza horário
   */
  public function update($hour, array $data)
  {

    $validator = Validator::make($data, [
      'open_time' => 'sometimes',
      'close_time' => 'sometimes',
      'lunch_start' => 'sometimes',
      'lunch_end' => 'sometimes'
    ]);

    if ($validator->fails()) {
      abort(422, $validator->errors()->first());
    }

    return $this->repository->update($hour, $data);
  }
}
