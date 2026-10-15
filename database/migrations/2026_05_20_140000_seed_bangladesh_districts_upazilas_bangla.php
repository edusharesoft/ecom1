<?php

/**
 * Seeds all districts & upazilas (Bangla names) from database/data/bd_locations_bangla.json.
 *
 * Dataset: bangladesh-location-data (MIT), https://github.com/sohan-99/bangladesh-location-data
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const SORT_ORDER_TO_BBS = [
        10 => '10',
        20 => '20',
        30 => '30',
        40 => '40',
        50 => '45',
        60 => '50',
        70 => '55',
        80 => '60',
    ];

    private const UPSERT_CHUNK = 400;

    public function up(): void
    {
        $path = database_path('data/bd_locations_bangla.json');
        if (! is_file($path)) {
            throw new RuntimeException(
                'Missing database/data/bd_locations_bangla.json — add that file before running this migration.'
            );
        }

        $raw = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        if (
            ! is_array($raw)
            || ! isset($raw['districts_bn'], $raw['upazilas_bn'], $raw['divisions_bn'])
        ) {
            throw new RuntimeException(
                'bd_locations_bangla.json must define divisions_bn, districts_bn, and upazilas_bn.'
            );
        }

        $bbsToDivisionTitle = [];
        foreach ($raw['divisions_bn'] as $row) {
            $bbsToDivisionTitle[(string) $row['value']] = $row['title'];
        }

        $divisions = DB::table('divisions')->orderBy('id')->get();
        if ($divisions->count() !== 8) {
            throw new RuntimeException('Expected exactly 8 rows in divisions (seed from 2026_05_19_100004 first).');
        }

        $districtBbsToId = [];
        $districtTotal = 0;
        $now = now();

        // TRUNCATE causes implicit commits on MySQL and breaks Laravel's transaction(); use deletes + chunked inserts.
        Schema::disableForeignKeyConstraints();

        try {

            DB::table('upazilas')->delete();
            DB::table('districts')->delete();

            foreach ($divisions as $div) {

                $bbs = self::SORT_ORDER_TO_BBS[(int) $div->sort_order]
                    ?? throw new RuntimeException('Unknown divisions.sort_order: '.$div->sort_order);

                DB::table('divisions')->where('id', $div->id)->update([
                    'name'       => $bbsToDivisionTitle[$bbs] ?? throw new RuntimeException('Division BBS '.$bbs.' missing Bengali title in JSON.'),
                    'updated_at' => $now,
                ]);

                $districtRows = $raw['districts_bn'][$bbs] ?? [];
                if ($districtRows === []) {
                    throw new RuntimeException('No districts_bn entry for division BBS code '.$bbs);
                }

                foreach ($districtRows as $sort => $districtRow) {
                    $districtBbs = (string) $districtRow['value'];
                    $districtId = DB::table('districts')->insertGetId([
                        'division_id'      => $div->id,
                        'name'             => $districtRow['title'],
                        'delivery_charge'  => 0,
                        'sort_order'       => (int) $sort,
                        'status'           => 1,
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ]);

                    $districtBbsToId[$districtBbs] = $districtId;
                    $districtTotal++;
                }
            }

            $buffer = [];

            foreach ($raw['upazilas_bn'] as $districtKey => $upazRows) {
                $districtPk = $districtBbsToId[(string) $districtKey] ?? null;
                if ($districtPk === null) {
                    continue;
                }

                foreach ($upazRows as $sort => $upazRow) {
                    $buffer[] = [
                        'district_id'  => $districtPk,
                        'name'         => $upazRow['title'],
                        'sort_order'   => (int) $sort,
                        'status'       => 1,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ];

                    if (count($buffer) >= self::UPSERT_CHUNK) {
                        DB::table('upazilas')->insert($buffer);
                        $buffer = [];
                    }
                }
            }

            if ($buffer !== []) {
                DB::table('upazilas')->insert($buffer);
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        $upazTotal = DB::table('upazilas')->count();
        if ($districtTotal !== 64 || $upazTotal !== 504) {
            throw new RuntimeException(
                'Unexpected seeded row counts: districts='.$districtTotal.' (expect 64), upazilas='.$upazTotal.' (expect 504).'
            );
        }
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('upazilas')->truncate();
        DB::table('districts')->truncate();
        Schema::enableForeignKeyConstraints();

        $english = [
            10 => 'Barishal',
            20 => 'Chattogram',
            30 => 'Dhaka',
            40 => 'Khulna',
            50 => 'Mymensingh',
            60 => 'Rajshahi',
            70 => 'Rangpur',
            80 => 'Sylhet',
        ];

        foreach ($english as $sort => $name) {
            DB::table('divisions')->where('sort_order', $sort)->update(['name' => $name, 'updated_at' => now()]);
        }
    }
};
