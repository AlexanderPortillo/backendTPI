<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        "id",
        "product_name",
        "category",
        "product_image",
        "supplier",
        "stock",
        "cost",
        "price",
        "description",
        "created_at",
        "updated_at",
    ];
}
