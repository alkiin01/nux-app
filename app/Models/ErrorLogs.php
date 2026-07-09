<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class ErrorLogs extends Model
{
    use HasFactory;

    public static function logError($error_from,$error_type,$description,$packNum,$orderNum,$partNum)
    {
        $username = Auth::user()->username;
        $db = DB::table('error_logs')
            ->insert([
                'error_from' => $error_from,
                'error_type' => $error_type,
                'description' => $description,
                'PackNum' => $packNum,
                'OrderNum'=> $orderNum,
                'PartNum'=> $partNum,
                'user_id' => $username
            ]);
        
        return $db;
    }
}
