<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbonnementUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',       
        'abonnement_id',
        'date_debut',
        'date_fin',
        'statut',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function abonnement(){
        return $this->belongsTo(Abonnement::class);
    }
}
