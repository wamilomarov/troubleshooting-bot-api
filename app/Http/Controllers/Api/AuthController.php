<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $request->merge(['password' => Hash::make($request->get('password'))]);

        User::query()->create($request->all());

        return response()->json([], 200);
    }

    public function login(LoginRequest $request)
    {
        $user = User::query()->where('username', Str::lower($request->get('username')))->first();

        if (is_null($user) || !Hash::check($request->get('password'), $user->getAttribute('password'))) {
            throw ValidationException::withMessages([
                'password' => [trans('auth.failed')]
            ]);
        }

        $user->setAttribute('api_token', Str::random(64));
        $user->save();
        $user->makeVisible(['api_token']);

        return response()->json(['data' => $user]);
    }

    public function logout()
    {
        $user = auth()->guard('api')->user();
        $user->api_token = null;
        $user->save();

        return response()->json([], 200);
    }
}
