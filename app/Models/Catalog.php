<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'wp_category_id',
        'name',
        'title',
        'content',
        'sku',
        'base_price',
        'status',
        'image',
    ];
}
