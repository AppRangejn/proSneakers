<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'image',
        'price',
        'sale_price',
        'is_available',
        'colors',
        'sizes',
        'gender',
        'brand',
        'purpose',
        'season',
        'lining_material',
        'upper_material',
        'sole_material'
    ];


    protected $casts = [
        'is_available' => 'boolean',
        'price' => 'float',
        'sale_price' => 'float',
    ];
}