<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'price',
        'shipping_price',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Upload::class, 'uploadable');
    }

    /**
     *          Query Scopes
     */
    public function scopeWithShipping($query)
    {
        return $query->addSelect(DB::raw("products.*, (products.price + products.shipping_price) as price"));
    }
}
