<?php

namespace App\Http\Controllers;

use App\Models\Abonnement;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AbonnementController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function show()
    {
        $abonnements = Abonnement::all();

        return view('abonnements/list-abonnements',compact('abonnements'));
    }

    public function store()
    {
        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
            'description' => ['required'],
            'duree' => ['required', 'max:50'],
            'tarif' => ['required', 'max:50', 'numeric'],
        ]);

        $user = Abonnement::create( [
            'name'=>$attributes['name'],
            'description'=>$attributes['description'],
            'duree'=>$attributes['duree'],
            'tarif'=>$attributes['tarif'],
            'created_at' => now(),
            'updated_at' => now()
         ]);

         return redirect('/liste-des-abonnements');
    }
}
