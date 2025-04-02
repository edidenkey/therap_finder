<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'street',
        'postal_code',
        'country',
        'region',
        'department',
        'email',
        'password',
        'role',
        'userable_type',
        'userable_id',
        'phone',
        'lat',
        'lon',
    ];

    public function userable()
    {
        return $this->morphTo();
    }

    public function images(){
        return $this->hasMany(Image::class);
    }

    public function abonnement(){
        return $this->hasMany(AbonnementUser::class);
    }

    public function info_bancaire(){
        return $this->hasMany(InformationPay::class);
    }

    public function profile_url()
    {
        $profile_url = '';
        $myimages = $this->images();
        if ($myimages) {
            foreach ($myimages as $key => $value) {
                if($value->imageable_type = 'App\Models\ProfilImage'){
                        $profile_url = $value->image_path;
                 }
            }
        }
        return $profile_url;
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
