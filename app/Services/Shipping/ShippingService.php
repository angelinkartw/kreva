<?php

namespace App\Services\Shipping;

use App\Models\Address;
use App\Models\Store;

interface ShippingService
{
    /**
     * @return array<int, array{courier: string, service: string, label: string, cost: int, etd: string}>
     */
    public function options(Store $store, Address $destination, int $weightGram): array;
}
