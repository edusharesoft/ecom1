<?php

namespace App\Support;

use App\Models\DeliveryDistrict;
use App\Models\DeliveryDivision;
use App\Models\DeliveryUpazila;

class DeliveryLocation
{
    public static function validateChain(?int $divisionId, ?int $districtId, ?int $upazilaId): bool
    {
        if (! $divisionId || ! $districtId || ! $upazilaId) {
            return false;
        }

        $district = DeliveryDistrict::query()
            ->whereKey($districtId)
            ->where('division_id', $divisionId)
            ->where('status', 1)
            ->first();

        if (! $district) {
            return false;
        }

        return DeliveryUpazila::query()
            ->whereKey($upazilaId)
            ->where('district_id', $districtId)
            ->where('status', 1)
            ->exists();
    }

    public static function shippingLabel(?int $divisionId, ?int $districtId, ?int $upazilaId): string
    {
        $upazila  = $upazilaId ? DeliveryUpazila::find($upazilaId) : null;
        $district = $districtId ? DeliveryDistrict::find($districtId) : null;
        $division = $divisionId ? DeliveryDivision::find($divisionId) : null;

        $parts = array_filter([
            $upazila?->name,
            $district?->name,
            $division?->name,
        ]);

        return $parts ? implode(', ', $parts) : '';
    }

    public static function chargeForDistrictId(?int $districtId): int
    {
        if (! $districtId) {
            return 0;
        }
        $district = DeliveryDistrict::query()->whereKey($districtId)->where('status', 1)->first();

        return $district ? (int) $district->delivery_charge : 0;
    }
}
