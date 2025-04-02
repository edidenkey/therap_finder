<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Disciplines;

class DisciplinesController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function show()
    {
        $disciplines = Disciplines::all();

        return view('disciplines/list-disciplines',compact('disciplines'));
    }

    public function store()
    {
        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
            'description' => ['required'],
        ]);

        $user = Disciplines::create( [
            'name'=>$attributes['name'],
            'description'=>$attributes['description'],
            'created_at' => now(),
            'updated_at' => now()
         ]);

         return redirect('/liste-des-disciplines');
    }
}
