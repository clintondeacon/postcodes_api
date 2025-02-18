<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'lat' => $this->lat,
            'long' => $this->long,
            'is_open' => $this->is_open,
            'store_type' => $this->store_type?->name,
            'max_delivery_distance' => $this->max_delivery_distance,
            'postcodes' => new PostcodeCollection($this->postcodes),
        ];
    }
}
