<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
     protected $fillable = ['order_id', 'delivery_way_id', 'status', 'tracking_number'];

    public function deliveryWay()
    {
        return $this->belongsTo(DeliveryWay::class);
    }
}
