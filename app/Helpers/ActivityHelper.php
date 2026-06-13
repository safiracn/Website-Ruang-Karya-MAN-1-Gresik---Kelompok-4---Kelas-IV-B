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

    public static function kodePesanan($id)
    {
        return '#RK' . str_pad($id, 5, '0', STR_PAD_LEFT);
    }

    public static function logPesanan($activity, $pesananId, $description = '')
    {
        $kode = self::kodePesanan($pesananId);

        return self::log(
            $activity,
            "Pesanan {$kode} {$description}"
        );
    }
}