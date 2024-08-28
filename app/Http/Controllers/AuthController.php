<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;

class AuthController extends Controller
{
    public function login(Request $request)
    {   try {
        $user = [
            'id' => 1,
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ];
        $key = env('JWT_SECRET');
        $payload = array_merge($user, [
            'iat' => time(),
            'exp' => time() + 3600, // Token expiration time (1 hour)
        ]);
        $token = JWT::encode($payload, $key, 'HS256');
        return response()->json(['token' => $token]);
    } catch (\Throwable $th) {
        throw $th;
    }
       
    }
}
