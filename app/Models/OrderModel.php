<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id',
        'subtotal_amount',
        'shipping_fee',
        'voucher_code',
        'voucher_type',
        'voucher_discount_amount',
        'total_amount',
        'shipping_address',
        'payment_method',
        'delivery_method',
        'status',
    ];
}
