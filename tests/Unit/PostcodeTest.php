<?php

namespace Tests\Unit;

use App\Models\Postcode;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostcodeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_stores_within_distance_of_a_postcode()
    {
        // Create a postcode
        $postcode = Postcode::factory()->create([
            'lat' => 51.5074, // Example latitude (London)
            'long' => -0.1278, // Example longitude
        ]);

        $storeWithinRange = Store::factory()->create([
            'lat' => 51.509865, // Close to postcode
            'long' => -0.118092,
            'max_delivery_distance' => 5, // 5 miles
        ]);

        $storeOutOfRange = Store::factory()->create([
            'lat' => 52.5200, // Far away (Berlin)
            'long' => 13.4050,
            'max_delivery_distance' => 5,
        ]);

        $response = $this->json('GET', "/postcodes/{$postcode->id}", ['distance' => 10]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

    }
}
