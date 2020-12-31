<?php

namespace Nelisys\Rbac\Http\Controllers;

use Nelisys\Rbac\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function store(LoginRequest $request)
    {
        $user = $request->authenticate();

        if ($request->route()->getPrefix() == 'api') {
            $token = $this->createToken($user);
            $scopes = ['*'];
        } else {
            $request->session()->regenerate();
            $token = null;
            $scopes = [];
        }

        return [
            'user' => $user,
            'token' => $token,
            'scopes' => $scopes,
        ];
    }

    public function destroy(Request $request)
    {
        Auth::guard()->logout();

        if ($request->route()->getPrefix() == 'api') {
            //
        } else {
            $request->session()->invalidate();

            $request->session()->regenerateToken();
        }

        return response([], 204);
    }

    public function createToken($user)
    {
        return $user->createToken('api')
            ->plainTextToken;
    }
}
