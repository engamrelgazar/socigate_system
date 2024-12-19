<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }
    public function showByApiKey(Request $request)
    {

        $apiKey = $request->header('api_key');


        if (!$apiKey) {
            return response()->json([
                'status_code' => 422,
                'message' => 'API key is missing in headers.',
                'success' => false,
            ], 422);
        }

        $user = User::where('api_key', $apiKey)->first();

        if (!$user) {
            return response()->json([
                'status_code' => 422,
                'message' => 'Invalid API key.',
                'success' => false,
            ], 422);
        }


        $user->load('employee');
        $user->load('role');


        return response()->json([
            'status_code' => 200,
            'message' => 'User retrieved successfully.',
            'success' => true,
            'user' => $user,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|unique:users|min:3|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|string|min:6',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'user_name' => $request->user_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
        ]);
        $user->api_key = Str::random(32);
        $user->save();
        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_name' => 'sometimes|string|unique:users|min:3|max:50',
            'email' => 'sometimes|email|unique:users|max:255',
            'password' => 'sometimes|string|min:6',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update([
            'user_name' => $request->user_name ?? $user->user_name,
            'email' => $request->email ?? $user->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'role_id' => $request->role_id ?? $user->role_id,
        ]);

        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    }
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
