<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Exception;

use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
 
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        try {
            if(!$token = JWTAuth::attempt($data)){
                return ResponseFormatter::error('Invalid Credentials', 401);

            }
            return ResponseFormatter::success([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 'Authenticated', 200);
            
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    // Ambil data user yang sedang login
    public function me(){
        try {
            $user = Auth::user();
            $user->load(['role', 'position']);

            return ResponseFormatter::success(new UserResource($user), 'User profile retrieved successfully.', 200);

        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function logout(){
        try {
            Auth::logout();
            return ResponseFormatter::success([
                'message' => 'Logout successful.'
            ], 200);

        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function refresh()
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());

            return ResponseFormatter::success([
                'access_token' => $newToken,
                'token_type' => 'Bearer',
            ], 'Token refreshed successfully.', 200);

        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}