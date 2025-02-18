<?php

namespace Tests\Unit;

use App\Models\Postcode;
use Tests\TestCase;
use App\Models\Store;
use App\Models\StoreType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful store creation.
     */
    public function test_store_creation_success(): void
    {
        $storeType = StoreType::factory()->create();

        Postcode::updateOrCreate([
            'pcd' => 'AB12 3CD',
            'lat' => 40.7118,
            'long' => -74.0080,
        ]);

        Postcode::create([
            'pcd' => 'AB12 3CF',
            'lat' => 40.7138,
            'long' => -74.0070,
        ]);

        $response = $this->postJson('/stores', [
            'name' => 'Test Store',
            'lat' => 40.7128,
            'long' => -74.0060,
            'is_open' => true,
            'store_type_id' => $storeType->id,
            'max_delivery_distance' => 100,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'store' => [
                    'id', 'name', 'lat', 'long', 'is_open', 'store_type', 'max_delivery_distance','postcodes'
                ]
            ]);

        $this->assertDatabaseHas('stores', [
            'name' => 'Test Store',
            'lat' => 40.7128,
            'long' => -74.0060,
            'is_open' => 1,
            'store_type_id' => $storeType->id,
            'max_delivery_distance' => 100,
        ]);
    }

    /**
     * Test validation errors when creating a store with missing data.
     */
    public function test_store_creation_validation_fails(): void
    {
        $response = $this->postJson('/stores', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name', 'lat', 'long', 'is_open', 'store_type_id', 'max_delivery_distance'
            ]);
    }

    /**
     * Test store creation fails with an invalid store_type_id.
     */
    public function test_store_creation_invalid_store_type_id(): void
    {
        $response = $this->postJson('/stores', [
            'name' => 'Invalid Store',
            'lat' => 40.7128,
            'long' => -74.0060,
            'is_open' => true,
            'store_type_id' => 99999, // Non-existent store type
            'max_delivery_distance' => 10.5,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['store_type_id']);
    }

    /**
     * Test store creation fails when trying to create a store with a duplicate name.
     */
    public function test_store_creation_duplicate_name_fails(): void
    {
        $storeType = StoreType::factory()->create();

        Store::factory()->create([
            'name' => 'Existing Store',
            'lat' => 40.7128,
            'long' => -74.0060,
            'is_open' => true,
            'store_type_id' => $storeType->id,
            'max_delivery_distance' => 10.5,
        ]);

        $response = $this->postJson('/stores', [
            'name' => 'Existing Store',
            'lat' => 41.1234,
            'long' => -75.5678,
            'is_open' => true,
            'store_type_id' => $storeType->id,
            'max_delivery_distance' => 5.0,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['name']);
    }

    /**
     * Test rate limiting on store creation.
     */
    public function test_store_creation_rate_limiting(): void
    {
        $storeType = StoreType::factory()->create();

        for ($i = 0; $i < 6; $i++) {
            $response = $this->postJson('/stores', [
                'name' => 'Store ' . $i,
                'lat' => 40.7128,
                'long' => -74.0060,
                'is_open' => true,
                'store_type_id' => $storeType->id,
                'max_delivery_distance' => 10.5,
            ]);

        }

        // Assert last request fails due to rate limiting
        $response->assertStatus(429)
            ->assertJson([
                'message' => 'Too Many Attempts.',
            ]);
    }
}
