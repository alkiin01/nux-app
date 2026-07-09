<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash ; 
use Illuminate\Support\Facades\Crypt ;
use Carbon\Carbon;


class User extends Authenticatable
{ 

    use HasFactory;
    protected $guarded = [];
    
    public static function get_user_by_email($email) { 
        $id = DB::table('users')->where('email', "$email")->first()->id ; 
        return $id ;
    } 

    public static function create_user_log($id,$name_activity,$value_activity) { 
          return DB::table('user_logs')->insert([
                'user_id' => "$id",
                'name_activity' => "$name_activity",
                'value_activity' => "$value_activity",
            ]) ;  
    } 

    public static function get_users($username) 
    {  
        $result = DB::table('users')
                ->where('username', $username)
                ->count(); 
        return $result;
    }

    public static function update_after_login($username, $password, $fullname, $email) 
    { 
        return DB::table('users')
            ->where('username', "$username") 
            ->update([
                'password' => Hash::make("$password"),
                'epicor_password' => Crypt::encryptString($password),
                'full_name' => "$fullname",
                'email' => ($email == '' ? null : "$email")
            ]) ;
    }

    public static function create_new_user($username, $password, $fullname, $email) 
    {
        return DB::table('users')->insert([
            'username'   => $username,
            'email' => 'aji.sanjaya@summitadyawinsa.co.id',
            'full_name'  => $fullname,
            'call_name'  => $username,
            'gender_id'  => 1, 
            'password'   => Hash::make($password),
            'epicor_password' => Crypt::encryptString($password),
            'status_id'  => 3, 
            'role_id'    => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }

    public static function get_user_id($username) { 
        $id = DB::table('users')->where('username', "$username")->first()->id ; 
        return $id ;
    } 

    public static function create_default_menu($username, $security_mgr, $group_list) {
        $id = self::get_user_id($username) ;
        DB::table('user_menus')->where('user_id', $id)->delete();
        // if ($security_mgr == true) {
        //     $menu = DB::table('menus')->get();
        //     $i = 0;
        //     foreach ($menu as $t) {
        //         ${'post'.$i} = DB::table('user_menus')->insert([
        //             'user_id' => "$id",
        //             'menu_id' => $t->id,
        //         ]) ; 
        //         $i++;
        //     }
        // } else { 
            // $db_group_list = explode("~", $group_list);
            $db_group_menu = DB::table('t100_group_menu')->where('group_code', 'approver')->get() ;
            $i = 0;
            foreach ($db_group_menu as $row) { 
                ${'post'.$i} = DB::table('user_menus')->insert([
                    'user_id' => $id,
                    'menu_id' => $row->menu_id,
                ]) ;  
                $i++;
            }
        // } 
        return DB::table('user_menus')->where('user_id', $id)->count();
    }
     

}
