<?php
  
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;  
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    { 
        // DB::table('users')->insert(['username' => '040814-394', 'email' => 'aji.sanjaya@summitadywinsa.co.id', 'full_name' => 'Herno Aji Sanjaya', 'call_name' => 'Aji', 'gender_id' => 1, 'password' => Hash::make('Epicor123'), 'status_id' => 3, 'role_id' => 1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s') ]);
        DB::table('users')->insert(['username' => '02-0049', 'email' => 'ppic@banshu-rubber.com', 'full_name' => 'Banshu Rubber Indonesia', 'call_name' => 'Melani Trias dan Okta', 'VendorID' => '02-0050', 'gender_id' => 1, 'password' => Hash::make('2VDIz9Tcqw'), 'status_id' => 3, 'role_id' => 1, 'partner_id' => 49, 'created_at' => Carbon::now()->format('Y-m-d H:i:s') ]);

        DB::table('users')->insert(['username' => '02-0044', 'email' => 'sales1@garudametalutama.com', 'full_name' => 'Garuda Metal Utama', 'call_name' => 'LASTRI', 'VendorID' => '02-0050', 'gender_id' => 1, 'password' => Hash::make('DvtRdoxhnA'), 'status_id' => 3, 'role_id' => 1, 'partner_id' => 44, 'created_at' => Carbon::now()->format('Y-m-d H:i:s') ]);



    }
}
