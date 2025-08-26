<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class category extends Model
{
    protected $fillable = ['name', 'image'];
    public function Product():HasMany {
        return $this->hasMany(Product::class);
    }
}
