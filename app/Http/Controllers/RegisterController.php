<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\ProfilImage;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function create()
    {
        return view('session.register');
    }

    public function store()
    {
        $attributes = request()->validate([
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'street' => ['required', 'max:50'],
            'postal_code' => ['required', 'max:50'],
            'country' => ['required', 'max:50'],
            'region' => ['required', 'max:50'],
            'street' => ['required', 'max:50'],
            'department' => ['required', 'max:50'],
            'phone' => ['required', 'max:50'],
            'username' => ['required', 'max:50', Rule::unique('users', 'username')],
            'email' => ['required', 'email', 'max:50', Rule::unique('users', 'email')],
            'password' => ['required', 'min:5', 'max:20'],
            'agreement' => ['accepted']
        ]);
        $attributes['password'] = bcrypt($attributes['password'] );
        $id = DB::table('therapeutes')->insertGetId([
            'note'=> 0,
            'description_profil'=>"",
            'created_at' => now(),
            'updated_at' => now()
        ]);


        session()->flash('success', '');
        $user = User::create( [
            'username'=>$attributes['username'],
            'first_name'=>$attributes['first_name'],
            'email'=>$attributes['email'],
            'password'=> $attributes['password'],
            'last_name'=>$attributes['last_name'],
            'street'=>$attributes['street'],
            'postal_code'=> $attributes['postal_code'],
            'country'=>$attributes['country'],
            'region'=>$attributes['region'],
            'department'=> $attributes['department'],
            'role'=> 'therapeute',
            'userable_type'=>'App\Models\Therapeute',
            'userable_id'=> $id,
            'phone'=> $attributes['phone'],
            'created_at' => now(),
            'updated_at' => now()
         ]);
         $profil_image = ProfilImage::create( [
            'type'=> 'profil',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $image_to_update =  Image::create( [
            'name'=>'default',
            'image_path'=> '/images/profil/default.jpg',
            'user_id'=> $user->id,
            'imageable_type'=> 'App\Models\ProfilImage',
            'imageable_id'=> $profil_image->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        Auth::login($user);
        return redirect('/dashboard');
    }
}
