<?php

namespace App\Http\Controllers; 
use Illuminate\Support\Facades\Auth; 

class ProductionJOController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth'); 
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
       
        $my_id = Auth::user()->id ; 
        $uri = explode("/", url()->current());  
        if (count($uri) < 5) {
            $menu = $this->menu($my_id, 'home') ;   
        } else {
            $menu = $this->menu($my_id, $uri[4]) ;  
        }  
        $data['head_title'] = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'] ;
        $data['menu_level_2'] = $menu['menu_level_2'] ;
        $data['menu_level_3'] = $menu['menu_level_3'] ;
        $data['menu_level_4'] = $menu['menu_level_4'] ; 
        return view('production_jo/production_jo_index', $data);
    }
}
