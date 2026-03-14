<?php

namespace App\Http\Services;

use App\Http\Repositories\UserRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
  private $userRepository;

  /**
   * Construct da classe.
   */
  public function __construct(UserRepository $userRepository)
  {
    $this->userRepository = $userRepository;
  }

  /**
   * Registra novo usuário.
   */
  public function register(array $data)
  {
    $data['password'] = Hash::make($data['password']);

    return $this->userRepository->create($data);
  }

  /**
   * Faz login de usuário.
   */
  public function login(array $data)
  {
    $user = $this->userRepository->findByEmail($data['email']);

    if (!$user || !Hash::check($data['password'], $user->password)) {
      throw new AuthenticationException('Credenciais inválidas.');
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return [
      'user' => $user,
      'token' => $token
    ];
  }

  /**
   * Faz logout de usuário.
   */
  public function logout($user)
  {
    $user->tokens()->delete();
  }
}
