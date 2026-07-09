<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PageControl extends Model
{
    use HasFactory;

    public static function menu_level_1($id, $menu_active) 
    {
        $get_child_id = DB::table('t100_menus')->where('menu', "$menu_active")->orderBy('id')->first()->sub_group_id ;
        $db_menu = DB::table('t100_menus as a')
        ->join('t100_user_menus as b', 'a.id', 'b.menu_id')
        ->select('a.*', DB::raw("CASE WHEN a.sub_group_id = $get_child_id THEN 'hover show' ELSE '' END AS active"))
        ->where('b.user_id', $id)
        ->where('a.level_menu_id', 1) 
        ->orderBy('a.sequence_id')->get();
        return $db_menu ;
    }

    public static function menu_level_2($id, $menu_active) 
    {
        $get_child_id = DB::table('t100_menus')->where('menu', "$menu_active")->orderBy('id')->first()->sub_group_id ;
        $db_menu = DB::table('t100_menus as a')
        ->join('t100_user_menus as b', 'a.id', 'b.menu_id')
        ->select('a.*', DB::raw("CASE WHEN a.sub_group_id = $get_child_id THEN 'hover show' ELSE '' END AS active"))
        ->where('b.user_id', $id)
        ->where('a.level_menu_id', 2)
        ->orderBy('a.sequence_id')->get();
        return $db_menu ;
    }

    public static function menu_level_3($id, $menu_active) 
    {
        $get_child_id = DB::table('t100_menus')->where('menu', "$menu_active")->orderBy('id')->first()->sub_group_id ;
        $db_menu = DB::table('t100_menus as a')
        ->join('t100_user_menus as b', 'a.id', 'b.menu_id')
        ->select('a.*', DB::raw("CASE WHEN a.sub_group_id = $get_child_id THEN 'hover show' ELSE '' END AS active"))
        ->where('b.user_id', $id)
        ->where('a.level_menu_id', 3) 
        ->orderBy('a.sequence_id')->get();
        return $db_menu ;
    }

    public static function menu_level_4($id, $menu_active) 
    {
        $db_menu = DB::table('t100_menus as a')
        ->join('t100_user_menus as b', 'a.id', 'b.menu_id')
        ->select('a.*', DB::raw("CASE WHEN a.menu = '$menu_active' THEN 'active' ELSE '' END AS active"))
        ->where('b.user_id', $id)
        ->where('a.level_menu_id', 4)
        ->orderBy('a.sequence_id')->get();
        return $db_menu ;
    }

    public static function getHeadTitle($menu_active) 
    { 
        $get_child_id = DB::table('t100_menus')->where('menu', "$menu_active")->orderBy('id')->first()->menu_name ;
        return $get_child_id ;
    }

}
