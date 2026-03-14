<?php

namespace App\Http\Controllers;

use App\Http\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
  private $authService;

  /**
   * Construct da classe.
   */
  public function __construct(AuthService $authService)
  {
    $this->authService = $authService;
  }

  /**
   * Registra novo usuário.
   */
  public function register(Request $request)
  {
    $data = $request->validate([
      'name' => 'required|string',
      'email' => 'required|email|unique:users',
      'password' => 'required|min:6'
    ]);

    return response()->json(
      $this->authService->register($data),
      201
    );
  }

  /**
   * Faz login de usuário.
   */
  public function login(Request $request)
  {
    $data = $request->validate([
      'email' => 'required|email',
      'password' => 'required'
    ]);

    return response()->json(
      $this->authService->login($data)
    );
  }

  /**
   * Faz logout de usuário.
   */
  public function logout(Request $request)
  {
    $this->authService->logout($request->user());

    return response()->json([
      'message' => 'Logout realizado'
    ]);
  }
}
