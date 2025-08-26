<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'payment_way_id',
        'amount',
        'status',
        'transaction_id',
    ];

    public function paymentWay()
    {
        return $this->belongsTo(PaymentWay::class);
    }
}
