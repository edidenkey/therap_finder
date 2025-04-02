<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'description_profil',
    ];


    public function user()
    {
        return $this->morphOne(User::class, 'userable');
    }

    public function order(){
        return $this->hasMany(Order::class);
    }

    public function meeting(){
        return $this->hasMany(Meeting::class);
    }


}
