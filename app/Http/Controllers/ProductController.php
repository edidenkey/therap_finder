<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function show()
    {
        $id = Auth::user()->userable_id;
        $all_products = Product::all();
        $user_products = Product::where('therap_id', $id)->get();
        $products = (Auth::user()->role == "admin") ? $all_products : $user_products ;
        $categories = Categorie::all();

        return view('products/list-products',compact('products','categories'));
    }

    public function store()
    {
        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
            'description' => ['required'],
            'image' => ['required'],
            'categorie' => ['required'],
            'prix' => ['required','numeric'],
        ]);
        
        $image = request()->file('image'); 
        $name =  Str::slug($attributes['name']).time().'.'.$image->getClientOriginalExtension();
        $destinationPath = '/images/products/'.$name;
        Storage::disk('public')->put($destinationPath, file_get_contents($image));
        $product_id = DB::table('products')->insertGetId([
            'name'=> $attributes['name'],
            'prix'=>$attributes['prix'],
            'description'=>$attributes['description'],
            'categorie_id'=>$attributes['categorie'],
            'therap_id'=> Auth::user()->userable_id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $id = DB::table('product_images')->insertGetId([
            'type'=> 'front',
            'product_id'=> $product_id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $image = Image::create( [
            'name'=>$name,
            'image_path'=>$destinationPath,
            'user_id'=> Auth::user()->userable_id,
            'imageable_type'=> 'App\Models\ProductImage',
            'imageable_id'=> $id,
            'created_at' => now(),
            'updated_at' => now()
         ]);



         return redirect('/liste-des-produits');
    }
}
