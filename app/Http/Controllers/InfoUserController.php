<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Therapeute;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;

class InfoUserController extends Controller
{

    public function create()
    {
        return view('laravel-examples/user-profile');
    }

    public function store(Request $request)
    {

        $attributes = request()->validate([
            'first_name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore(Auth::user()->id)],
            'phone'     => ['required','max:50'],
            'country' => ['required','max:70'],
            'description_profil'    => ['max:150'],
            'description_services'    => ['max:150'],
            'last_name' => ['required','max:70'],
        ]);
        /*if($request->get('email') != Auth::user()->email)
        {
            if(env('IS_DEMO') && Auth::user()->id == 1)
            {
                return redirect()->back()->withErrors(['msg2' => 'You are in a demo version, you can\'t change the email address.']);
                
            }
            //return redirect()->back()->withErrors(['msg2' => 'You are in a demo version, you can\'t change the email address.']);

            
        }
        else{
            $attribute = request()->validate([
                'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore(Auth::user()->id)],
            ]);
        }*/
        
        
        User::where('id',Auth::user()->id)
        ->update([
            'first_name'    => $attributes['first_name'],
            'last_name'    => $attributes['last_name'],
            'email' => $attributes['email'],
            'phone'     => $attributes['phone'],
            'country' => $attributes['country'],
        ]);


        if (Auth::user()->role == 'admin') {
            Admin::where('id',Auth::user()->userable_id)
            ->update([
                'description_profil'    => $attributes['description_profil'],
            ]);
        }elseif (Auth::user()->role == 'therapeute') {
            Therapeute::where('id',Auth::user()->userable_id)
            ->update([
                'description_profil'    => $attributes['description_profil'],
                'description_services'    => $attributes['description_services'],
            ]);
        }

        return redirect('/mon-profil');
    }
}
