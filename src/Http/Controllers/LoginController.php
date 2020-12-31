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
        if ($request->route()->getPrefix() == 'api') {
            $this->deleteToken();
        } else {
            $request->session()->invalidate();

            $request->session()->regenerateToken();

            Auth::guard('web')->logout();
        }

        return response([], 204);
    }

    public function createToken($user)
    {
        return $user->createToken('api')
            ->plainTextToken;
    }

    public function deleteToken()
    {
        $user = auth()->user();

        $user->tokens()
            ->where('id', $user->currentAccessToken()->id)
            ->delete();
    }
}
