<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Notifications\EmailNotification;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Notification;

 
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */
 

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
  
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    } 
    
public function showRegistrationForm()
    {
        // dd(Uuid::uuid4()->toString());
        return view('auth.register');
    }
  
public function register_account(Request $request)
    {
        $uuid = Uuid::uuid4()->toString() ;
        $token = self::generate_verification_code() ;   
        $phone_validate = Validator::make($request->all(), [  
                'phone-num' => ['required', 'string', 'max:14'],   
            ],
            [ 
                'phone-num.max:14' => 'Mohon isi nomor handphone dengan benar.'
            ]
        );
        
        $validate_phone =  User::where('phone_num', "$request->email")->count() ; 
        $email_validate =  User::where('email', "$request->email")->get()->count() ; 
        if ($phone_validate->fails()) { 
            if ($email_validate==0) {
                $data['status_process'] = 0 ;
                $data['msg'] = 'Mohon isi nomor handphone dengan benar.' ; 
            } else {
                if ($validate_phone==0) {
                    $data['status_process'] = 0 ;
                    $data['msg'] = 'Email sudah terdaftar!' ; 
                } else {
                    $data['status_process'] = 0 ;
                    $data['msg'] = 'Ponsel sudah terdaftar!' ;
                }
            } 
        } else {
            if ($email_validate==0) {
                if ($validate_phone==0) {
                    $post_user = User::create([
                        'id' => "$uuid",
                        'first_name' => $request['first-name'],
                        'last_name' => $request['last-name'],
                        'phone_num' => $request['phone-num'],
                        'email' => $request['email'],
                        'status_id' => 1,
                        'remember_token' => "$token",
                        'password' => Hash::make($request['password']), 
                    ]); 
                    if ($post_user) {
                        $data['status_process'] = 1 ; 
                        $data['msg'] = 'Pendaftaran sukses, silahkan verifikasi email anda!' ;  
                        User::create_user_log($uuid,"Pendaftaran Akun","Berhasil");
                        User::create_default_menu($request->email); 
                        self::send_verification_code($request->email,$token) ; 
                    } else {
                        $data['status_process'] = 0 ; 
                        $data['msg'] = 'Pendaftaran gagal, silahkan refresh halaman & coba lagi!' ;
                    }
                } else {
                    $data['status_process'] = 0 ;
                    $data['msg'] = 'Ponsel sudah terdaftar!' ;
                }

            } else {
                $data['status_process'] = 0 ;
                $data['msg'] = 'Email sudah terdaftar!' ; 
            }
        }
        return json_encode($data) ;
    }

public function send_verification_code($email,$token) 
    {
    	$user = User::where('email', "$email")->first(); 
        $year = Carbon::now()->year ;
        $project = [
            'greeting' => 'Hi '.$user->first_name.' '.$user->last_name.',',
            'body' => 'Silahkan verifikasi email anda sebelum anda login.',
            'thanks' => 'Horizon Karawang '.$year,
            'actionText' => 'Verification Code',
            'actionURL' => url('login?email='.$email.'&verification_code='.$token),
            'id' => $user->id
        ]; 
        Notification::send($user, new EmailNotification($project));  
    }
  
public function verification_account(Request $request)
    { 
        $uuid = User::get_user_by_email($request->email);
        $validate =  User::where('email', "$request->email")->where('remember_token', "$request->verification_code")->get()->count() ;  
            if ($validate>0) {
                $validate2 =  User::where('email', "$request->email")->where('remember_token', "$request->verification_code")->where('status_id', '<>', 1)->get()->count() ;  
            if ($validate2==0) {
                $post_user = User::where('email', "$request->email")->where('remember_token', "$request->verification_code")
                ->update([ 
                    'status_id' => 3,
                    'email_verified_at' => Carbon::now(),
                ]);
                if ($post_user) {
                    User::create_user_log($uuid,"Email Verification","Success"); 
                    $data['status_process'] = 1 ; 
                    $data['msg'] = 'Account Verification Seccess!' ; 
                } else {
                    $data['status_process'] = 0 ; 
                    $data['msg'] = 'Fail Verification !' ;
                }
            } else {
                $data['status_process'] = 0 ;
                $data['msg'] = 'Link Expired!' ; 
            }
            } else {
                $data['status_process'] = 0 ;
                $data['msg'] = 'Link Expired!' ; 
            }
        
        return json_encode($data) ;
    } 
  
 
}
