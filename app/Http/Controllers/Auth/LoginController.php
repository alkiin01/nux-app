<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers; 
use Illuminate\Support\Facades\Validator;  
use GuzzleHttp\Exception\RequestException; 
use Illuminate\Support\Facades\Crypt ;
use Illuminate\Support\Facades\Hash ; 
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;  
use Carbon\Carbon;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
     

    protected $redirectTo = RouteServiceProvider::HOME;


    public function __construct()
    { 
        $this->middleware('guest')->except('logout');  
    }  

    public function check_robot_login(Request $request)
    {
        $username = $request->username ;
        $password = $request->password ;
        // $validate = Validator::make($request->all(), [
        //     'g-recaptcha-response' => 'required|captcha'
        // ], [
        //     'g-recaptcha-response.required' => 'required|captcha'
        // ]);
        // if ($validate->fails()) {
        //     $data['status_process'] = 0;
        //     $data['msg'] = 'Please confirm, are you robot or not';
        // } else { 
            $auth_epicor = self::auth_epicor($username, $password) ;   
            // dd($auth_epicor);
            $fullname  = $auth_epicor['fullname'] ;
            $email = $auth_epicor['email'] ; 
            $security_mgr = $auth_epicor['security_mgr'] ;
            $group_list = $auth_epicor['group_list'] ; 
            if ($auth_epicor['code'] == 200) {
                $get_user = User::get_users($username);   
                if ($get_user > 0) { 
                    $update = DB::table('users')
                    ->where('username', "$username") 
                    ->update([
                        'password' => Hash::make("$password"),
                        'epicor_password' => Crypt::encryptString($password),
                        'full_name' => "$fullname",
                        'email' => ($email == '' ? $username.'@summitadyawinsa.co.id' : "$email"),
                    ]) ;
                    
                    if ($update) {
                        $data['status_process'] = 1 ;
                    } else {
                        $data['status_process'] = 0 ;
                        $data['msg'] = 'Username not found!' ; 
                    }
                } else {  
                    $create_new_user =  DB::table('users')->insert([
                        'username'   => $username,
                        'email' => ($email == '' ? $username.'@summitadyawinsa.co.id' : "$email"),
                        'full_name'  => "$fullname",
                        'call_name'  => $username,
                        'gender_id'  => 1, 
                        'password'   => Hash::make($password),
                        'epicor_password' => Crypt::encryptString($password),
                        'status_id'  => 3, 
                        'role_id'    => 1,
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]);  
                    if ($create_new_user) {  
                        if (strpos($group_list, 'DPC') !== false) {
                            $db_group_menu = DB::table('t100_group_menu')->where('group_code', 'shipment')->get() ;
                            $id = User::get_user_id($username) ;
                            $i = 0;
                            foreach ($db_group_menu as $row) { 
                                ${'post'.$i} = DB::table('t100_user_menus')->insert([
                                    'user_id' => $id,
                                    'menu_id' => $row->menu_id,
                                ]) ;  
                                $i++;
                            } ; 
                        } else if (strpos($group_list, 'ADM-ASSY') !== false) {
                            $db_group_menu = DB::table('t100_group_menu')->where('group_code', 'ADMIN ASSEMBLY')->get() ;
                            $id = User::get_user_id($username) ;
                            $i = 0;
                            foreach ($db_group_menu as $row) { 
                                ${'post'.$i} = DB::table('t100_user_menus')->insert([
                                    'user_id' => $id,
                                    'menu_id' => $row->menu_id,
                                ]) ;  
                                $i++;
                            } ; 
                        } else if (strpos($group_list, 'ADM-STP') !== false) {
                            $db_group_menu = DB::table('t100_group_menu')->where('group_code', 'ADMIN STAMPING')->get() ;
                            $id = User::get_user_id($username) ;
                            $i = 0;
                            foreach ($db_group_menu as $row) { 
                                ${'post'.$i} = DB::table('t100_user_menus')->insert([
                                    'user_id' => $id,
                                    'menu_id' => $row->menu_id,
                                ]) ;  
                                $i++;
                            } ; 
                        } else if (strpos($group_list, 'MANAGER') !== false || strpos($group_list, 'STAFF') !== false || strpos($group_list, 'AGM') !== false || strpos($group_list, 'HEAD') !== false || strpos($group_list, 'SECTION') !== false || strpos($group_list, 'SUPER') !== false) {
                            $db_group_menu = DB::table('t100_group_menu')->where('group_code', 'approver')->get() ;
                            $id = User::get_user_id($username) ;
                            $i = 0;
                            foreach ($db_group_menu as $row) { 
                                ${'post'.$i} = DB::table('t100_user_menus')->insert([
                                    'user_id' => $id,
                                    'menu_id' => $row->menu_id,
                                ]) ;  
                                $i++;
                            } ; 
                        }

                          
                        $data['status_process'] = 1 ; 
                    } else {
                        $data['status_process'] = 0 ;
                        $data['msg'] = 'Failed to Login Call Administrator!' ; 
                    }
                }
            } else if ($auth_epicor['status'] == 401) {
                $data['status_process'] = 0 ;
                $data['msg'] = 'Username and Password not found!' ; 
            } else {
                $data['status_process'] = 0 ;
                $data['msg'] = 'Username and Password not found!' ;
            }
        // }
        return json_encode($data);
    }

    public function auth_epicor($username, $password)
    {
        $client = new Client(); 
        $data = []; 
        $host_api = self::get_host_api() ;
        try {
            $response = $client->request('POST', $host_api.'Auth/Login', [
                'json' => [
                    'nik' => "$username", 
                    'password' => "$password"
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,      
            ]);
        
            // Mendapatkan body dari respons dan decode dari JSON ke array
            $responseBody = json_decode($response->getBody()->getContents(), true);
            // Mengisi data dari respons
            $data['code'] = $responseBody['code'];
            $data['status'] = $responseBody['status'];
            $data['fullname'] = $responseBody['name'];
            $data['email'] = $responseBody['email'];
            $data['security_mgr'] = $responseBody['securityMgr'];
            $data['group_list'] = $responseBody['groupList'];
        
        } catch (RequestException $e) { 
            \Log::error('API request failed', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);
        
            $data['code'] = 500;
            $data['status'] = 'error'; 
            $data['fullname'] = '';
            $data['email'] = '';
            $data['security_mgr'] = '';
            $data['group_list'] = '';
        }

        return $data ;
    }
  

}
