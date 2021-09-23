<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderDetailResource;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;

class OrderDetailController extends Controller
{
    public function getOrderDetails(Order $order)
    {
        $details = OrderDetail::where('order_id', $order->id)->get();

        return OrderDetailResource::collection($details);
    }
}
