<?php

namespace App\Http\Repositories;

use App\Models\BusinessHour;

class BusinessHourRepository
{

  /**
   * Lista horários
   */
  public function all()
  {
    return BusinessHour::all();
  }

  /**
   * Busca horário
   */
  public function find($id)
  {
    return BusinessHour::findOrFail($id);
  }

  /**
   * Cria horário
   */
  public function create(array $data)
  {
    return BusinessHour::create($data);
  }

  /**
   * Atualiza horário
   */
  public function update(BusinessHour $hour, array $data)
  {
    $hour->update($data);

    return $hour;
  }
}
