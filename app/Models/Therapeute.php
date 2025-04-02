<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Therapeute extends Model
{
    use HasFactory;

    protected $fillable = [
        'note',
        'description_profil',
        'description_services',
    ];

    public function user()
    {
        return $this->morphOne(User::class, 'userable');
    }

    public function products(){
        return $this->hasMany(Product::class);
    }

    public function disciplineUser(){
        return $this->hasMany(DisciplinesUser::class);
    }

    public function service(){
        return $this->hasMany(Service::class);
    }

}
