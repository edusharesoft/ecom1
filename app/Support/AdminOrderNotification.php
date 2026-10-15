<?php

namespace App\Support;

class AdminOrderNotification
{
    public const SESSION_SHOW_POPUP = 'admin_show_new_orders_popup';

    public static function flagAfterLogin(): void
    {
        session([self::SESSION_SHOW_POPUP => true]);
    }

    public static function shouldShowPopup(): bool
    {
        return (bool) session(self::SESSION_SHOW_POPUP, false);
    }

    public static function dismissPopup(): void
    {
        session()->forget(self::SESSION_SHOW_POPUP);
    }
}
