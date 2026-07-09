<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Application extends Model
{
    use HasFactory;
  
    public static function get_flow_process($step) 
    { 
        if ($step=='') {
            $db_menu = DB::table('flow_process as a') 
            ->select('a.*')
            ->where('a.is_active', 1)
            ->where('a.is_delete', 0)
            ->orderBy('sequence_id')
            ->get();
        } else {
            $db_menu = DB::table('flow_process as a') 
            ->select('a.*')
            ->where('a.is_active', 1)
            ->where('a.is_delete', 0)
            ->where('a.sequence_id', '>', $step)
            ->orderBy('sequence_id')
            ->get();
        }
        return $db_menu ;
    }

}
