<?php

namespace App\Http\Repositories;

use App\Models\User;

class UserRepository
{

  /**
   * Encontra usuário pelo email.
   */
  public function findByEmail(string $email): ?User
  {
    return User::where('email', $email)->first();
  }

  /**
   * Cria novo usuário.
   */
  public function create(array $data): User
  {
    return User::create($data);
  }
}
