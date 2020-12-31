<?php

namespace Nelisys\Rbac\Http\Controllers;

use Nelisys\Rbac\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    public function store(LoginRequest $request)
    {
        $user = $request->authenticate();

        $request->session()->regenerate();

        return $user;
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response([], 204);
    }
}
