<?php
namespace App\Constants;

class OrderStatus
{
    const PROCESSING = 'PROCESSING';
    const SHIPPED    = 'SHIPPED';
    const DELIVERED  = 'DELIVERED';
    const CANCELLED  = 'CANCELLED';
    const REFUND_PROCESSING  = 'REFUND PROCESSING';
    const REFUND_REJECTED  = 'REFUND REJECTED';
    const REFUNDED  = 'REFUNDED';
}