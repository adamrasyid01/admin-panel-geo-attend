<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return ResponseFormatter::error('Invalid email or password.');
            }

            // Generate a token for the user
            $token = $user->createToken('auth_token')->plainTextToken;

            return ResponseFormatter::success([
                'message' => 'Login successful.',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => new UserResource($user)
            ]);
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage());
        }
    }

    public function logout(Request $request){
        try {
            $request->user()->currentAccessToken()->delete;
            return ResponseFormatter::success([
                'message' => 'Logout successful.'
            ]);
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage());
        }
    }
}
