<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'delivery_way_id',
        'status',
        'tracking_number'
    ];

    // Helper methods
    public function isPending() {
        return $this->status === 'pending';
    }

    public function isApproved() {
        return $this->status === 'approved';
    }

    public function isRejected() {
        return $this->status === 'rejected';
    }

    // Relationships
    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function deliveryWay() {
        return $this->belongsTo(DeliveryWay::class);
    }
}
