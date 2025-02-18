<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostcodeCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($postcode) {
            return [
                'pcd' => $postcode->pcd,
                'lat' => $postcode->lat,
                'long' => $postcode->long,
                'distance' => round($postcode->distance, 2),
            ];
        })->toArray();
    }
}
