<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
    ];


    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

}
