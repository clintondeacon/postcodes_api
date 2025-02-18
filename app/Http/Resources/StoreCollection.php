<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class StoreCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($store) {
            return [
                'id' => $store->id,
                'name' => $store->name,
                'lat' => $store->lat,
                'long' => $store->long,
                'is_open' => $store->is_open,
                'store_type' => $store->store_type?->name,
                'max_delivery_distance' => $store->max_delivery_distance,
            ];
        })->toArray();
    }
}
