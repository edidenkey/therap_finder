<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*DB::table('users')->insert([
            'id' => 1,
            'name' => 'admin',
            'email' => 'admin@softui.com',
            'password' => Hash::make('secret'),
            'created_at' => now(),
            'updated_at' => now()
        ]);*/

        
        
/*
        $id = DB::table('admins')->insertGetId([
            'description_profil'=>"Je suis l'admin de cette application",
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        \App\Models\User::create(
            [
                'username'=>'Admin',
                'first_name'=>'Admin',
                'email'=>'admin@gmail.com',
                'password'=> bcrypt('adminsys'),
                'last_name'=>'System',
                'street'=>'test',
                'postal_code'=> '0000',
                'country'=>'Togo',
                'region'=>'Lome',
                'department'=> 'Zio',
                'role'=> 'admin',
                'userable_type'=>'App\Models\Admin',
                'userable_id'=> $id,
                'phone'=> '+22892520560',
                'created_at' => now(),
                'updated_at' => now()
             ]
        ); 
*/
    }
}
