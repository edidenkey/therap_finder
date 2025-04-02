<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisciplinesUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'discipline_id',
        'therapeute_id',
    ];

    public function therapeute(){
        return $this->belongsTo(Therapeute::class);
    }

    public function discipline(){
        return $this->belongsTo(Disciplines::class);
    }
}
