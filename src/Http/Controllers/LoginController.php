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

        if ($request->route()->getPrefix() != 'api') {
            $request->session()->regenerate();
        }

        return $user;
    }

    public function destroy(Request $request)
    {
        Auth::guard()->logout();

        if ($request->route()->getPrefix() != 'api') {
            $request->session()->invalidate();

            $request->session()->regenerateToken();
        }

        return response([], 204);
    }
}
