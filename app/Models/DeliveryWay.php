<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryWay extends Model
{
     protected $fillable = ['name', 'price', 'estimated_days'];

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }
}
