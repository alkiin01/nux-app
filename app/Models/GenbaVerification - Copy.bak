<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class GenbaVerification extends Model
{
    use HasFactory;
    #region SPV Genba Verification
    public static function get_spv_verification()
    {
        $result = DB::connection('sqlsrv')->table('GenbaSPVProcAudit')
            ->select('SysID', 'Date', 'Area_checked', 'Auditor', 'Status', 'note');
        if (!empty($search)) {
            $result = $result->where('Date', 'LIKE', "%$search%");
        }
        return $result->where('IsDelete', 0);
    }
    #endregion
}
