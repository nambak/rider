<?php

namespace App\Http\Controllers;

use App\Alimtalk;
use App\Models\BranchOffice;
use App\Models\Order;
use App\Notifications\CompletedOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Notification;

class OrderController extends Controller
{

    public function getOrderWithDetails($order)
    {
        $result = Order::whereOrderNumber($order)
            ->with(['details', 'delivery'])
            ->first();

        if(! $result) {
            $result = Order::whereId($order)
                ->with(['details', 'delivery'])
                ->first();
        }

        return $result;
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
        if ($order->delivery->completed_at) {
            return response('이미 완료된 주문 건 입니다.', 400);
        }

        $imageUrl = $this->uploadImageToS3($request->image, $order);

        Alimtalk::send('OJ001', $order->receiver_phone, [
            '#{product}' => $order->generateTitle(),
        ]);

        Notification::route('slack', config('logging.channels.slack.url'))
            ->notify(new CompletedOrder($order, $imageUrl));

        $order->delivery->update(['completed_at' => now()]);

        // 적립금 지급
        $order->depositMileage();

        return response('success', 200);
    }

    public function filterByBranch(BranchOffice $branch)
    {
        return Order::where(function ($query) use ($branch) {
            $query->where('branch_office_id', $branch->id);
        })->where(function ($query) {
            $query->whereNull('orders.status')
                ->orWhere('orders.status', '!=', '결제대기');
        })
            ->whereHas('delivery', function ($query) {
                $query->whereNull('completed_at');
            })
            ->where('orders.order_date', 'LIKE', now()->format('Y-m-d') . '%')
            ->with('details')
            ->groupBy('orders.id')
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
