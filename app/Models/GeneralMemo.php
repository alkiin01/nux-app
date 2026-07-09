<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Support\Facades\DB ;
  
class GeneralMemo extends Model
{
    use HasFactory;
  
    public static function get_part($category_id) 
    {       
        $result = DB::connection('sqlsrv4')->table('Erp.Part AS a') 
            ->where('a.ClassID', 'LIKE', '%'.$category_id.'%')  ;  
        return $result ;
    } 
    public static function get_partname($partnum) 
    {       
        $result = DB::connection('sqlsrv4')->table('Erp.Part AS a') 
            ->where('a.PartNum', $partnum)
            ->value('PartDescription');  
        return $result ;
    } 
    public static function get_warehousename($warehouse_code) 
    {       
        $result = DB::connection('sqlsrv4')->table('Erp.Warehse AS a') 
            ->where('a.WarehouseCode', $warehouse_code)
            ->value('Description');  
        return $result ;
    } 

}
