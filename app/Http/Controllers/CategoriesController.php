<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Categorie;

class CategoriesController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function show()
    {
        $categories = Categorie::all();

        return view('categories/list-categories',compact('categories'));
    }

    public function store()
    {
        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
            'description' => ['required'],
        ]);

        $user = Categorie::create( [
            'name'=>$attributes['name'],
            'description'=>$attributes['description'],
            'created_at' => now(),
            'updated_at' => now()
         ]);

         return redirect('/liste-des-categories');
    }
}
