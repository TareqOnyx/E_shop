<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use app\contrllers\CategoryController;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
        protected $fillable = [
        'name',
        'image',
    ];

     public function Proudct():HasMany {
        return $this->hasMany(Product::class);
    }
}
