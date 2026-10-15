<?php

namespace App\Helpers;

use App\Models\FundTransaction;

class FundHelper
{
    public static function balance()
    {
        $in  = FundTransaction::where('direction', 'in')->sum('amount');
        $out = FundTransaction::where('direction', 'out')->sum('amount');
        return $in - $out;
    }
}
