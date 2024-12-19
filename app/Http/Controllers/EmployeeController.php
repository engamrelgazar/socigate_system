<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return response()->json($employees, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'id_card_file' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
            'national_id' => 'nullable|string|unique:employees,national_id',
            'phone_number' => 'required|string',
            'address' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'hiring_date' => 'required|date',
            'date_of_birth' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $id_card_file = null;
        if ($request->hasFile('id_card_file')) {
            $id_card_file = $request->file('id_card_file')->store('id_card_files', 'public');
        }

        $profile_picture = null;
        if ($request->hasFile('profile_picture')) {
            $profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $employee = Employee::create([
            'user_id' => $request->user_id,
            'id_card_file' => $id_card_file,
            'national_id' => $request->national_id,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'profile_picture' => $profile_picture,
            'hiring_date' => $request->hiring_date,
            'date_of_birth' => $request->date_of_birth,
        ]);

        return response()->json(['message' => 'Employee created successfully', 'employee' => $employee], 201);
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_card_file' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:2048',
            'national_id' => 'nullable|string|unique:employees,national_id,' . $id,
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'hiring_date' => 'nullable|date',
            'date_of_birth' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('profile_picture')) {
            if ($employee->profile_picture && Storage::exists($employee->profile_picture)) {
                Storage::delete($employee->profile_picture);
            }
            $profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
            $employee->profile_picture = $profile_picture;
        }

        if ($request->hasFile('id_card_file')) {
            if ($employee->id_card_file && Storage::exists($employee->id_card_file)) {
                Storage::delete($employee->id_card_file);
            }
            $id_card_file = $request->file('id_card_file')->store('id_card_files', 'public');
            $employee->id_card_file = $id_card_file;
        }

        $employee->phone_number = $request->phone_number;
        $employee->address = $request->address;
        $employee->hiring_date = $request->hiring_date;
        $employee->date_of_birth = $request->date_of_birth;

        $employee->save();

        return response()->json(['message' => 'Employee updated successfully', 'employee' => $employee], 200);
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        if ($employee->profile_picture && Storage::exists($employee->profile_picture)) {
            Storage::delete($employee->profile_picture);
        }

        if ($employee->id_card_file && Storage::exists($employee->id_card_file)) {
            Storage::delete($employee->id_card_file);
        }

        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully'], 200);
    }

    public function show($id)
    {
        $employee = Employee::findOrFail($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return response()->json($employee, 200);
    }
}
