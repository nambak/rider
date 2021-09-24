<?php

namespace App\Http\Controllers;

use App\Cafe24\Auth;
use App\Models\Order;
use App\Notifications\CompletedOrder;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Notification;

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

    public function getMyOrders(Order $order)
    {
        return view('order.my_orders', compact('order'));
    }

    public function deliveryComplete(Order $order)
    {
        return view('order.delivery_complete', compact('order'));
    }

    public function complete(Request $request, Order $order)
    {
        $imageUrl = $this->uploadImageToS3($request->image, $order);

        Notification::route('slack', config('logging.channels.slack.url'))
            ->notify(new CompletedOrder($order, $imageUrl));
    }

    public function persistShipment(Request $request, Order $order)
    {
        $client = new Client();
        $accessToken = Auth::getAccessToken();

        $client->request('POST', 'https://tenminutesquad.cafe24api.com/api/v2/admin/shipments', [
            'headers'     => [
                'headers' => [
                    'Authorization'        => 'Bearer ' . $accessToken,
                    'Content-Type'         => 'application/json',
                    'X-Cafe24-Api-Version' => '2021-09-01',
                ],
            ],
            'form_params' => [
                'requests' => [
                    [
                        'tracking_no'           => '1',
                        'shipping_company_code' => '0006',
                        'status'                => 'shipping',
                        'order_id'              => $order->order_id,
                    ],
                ],
            ],
        ]);
    }

    private function uploadImageToS3($file, $order)
    {
        $image = Image::make($file);
        $resizedImage = $image->orientate()->resize(1000, null, function ($constraint) {
            $constraint->aspectRatio();
        })->encode('jpg', 100);

        $fileName = $order->order_id . '_' . time() . 'jpg';

        Storage::disk('s3')->put(
            'order_completed_picture/' . $fileName,
            $resizedImage->__toString(),
            'public'
        );

        $resultPath = 'https://s3.ap-northeast-2.amazonaws.com/10min.squad.mfc/order_completed_picture/'
            . $fileName;

        return $resultPath;
    }
}
