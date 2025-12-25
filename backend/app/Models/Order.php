<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total_price',
        'status',
        'first_name',
        'last_name',
        'email',
        'phone',
        'payment_method', // card, cash
        'delivery_type',  // courier, post
        'city',
        'address',        // для кур'єра
        'post_type',      // branch, post-machine
        'post_number',    // номер відділення/поштомату
        'fee'             // комісія 2% за готівку
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}