<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $table = 't100_menus';
   public function GroupId($query){
    return $query->groupBy('group_id');
   }
   public function SubGroupId($query){
    return $query->groupBy('sub_group_id');
   }
}
