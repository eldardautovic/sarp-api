<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    
    use HttpResponses;

    public function login (LoginUserRequest $request) {

        $request->validated($request->all());

        if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return $this->error('', 'Credentials do not match.', 401);
        }

        $user = User::where('email', $request->email)->first();

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API token of '.$user->name)->plainTextToken
        ],
        "Login was successful.");
    }

    public function register (StoreUserRequest $request) {
    
        $request->validated($request->all());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
    
        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API token of '.$user->name)->plainTextToken
        ],
        'User created successfully.');
    }

    public function logout() {
        Auth::user()->currentAccessToken()->delete();

        return $this->success([
            'Message' => "You have successfully been logged out."
        ]);
    }
}
