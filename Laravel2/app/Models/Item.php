<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'purchase_price',
        'sale_price',
        'stock',
        'min_stock',
        'unit',
        'notif_active',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function changes()
    {
        return $this->hasMany(StockChange::class);
    }
}

