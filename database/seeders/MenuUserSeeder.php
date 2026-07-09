<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $menu_full = DB::table('t100_menus')->get() ;
        // foreach ($menu_full AS $row) {
            $user_id = 1082 ;
            DB::table('t100_user_menus')->insert([ 
                'user_id' => $user_id,
                'menu_id' => 1,    
                'as_create' => 1,
                'as_read' => 1,
                'as_update' => 1,
                'as_delete' => 1,
            ]);
            DB::table('t100_user_menus')->insert([ 
                'user_id' => $user_id,
                'menu_id' => 2,    
                'as_create' => 1,
                'as_read' => 1,
                'as_update' => 1,
                'as_delete' => 1,
            ]);
            DB::table('t100_user_menus')->insert([ 
                'user_id' => $user_id,
                'menu_id' => 19,    
                'as_create' => 1,
                'as_read' => 1,
                'as_update' => 1,
                'as_delete' => 1,
            ]);
            DB::table('t100_user_menus')->insert([ 
                'user_id' => $user_id,
                'menu_id' => 83,    
                'as_create' => 1,
                'as_read' => 1,
                'as_update' => 1,
                'as_delete' => 1,
            ]);
            DB::table('t100_user_menus')->insert([ 
                'user_id' => $user_id,
                'menu_id' => 88,    
                'as_create' => 1,
                'as_read' => 1,
                'as_update' => 1,
                'as_delete' => 1,
            ]);
            DB::table('t100_user_menus')->insert([ 
                'user_id' => $user_id,
                'menu_id' => 81,    
                'as_create' => 1,
                'as_read' => 1,
                'as_update' => 1,
                'as_delete' => 1,
            ]); 

            // DB::table('t100_user_doc_access')->insert([ 
            //     'user_id' => $user_id,
            //     'trc_type_id' => 76,    
            //     'descr' => 'PP',
            // ]); 


        // }
        
    }
}
