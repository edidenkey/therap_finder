<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disciplines extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',       
        'description',
    ];

    public function disciplineUser(){
        return $this->hasMany(DisciplineUser::class);
    }
}
