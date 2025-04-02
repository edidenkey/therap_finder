<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'prix',
        'description',
        'categorie_id',
        'therapeute_id',
        'stock'
    ];

    public function images(){
        return $this->hasMany(ProductImage::class);
    }

    public function therapeute(){
        return $this->belongsTo(Therapeute::class);
    }

    public function categorie(){
        return $this->belongsTo(Categorie::class);
    }

    public function order(){
        return $this->hasMany(Order::class);
    }

}
