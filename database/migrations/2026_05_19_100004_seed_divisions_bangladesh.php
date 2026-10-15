<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $names = [
            ['Barishal', 10],
            ['Chattogram', 20],
            ['Dhaka', 30],
            ['Khulna', 40],
            ['Mymensingh', 50],
            ['Rajshahi', 60],
            ['Rangpur', 70],
            ['Sylhet', 80],
        ];
        foreach ($names as [$name, $sort]) {
            DB::table('divisions')->insert([
                'name'       => $name,
                'sort_order' => $sort,
                'status'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        $names = ['Barishal', 'Chattogram', 'Dhaka', 'Khulna', 'Mymensingh', 'Rajshahi', 'Rangpur', 'Sylhet'];
        DB::table('divisions')->whereIn('name', $names)->delete();
    }
};
