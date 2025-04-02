<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function home()
    {
        $nombre_therapeutes =  DB::table('therapeutes')->count();
        $nombre_clients =  DB::table('clients')->count();
        $nombre_orders =  DB::table('orders')->count();
        $nombre_products =  DB::table('products')->count();

        return view('dashboard',compact('nombre_therapeutes','nombre_clients','nombre_orders','nombre_products'));
    }
}
