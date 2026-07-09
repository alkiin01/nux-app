<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller; 
use App\Notifications\EmailNotification;
use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\Hash; 
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;
use Notification;


class ForgotPasswordController extends Controller
{ 

public function index() 
    {
    	return view('auth.reset');
    }

public function confirm_reset(Request $request)
    { 
        $validate =  User::where('email', "$request->email")->get()->count() ;  
        $validate2 =  User::where('email', "$request->email")->where('status_id', '>', 1)->count() ;
        $verification_code = self::generate_verification_code();
            if ($validate>0) { 
                if ($validate2>0) {
                    $post_user = User::where('email', "$request->email") 
                    ->update([ 
                        'remember_token' => "$verification_code",
                    ]);
                    if ($post_user) {
                        $data['status_process'] = 1 ; 
                        $data['msg'] = 'Please confirm, check your mailbox!' ; 
                        self::send_reset_code($request->email, $verification_code);
                    } else {
                        $data['status_process'] = 0 ; 
                        $data['msg'] = 'Fail, Please refresh and try again!' ;
                    }  
                } else {
                    $data['status_process'] = 0 ; 
                    $data['msg'] = 'Please confirm your email!' ;
                }  
            } else {
                $data['status_process'] = 0 ; 
                $data['msg'] = 'Email is unknown!' ;
            }
        
        return json_encode($data) ;
    }

public function send_reset_code($email,$token) 
    {
        $uuid = Uuid::uuid4()->toString() ; 
        $year = Carbon::now()->year ;
    	$user = User::where('email', "$email")->first(); 
        $content = [
            'greeting' => 'Hi '.$user->first_name.' '.$user->last_name.',',
            'body' => 'Silahkan verifikasi email anda dan dapat mereset password dan kembali mengakses akun anda dan juga update terbaru dari SAI!',
            'thanks' => 'SUMMIT ADYAWINSA INDONESIA '.$year,
            'actionText' => 'Reset Password',
            'actionURL' => url('confirm_password?email='.$email.'&verification_code='.$token),
            'id' => $user->id,
        ]; 
        Notification::send($user, new EmailNotification($content));  
    }

public function change_password(Request $request)
    { 
        $password = Hash::make($request['password']) ;
        $validate =  User::where('email', "$request->email")->where('remember_token', "$request->verification_code")->first() ;   
        $verification_code = self::generate_verification_code();
            if ($validate->count() > 0) { 
                $post_user = User::where('email', "$request->email")->where('remember_token', "$request->verification_code") 
                ->update([ 
                    'password' => "$password",
                    'updated_by' => $validate->id,
                    'updated_at' => Carbon::now(),
                    'remember_token' => "$verification_code",
                ]);
                if ($post_user) {
                    $data['status_process'] = 1 ;   
                } else {
                    $data['status_process'] = 0 ; 
                    $data['msg'] = 'Fail, Please refresh and try again!' ;
                }  
            } else {
                $data['status_process'] = 0 ; 
                $data['msg'] = 'Link expired!' ;  
            } 
        return json_encode($data) ;
    }

public function confirm_password(Request $request) 
    {
        $email = $request->email;
        $verify_code = $request->verification_code;
        $user = User::where('email', "$email")->where('remember_token', "$verify_code")->get();
        $data['email'] = $email ;
        $data['verification_code'] = $verify_code ;

        $data['after_reset'] = (($request->after_reset==1) ? 1 : 0) ;
        $data['after_register'] = (($request->after_register==1) ? 1 : 0) ; 

        if ($user->count() > 0) { 
    	    return view('auth.confirm', $data);
        } else {
    	    return view('auth.login', $data); 
        }
    }

    public static function generate_verification_code($length = 20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
