<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
    ];

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

}
