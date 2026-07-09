<?php

namespace App\Http\Controllers;
 
use Illuminate\Support\Facades\Auth; 

class ProfileController extends Controller
{
     
    public function index()
    {
        $my_id = (Auth::user() == 'NULL' ? Auth::user()->id : 9999) ;  
        $uri = explode("/", url()->current()); 
        if (count($uri) < 4) {
            $menu = $this->menu($my_id, 'home') ;   
        } else {
            $menu = $this->menu($my_id, $uri[3]) ;  
        } 
        $data['head_title'] = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'] ; 
        return view('profile/overview', $data);
    }
 
}
