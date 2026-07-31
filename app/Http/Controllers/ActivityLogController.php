<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::query()
            ->when($request->user_role, fn($q) => $q->where('user_role', $request->user_role))
            ->when($request->module, fn($q) => $q->where('module', $request->module))
            ->when($request->action, fn($q) => $q->where('action', $request->action))
            ->when($request->search, fn($q) => $q->where(function($sub) use ($request) {
                $sub->where('description', 'like', '%' . $request->search . '%')
                    ->orWhere('user_name', 'like', '%' . $request->search . '%');
            }))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        // For filter dropdowns
        $modules = ActivityLog::distinct()->pluck('module');
        $actions = ActivityLog::distinct()->pluck('action');
        $roles   = ActivityLog::distinct()->pluck('user_role');

        // Changed view path to views/activity-log
        return view('activity-log.index', compact('logs', 'modules', 'actions', 'roles'));
    }
}