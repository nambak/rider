<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class DeliveryStartNotification extends Notification
{
    use Queueable;

    private $order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        if ($this->order->branchOffice) {
            $channel = $this->order->branchOffice->name;
            $orderNumber = $this->order->order_number;
        } else {
            $channel = $this->order->details->first()->supplier_name;
            $orderNumber = $this->order->order_id;
        }
        return (new SlackMessage)
            ->from('bot')
            ->to($channel)
            ->content("주문번호 {$orderNumber} 배송이 시작되었습니다.");
    }
}
