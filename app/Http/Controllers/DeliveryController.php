<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function pickup(Order $order)
    {
        $order->delivery->update(['order_picked_at' => now()]);
    }

    public function packed(Order $order)
    {
        $order->delivery->update(['order_packed_at' => now()]);
    }

    public function start(Order $order)
    {
        $order->delivery->update(['started_at' => now()]);
    }
}
