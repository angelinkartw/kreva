<?php

namespace App\Services\Shipping;

use App\Models\Address;
use App\Models\Store;

/** Implementasi sederhana untuk MVP/dev. Ganti dengan agregator (Biteship/RajaOngkir) di produksi. */
class FlatRateShippingService implements ShippingService
{
    public function options(Store $store, Address $destination, int $weightGram): array
    {
        $kg = max(1, (int) ceil($weightGram / 1000));
        $sameProvince = strcasecmp((string) $store->province, $destination->province) === 0;
        $sameCity = $sameProvince && strcasecmp((string) $store->city, $destination->city) === 0;
        $perKg = $sameCity ? 7000 : ($sameProvince ? 10000 : 18000);

        $couriers = $store->enabled_couriers ?: ['jne', 'jnt', 'sicepat'];
        $services = [
            'ekonomi' => ['Ekonomi', 0.8, '4-7 hari'],
            'reguler' => ['Reguler', 1.0, '2-4 hari'],
            'kilat' => ['Kilat', 1.6, '1-2 hari'],
        ];

        $out = [];
        foreach ($couriers as $courier) {
            foreach ($services as $key => [$label, $mult, $etd]) {
                $out[] = [
                    'courier' => $courier,
                    'service' => $key,
                    'label' => strtoupper($courier).' '.$label,
                    'cost' => (int) (round($perKg * $kg * $mult / 100) * 100),
                    'etd' => $etd,
                ];
            }
        }

        return $out;
    }
}
