<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('actor')->latest();

        if ($request->filled('actor_id')) {
            $query->where('actor_id', $request->integer('actor_id'));
        }
        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->input('action') . '%');
        }

        $logs = $query->paginate(20)->withQueryString();
        $actors = User::whereHas('activityLogs')->orderBy('name')->get(['id', 'name']);

        return view('admin.activity-logs.index', compact('logs', 'actors'));
    }
}
