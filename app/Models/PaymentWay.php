<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentWay extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'desc', 'status'];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
