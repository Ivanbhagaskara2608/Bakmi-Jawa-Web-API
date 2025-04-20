<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_method_code',
        'status',
        'total',
        'total_point',
        'invoice_id',
        'expired_at',
        'checkout_url',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
