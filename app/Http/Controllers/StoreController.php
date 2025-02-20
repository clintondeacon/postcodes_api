<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Store a newly created store in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {

        // Validate request data
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:stores,name',
            'lat' => 'required|numeric|between:-90,90',
            'long' => 'required|numeric|between:-180,180',
            'is_open' => 'required|boolean',
            'store_type_id' => 'required|exists:store_types,id',
            'max_delivery_distance' => 'required|numeric|min:0',
        ]);

        $store = Store::updateOrCreate([
            'name' => $validated['name'],
            'lat' => $validated['lat'],
            'long' => $validated['long'],
            'is_open' => $validated['is_open'],
            'store_type_id' => $validated['store_type_id'],
            'max_delivery_distance' => $validated['max_delivery_distance'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Store created successfully!',
            'store' => new StoreResource($store)
        ], 201);
    }

    /**
     * Get all postcodes a store delivers to.
     *
     * @param int $storeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($storeId)
    {
        $store = Store::findOrFail($storeId);

        return response()->json([
            'status' => 'success',
            'store' => new StoreResource($store)
        ]);
    }

}
