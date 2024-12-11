<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;


class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid email or password'], Response::HTTP_UNAUTHORIZED);
            }


            $accessToken = Str::random(60);

            
            $user->access_token = $accessToken; 
            $user->save();

           
            return response()->json([
                'message' => 'Login successful',
                'access_token' => $accessToken
            ], Response::HTTP_OK);


        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR); 
        }
    }

    public function logout(Request $request)
    {
        try {

            $token = $request->header('Authorization');

            if (!$token) {
                return response()->json(['error' => 'Authorization token is required'], Response::HTTP_UNAUTHORIZED);
            }
            $token = str_replace('Bearer ', '', $token);

            $user = User::where('access_token', $token)->first();
            
            $user->access_token = '';
            $user->save();

            return response()->json(['message' => 'Logout successful'], Response::HTTP_OK);

        } catch (\Throwable $th) {
           return response()->json(['message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}
