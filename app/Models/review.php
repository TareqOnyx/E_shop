<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class review extends Model
{
    protected $fillable = ['product_id', 'review_text', 'rating'];
    public function review() :BelongsTo {
        return $this->belongsTo(review::class);
    }
}
