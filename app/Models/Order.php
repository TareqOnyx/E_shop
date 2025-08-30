<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total', 'status', 'delivery_way_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

        public function DeliveryWay()
    {
        return $this->belongsTo(DeliveryWay::class);
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    // Optional: Auto calculate total from items
    public function getTotalAttribute()
    {
        return $this->items->sum(fn($item) => $item->price * $item->quantity);
    }
}
