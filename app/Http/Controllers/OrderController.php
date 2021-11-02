<?php

namespace App\Http\Controllers;

use App\Models\BranchOffice;
use App\Models\Order;
use App\Notifications\CompletedOrder;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Notification;
use Exception;

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
        try {
            $client = new Client();

            $client->post('https://deliver.10tenminute.xyz/api/update_shipment_state',[
                'json' => [
                    'order_number' => $order->order_id,
                    'state' => 'shipped'
                ],
            ]);

            $imageUrl = $this->uploadImageToS3($request->image, $order);

            Notification::route('slack', config('logging.channels.slack.url'))
                ->notify(new CompletedOrder($order, $imageUrl));
        } catch (Exception $exception) {
            return response($exception->getMessage(), $exception->getcode());
        }

        return response('success', 200);
    }

    public function filterByBranch(BranchOffice $branch)
    {
        return Order::whereHas('details', function ($query) use ($branch) {
            $query->where('supplier_name', '=', $branch->name);
        })
            ->where('order_date', 'LIKE', now()->format('Y-m-d') . '%')
            ->with('details')
            ->get();
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
