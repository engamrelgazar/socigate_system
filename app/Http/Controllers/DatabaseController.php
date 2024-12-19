<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use App\Models\Role;

class DatabaseController extends Controller
{
    public function migrateAndSeed()
    {
        Artisan::call('migrate');


        Role::create([
            'name' => 'admin',
            'role_desc' => 'Admin role with all privileges',
            'permissions' => json_encode(['all']),
        ]);

        return response()->json(['message' => 'Database migrated and Admin role created successfully.']);
    }
}
