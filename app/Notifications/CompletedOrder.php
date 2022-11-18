<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class CompletedOrder extends Notification
{
    use Queueable;

    public $order;
    public $image;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order, $image)
    {
        $this->order = $order;
        $this->image = $image;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
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
        } else {
            $channel = $this->order->details->first()->supplier_name;
        }
        return (new SlackMessage)
            ->from('bot')
            ->to($channel)
            ->attachment(function ($attachment) {
                $attachment->title('첨부이미지')
                    ->image($this->image);
            })
            ->content($this->createMessage());
    }

    private function createMessage()
    {
        $message = "배달완료\n";

        $message .= "주문번호: {$this->order->order_number}\n";

        return $message;
    }
}
