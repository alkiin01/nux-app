<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueMaterialDetail extends Model
{
    use HasFactory;

    protected $table = 'issue_material_details';

    protected $fillable = [
        'header_id',
        'mtl_seq',
        'part_num',
        'part_name',
        'uom',
        'qty_required',
        'qty_issue',
        'lot_num',
        'warehouse_code',
        'bin_num',
        'created_by',
        'updated_by',
    ];
}
