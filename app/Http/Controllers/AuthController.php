<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate the request data
        $validatedData = Validator::make($request->all(),[
            'name'=> 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        // Check if validation fails
        if ($validatedData->fails()){
            return response()->json([
                'errors' => $validatedData->errors(),
                'status'=> false,
                'message' => 'Validation failed', 
            ],422);
        }

        try{
            $data= $validatedData->validated();

            // Create the user
            $user= User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // Create a token for the user
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'user'=> new UserResource($user), 
                'status' => true,
                'message' => 'User registered successfully',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ] ,201);

        } catch(\Exception $e){
            // Handle any exceptions that occur during registration
            return response()->json([
                'status' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        };
    }
    public function login(Request $request)
    {
        // Validate the request data
        $validatedData = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);
        // Check if validation fails
        if ($validatedData->fails()) {
            return response()->json([
                'errors' => $validatedData->errors(),
                'status' => false,
                'message' => 'Validation failed',
            ], 422);
        }

        $data = $validatedData->validated();
        $credentials = [
            'email' => $data['email'],
            'password' => $data['password'],
        ];

        try{
            if(!Auth::attempt($credentials)){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }
            $user = User::where('email', $data['email'])->firstOrFail(); 
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => new UserResource($user),
                'status' => true,
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);
        } catch(\Exception $e){
            // Handle any exceptions that occur during login
            return response()->json([
                'status' => false,
                'message' => 'Login failed: ' . $e->getMessage(),
            ], 500);
        }
        
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ], 200);

    }
}
