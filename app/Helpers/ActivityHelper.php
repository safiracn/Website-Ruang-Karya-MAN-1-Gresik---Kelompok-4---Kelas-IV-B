<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityHelper
{
    public static function log($activity, $description = null)
    {
        $user = Auth::user();

        ActivityLog::create([
            'user_id' => $user?->id_user,
            'role' => $user?->role ?? 'Guest',
            'activity' => $activity,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}