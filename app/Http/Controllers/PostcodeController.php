<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostcodeResource;
use App\Http\Resources\StoreCollection;
use App\Http\Resources\StoreResource;
use App\Models\Postcode;
use App\Models\Store;
use Illuminate\Http\Request;

class PostcodeController extends Controller
{
    /**
     * Get all postcodes a store delivers to.
     *
     * @param int $storeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request,$postcodeId)
    {
        $distance = $request->distance?? 10;

        $postcode = Postcode::findOrFail($postcodeId);

        $storesWithinDistance = Store::storesWithinDistance($postcode->lat, $postcode->long, $distance)->get();

        return response()->json([
            'status' => 'success',
            'postcode' => new PostcodeResource($postcode),
            'stores' => new StoreCollection($storesWithinDistance)
        ]);
    }
}
