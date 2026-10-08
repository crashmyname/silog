<?php

namespace App\Controllers;

use App\Services\AuthService;
use Bpjs\Framework\Helpers\Auth;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\Response;
use Bpjs\Framework\Helpers\View;
use Middlewares\SessionMiddleware;

class AuthController extends BaseController
{
    // Controller logic here
    public function login(Request $request, AuthService $service)
    {
        $login = $service->login($request->all());
        return Response::json([
            'status' => $login['status'],
            'message' => $login['message'] ?? 'success',
            'data' => $login['data'] ?? null
        ],$login['status']);
    }

    public function me(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return $this->json(['error' => 'Unauthenticated'], 401);
        }

        return $this->json([
            'user' => [
                'id'       => $user->id,
                'name'     => $user->name,
                'username' => $user->username,
                'role'     => $user->role,
                'email'    => $user->email ?? '',
            ],
        ], 200);
    }
    public function logout()
    {
        Auth::logout();
        SessionMiddleware::destroy();
        if(Request::isAjax()){
            return Response::json([
                'status' => 200,
                'message' => 'Berhasil logout'
            ]);
        }
        return redirect('');
    }
}
