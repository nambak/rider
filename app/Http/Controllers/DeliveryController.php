<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Notifications\DeliveryStartNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

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

        Notification::route('slack', config('logging.channels.slack.url'))
            ->notify(new DeliveryStartNotification($order));

    }
}
