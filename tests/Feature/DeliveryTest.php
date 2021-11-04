<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeliveryTest extends TestCase
{
    public function test_order_pickup_api()
    {
        $response = $this->post('/api/order/1/pickup');

        $response->assertStatus(200);
    }

    public function test_order_packed_api()
    {
        $response = $this->post('/api/order/1/packed');

        $response->assertStatus(200);
    }

    public function test_order_delivery_start_api()
    {
        $response = $this->post('/api/order/1/start_delivery');

        $response->assertStatus(200);
    }
}
