<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function update(ProfileUpdateRequest $request)
    {
        $user = auth()->guard('api')->user();
        if ($request->filled('new_password'))
        {
            if (Hash::check($request->get('old_password'), auth()->guard('api')->user()->getAuthPassword()))
            {
                $user->password = Hash::make($request->get('new_password'));
            }
            else
            {
                throw ValidationException::withMessages([
                    'old_password' => [trans('validation.incorrect_old_password')]
                ]);
            }
        }

        foreach (array_keys($request->rules()) as $key) {
            if (!in_array($key, ['old_password', 'new_password']))
            {
                $user->{$key} = $request->get($key);
            }
        }

        $user->save();

        return response()->json(['data' => $user->refresh()], 200);
    }
}
