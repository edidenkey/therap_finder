<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Therapeute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SessionsController extends Controller
{
    public function create()
    {
        return view('session.login-session');
    }

    public function store()
    {
        $attributes = request()->validate([
            'email'=>'required|email',
            'password'=>'required' 
        ]);

        if(Auth::attempt($attributes))
        {
            session()->regenerate();
            $admin = Admin::find(Auth::user()->userable_id);
            $therapeute = Therapeute::find(Auth::user()->userable_id);
            if (Auth::user()->role == "admin" && $admin != null) {

                return redirect('dashboard');

            }elseif (Auth::user()->role == "therapeute" && $therapeute != null) {

                return redirect('dashboard');

            }
            else{

            return back()->withErrors(['email'=>"Vous n'êtes pas autorisé à vous connecter"]);
        }
        }
        else{

            return back()->withErrors(['email'=>'Email ou mot de passe invalide.']);
        }
    }
    
    public function destroy()
    {

        Auth::logout();

        return redirect('/login');
    }
}
