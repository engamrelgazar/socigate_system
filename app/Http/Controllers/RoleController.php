<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles|max:255',
            'role_desc' => 'required|string|max:255',
            'permissions' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role = Role::create([
            'name' => $request->name,
            'role_desc' => $request->role_desc,
            'permissions' => $request->permissions,
        ]);

        return response()->json(['message' => 'Role created successfully', 'role' => $role], 201);
    }
    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|unique:roles|max:255',
            'role_desc' => 'sometimes|string|max:255',
            'permissions' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role->update([
            'name' => $request->name ?? $role->name,
            'role_desc' => $request->role_desc ?? $role->role_desc,
            'permissions' => $request->permissions ?? $role->permissions,
        ]);

        return response()->json(['message' => 'Role updated successfully', 'role' => $role], 200);
    }
    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $role->delete();

        return response()->json(['message' => 'Role deleted successfully'], 200);
    }
}
