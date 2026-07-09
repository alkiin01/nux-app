<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;  


class ApplicationsController extends Controller
{
    public function check_robot(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'g-recaptcha-response' => 'required|captcha'
        ],[
            'g-recaptcha-response.required' => 'required|captcha'
        ]);
        if ($validate->fails()) { 
            $data['status_process'] = 0 ;
            $data['msg'] = 'Please confirm, are you robot or not.' ; 
        } else {
            $data['status_process'] = 1 ;
        }
        return json_encode($data);
    }
}
