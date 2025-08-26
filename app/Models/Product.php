<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Product extends Model
{

    protected $fillable = [
        'name',
        'cat_id',
        'price',
        'desc',
        'image',
    ];
        public function Category() :BelongsTo {
        return $this->belongsTo(Category::class);
    }
}
