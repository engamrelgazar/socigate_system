<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActivityLogController extends Controller
{
    public function index()
    {
        $activityLogs = ActivityLog::with('user')->get();
        return response()->json($activityLogs);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'activity_type' => 'required|in:login,add,edit,delete,other',
            'activity_description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $activityLog = ActivityLog::create([
            'user_id' => $request->user_id,
            'activity_type' => $request->activity_type,
            'activity_description' => $request->activity_description,
        ]);

        return response()->json(['message' => 'Activity log created successfully', 'activity_log' => $activityLog], 201);
    }

    public function update(Request $request, $id)
    {
        $activityLog = ActivityLog::find($id);

        if (!$activityLog) {
            return response()->json(['message' => 'Activity log not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'sometimes|exists:users,id',
            'activity_type' => 'sometimes|in:login,add,edit,delete,other',
            'activity_description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $activityLog->update([
            'user_id' => $request->user_id ?? $activityLog->user_id,
            'activity_type' => $request->activity_type ?? $activityLog->activity_type,
            'activity_description' => $request->activity_description ?? $activityLog->activity_description,
        ]);

        return response()->json(['message' => 'Activity log updated successfully', 'activity_log' => $activityLog], 200);
    }

    public function destroy($id)
    {
        $activityLog = ActivityLog::find($id);

        if (!$activityLog) {
            return response()->json(['message' => 'Activity log not found'], 404);
        }

        $activityLog->delete();

        return response()->json(['message' => 'Activity log deleted successfully'], 200);
    }
}
