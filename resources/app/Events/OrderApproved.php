<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class OrderApproved implements ShouldBroadcast
{
    use SerializesModels;

    public $orderId;
    public $userId;

    public function __construct($orderId, $userId)
    {
        $this->orderId = $orderId;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('orders.' . $this->userId);
    }
    public function broadcastWith()
    {
        return [
            'orderId' => $this->orderId,
            'message' => 'تم قبول طلبك بنجاح',
        ];
    }
}
