<?php

namespace App\Http\Services;

use App\Http\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
    $validated = validator($data, [
      'name' => 'required|string',
      'email' => 'required|email|unique:users',
      'password' => 'required|min:6'
    ])->validate();

    $validated['password'] = Hash::make($validated['password']);

    return $this->userRepository->create($validated);
  }

  /**
   * Faz login de usuário.
   */
  public function login(array $data)
  {
    $validated = validator($data, [
      'email' => 'required|email',
      'password' => 'required'
    ])->validate();

    $user = $this->userRepository->findByEmail($validated['email']);

    if (!$user || !Hash::check($validated['password'], $user->password)) {
      throw ValidationException::withMessages([
        'email' => ['Credenciais inválidas.']
      ]);
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
