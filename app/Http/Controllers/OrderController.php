<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function getOrderWithDetails($orderNumber)
    {
        return Order::where('order_id', $orderNumber)->with('details')->first();
    }

    public function pickup(Order $order)
    {
        return view('order.pickup', compact('order'));
    }

    public function getMyOrders()
    {
        return view('order.my_orders');
    }

    public function deliveryComplete(Order $order)
    {
        return view('order.delivery_complete', compact('order'));
    }
}
