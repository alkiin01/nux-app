<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SjGeneral extends Model
{
    use HasFactory;

    protected $table = 't100_sj_general';
    protected $guarded = [];

    /**
     * Category matrix mapping:
     * Key = [return_status_index][value_aspect_index]
     * return_status: 0=<1bln, 1=1-3bln, 2=3-6bln, 3=6bln-3th, 4=>3th, 5=tidak dikembalikan
     * value_aspect: 0=<=500rb, 1=<=5jt, 2=<=50jt, 3=>50jt
     */
    public static function get_category($return_status, $value_aspect)
    {
        $matrix = [
            // ≤500k   ≤5jt   ≤50jt   >50jt
            ['D',    'C',    'B',    'A'],  // < 1 bulan
            ['D',    'C',    'B',    'A'],  // 1 - 3 bulan
            ['D',    'C',    'B',    'A'],  // 3 - 6 bulan
            ['D',    'C',    'A',    'A'],  // 6 bulan - 3 th
            ['D',    'C',    'A',    'A'],  // > 3 tahun
            ['C',    'B',    'A',    'A'],  // Tidak dikembalikan
        ];

        $return_map = [
            'Dikembalikan ke SAI < 1 bulan'      => 0,
            'Dikembalikan ke SAI 1 - 3 bulan'    => 1,
            'Dikembalikan ke SAI 3 - 6 bulan'    => 2,
            'Dikembalikan ke SAI 6 bulan - 3 th' => 3,
            'Dikembalikan ke SAI > 3 tahun'      => 4,
            'Tidak dikembalikan'                 => 5,
        ];

        $value_map = [
            'Bernilai ≤ Rp.500.000'    => 0,
            'Bernilai ≤ Rp.5.000.000'  => 1,
            'Bernilai ≤ Rp.50.000.000' => 2,
            'Bernilai > Rp.50.000.000' => 3,
        ];

        $r = $return_map[$return_status] ?? null;
        $v = $value_map[$value_aspect]   ?? null;

        if ($r === null || $v === null) return null;
        return $matrix[$r][$v];
    }

    /**
     * Minimum job_level required for checker/approver based on category.
     * Returns [min_checker, min_approver].
     */
    public static function get_approval_levels($category)
    {
        // Category A: Dibuat=staff, Diperiksa=dept head, Disetujui=direktur
        // Category B: Dibuat=staff, Diperiksa=dept head, Disetujui=GM
        // Category C: Dibuat=staff, Diperiksa=section head, Disetujui=asst dept head
        // Category D: Dibuat=staff, Diperiksa=leader, Disetujui=section head
        $levels = [
            'A' => ['checker' => 'dept head',    'approver' => 'direktur'],
            'B' => ['checker' => 'dept head',    'approver' => 'GM'],
            'C' => ['checker' => 'section head', 'approver' => 'asst dept head'],
            'D' => ['checker' => 'leader',       'approver' => 'section head'],
        ];
        return $levels[$category] ?? ['checker' => 'leader', 'approver' => 'section head'];
    }

    /**

     * Role rank map for t110_user_roles (role_id → rank, higher = more senior).
     * BOD=8, AGM=7, Dept Head=6, Sect Head=5, Specialist(Asst DH)=4, Leader=3, Pelaksana=2, Staff=1
     */
    public static function get_role_rank_map()
    {
        return [
            1  => 8, // BOD / Direktur
            3  => 7, // AGM / GM
            4  => 6, // Dept Head
            5  => 5, // Sect Head / Section Head
            6  => 4, // Specialist / Asst Dept Head
            7  => 3, // Leader
            8  => 2, // Pelaksana
            10 => 1, // Staff
        ];
    }

    /**
     * Map approval level name to minimum rank.
     */
    public static function get_level_rank($level)
    {
        $rank = [
            'staff'          => 1,
            'leader'         => 3,
            'asst dept head' => 4,
            'section head'   => 5,
            'dept head'      => 6,
            'GM'             => 7,
            'direktur'       => 8,
        ];
        return $rank[$level] ?? 0;
    }

    /**
     * Company-level roles that are NOT filtered by department.
     * BOD (direktur) and AGM (GM) are company-wide approvers.
     */
    public static function is_company_level($level)
    {
        return in_array($level, ['direktur', 'GM']);
    }

    /**
     * Get users from t110_user_roles (joined with users) meeting minimum role level.
     * Company-level roles (direktur/GM) ignore department filter.
     */
    public static function get_users_by_min_level($min_level, $search = null, $department_id = null)
    {
        $min_rank  = self::get_level_rank($min_level);
        $rank_map  = self::get_role_rank_map();

        $eligible_role_ids = array_keys(array_filter($rank_map, fn($r) => $r >= $min_rank));

        $query = DB::table('t110_user_roles as r')
            ->join('users as u', 'u.username', '=', 'r.username')
            ->whereIn('r.role_id', $eligible_role_ids)
            ->select('u.id', 'u.full_name', 'r.role_name', 'r.department_name');

        // For non-company-level approvals, filter by the PIC's department
        if (!self::is_company_level($min_level) && $department_id) {
            $query->where('r.department_id', $department_id);
        }

        if ($search) {
            $query->where('u.full_name', 'like', '%' . $search . '%');
        }

        return $query->distinct();
    }

    /**
     * Get list of active departments for dropdown.
     */
    public static function get_departments()
    {
        return DB::table('Department')
            ->whereNull('DeletedAt')
            ->orderBy('DepartmentName')
            ->select('id', 'DepartementCode as code', 'DepartmentName as name', 'Division as division')
            ->get();
    }

    /**
     * Generate next SJ number: SAI/SJG/[DEPT_ABBR]/NNNN
     */
    public static function generate_sj_number($departmentCode = null)
    {
        $deptSegment = self::normalize_dept_code($departmentCode);
        $prefix = "SAI/SJG/{$deptSegment}/";

        $last = DB::table('t100_sj_general')
            ->where('sj_number', 'like', $prefix . '%')
            ->orderBy('sj_number', 'desc')
            ->value('sj_number');

        if ($last) {
            $seq = (int) substr($last, -4) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    private static function normalize_dept_code($departmentCode)
    {
        $value = trim((string) $departmentCode);
        if ($value === '') {
            return 'UMUM';
        }

        $value = strtoupper($value);
        $value = preg_replace('/[^A-Z0-9]/', '', $value);

        return $value !== '' ? $value : 'UMUM';
    }

    /**
     * DataTables list query.
     */
    public static function get_list($search = null, $status = null, $user_id = null)
    {
        $query = DB::table('t100_sj_general as h')
            ->leftJoin('users as u', 'u.id', '=', 'h.created_by')
            ->leftJoin('users as c', 'c.id', '=', 'h.checked_by')
            ->leftJoin('users as a', 'a.id', '=', 'h.approved_by')
            ->where('h.is_deleted', 0)
            ->select(
                'h.*',
                'u.full_name as creator_name',
                'c.full_name as checker_name',
                'a.full_name as approver_name'
            );

        if ($status === 'mine' && $user_id) {
            $query->where('h.created_by', $user_id);
        } elseif ($status === 'waiting_check' && $user_id) {
            $query->where('h.checked_by', $user_id)
                  ->where('h.status_checker', 'PENDING')
                  ->whereNotNull('h.sj_number');
        } elseif ($status === 'waiting_approve' && $user_id) {
            $query->where('h.approved_by', $user_id)
                  ->where('h.status_checker', 'APPROVED')
                  ->where('h.status_approver', 'PENDING');
        } elseif ($status === 'approved') {
            $query->where('h.status_checker', 'APPROVED')
                  ->where('h.status_approver', 'APPROVED');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('h.sj_number', 'like', "%$search%")
                  ->orWhere('h.recipient_name', 'like', "%$search%")
                  ->orWhere('u.full_name', 'like', "%$search%");
            });
        }

        return $query;
    }
}
