<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'delivery_address',
        'delivery_date',
        'status'
    ];

    // دوال مساعدة للتحقق من الحالة
    public function isPending() {
        return $this->status === 'pending';
    }

    public function isApproved() {
        return $this->status === 'approved';
    }

    public function isRejected() {
        return $this->status === 'rejected';
    }
}
d