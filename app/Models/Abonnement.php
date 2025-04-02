<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abonnement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'duree',
        'tarif',
    ];

    public function abonnementUser(){
        return $this->hasMany(AbonnementUser::class);
    }

}
