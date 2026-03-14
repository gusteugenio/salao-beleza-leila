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
    return response()->json(
      $this->authService->register($request->all()),
      201
    );
  }

  /**
   * Faz login de usuário.
   */
  public function login(Request $request)
  {
    return response()->json(
      $this->authService->login($request->all())
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
