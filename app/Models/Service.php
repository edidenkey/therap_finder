<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',       
        'description',
        'therapeute_id',
        'tarif',
    ];

    public function therapeute(){
        return $this->belongsTo(Therapeute::class);
    }

    public function meeting(){
        return $this->hasOne(Meeting::class);
    }
}
