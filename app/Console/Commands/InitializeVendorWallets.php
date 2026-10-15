<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vendor;
use App\Models\VendorWallet;

class InitializeVendorWallets extends Command
{
    protected $signature = 'vendor:wallets:init';
    protected $description = 'Initialize wallets for all existing vendors';

    public function handle()
    {
        $vendors = Vendor::all();
        $count = 0;

        foreach ($vendors as $vendor) {
            VendorWallet::firstOrCreate(
                ['vendor_id' => $vendor->id],
                [
                    'balance' => 0,
                    'total_earned' => 0,
                    'total_withdrawn' => 0,
                ]
            );
            $count++;
        }

        $this->info("Successfully initialized wallets for {$count} vendors!");
        return 0;
    }
}
