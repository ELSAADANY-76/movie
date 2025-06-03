<?php

namespace App\Http\Controllers;

use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SystemNotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = SystemNotification::where(function ($query) use ($user) {
            $query->whereNull('target_users')
                  ->whereNull('target_roles')
                  ->orWhereJsonContains('target_users', $user->id)
                  ->orWhereJsonContains('target_roles', $user->roles->pluck('name')->toArray());
        })
        ->where('is_active', true)
        ->where(function ($query) {
            $query->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
        })
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json(['notifications' => $notifications]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,error',
            'icon' => 'nullable|string',
            'target_users' => 'nullable|array',
            'target_roles' => 'nullable|array',
            'is_active' => 'boolean',
            'expires_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $notification = SystemNotification::create([
            ...$request->all(),
            'created_by' => Auth::id(),
        ]);

        return response()->json(['notification' => $notification], 201);
    }

    public function show(SystemNotification $notification)
    {
        return response()->json(['notification' => $notification]);
    }

    public function update(Request $request, SystemNotification $notification)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'message' => 'string',
            'type' => 'in:info,success,warning,error',
            'icon' => 'nullable|string',
            'target_users' => 'nullable|array',
            'target_roles' => 'nullable|array',
            'is_active' => 'boolean',
            'expires_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $notification->update($request->all());
        return response()->json(['notification' => $notification]);
    }

    public function destroy(SystemNotification $notification)
    {
        $notification->delete();
        return response()->json(null, 204);
    }

    public function markAsRead(SystemNotification $notification)
    {
        $user = Auth::user();
        $notification->update([
            'read_at' => now(),
        ]);

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        SystemNotification::whereNull('read_at')
            ->where(function ($query) use ($user) {
                $query->whereNull('target_users')
                      ->whereNull('target_roles')
                      ->orWhereJsonContains('target_users', $user->id)
                      ->orWhereJsonContains('target_roles', $user->roles->pluck('name')->toArray());
            })
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read']);
    }
} 