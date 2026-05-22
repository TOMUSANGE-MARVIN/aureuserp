<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Create a notification for a user.
     */
    public static function notify(
        int    $userId,
        string $title,
        string $body   = '',
        string $url    = '',
        string $type   = 'system',
        string $icon   = 'bell'
    ): void {
        DB::table('erp_notifications')->insert([
            'user_id'    => $userId,
            'type'       => $type,
            'title'      => $title,
            'body'       => $body,
            'url'        => $url,
            'icon'       => $icon,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Notify all admin users.
     */
    public static function notifyAdmins(
        string $title,
        string $body  = '',
        string $url   = '',
        string $type  = 'system',
        string $icon  = 'bell'
    ): void {
        $admins = DB::table('users')
            ->where(function ($q) {
                $q->where('is_superadmin', 1)
                  ->orWhereExists(function ($sub) {
                      $sub->select(DB::raw(1))
                          ->from('model_has_roles')
                          ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                          ->whereColumn('model_has_roles.model_id', 'users.id')
                          ->where('model_has_roles.model_type', 'App\\Models\\User')
                          ->where('roles.name', 'Admin');
                  });
            })
            ->pluck('id');

        foreach ($admins as $adminId) {
            static::notify($adminId, $title, $body, $url, $type, $icon);
        }
    }

    /**
     * Notify all users in the current org (company).
     */
    public static function notifyAll(
        string $title,
        string $body  = '',
        string $url   = '',
        string $type  = 'system',
        string $icon  = 'bell'
    ): void {
        $users = DB::table('users')->pluck('id');
        foreach ($users as $uid) {
            static::notify($uid, $title, $body, $url, $type, $icon);
        }
    }
}
