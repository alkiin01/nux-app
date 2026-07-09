<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class GenbaManagement extends Model
{
    use HasFactory;

    public static function get_team_list($search)
    {
        $result = DB::connection('sqlsrv')->table('GenbaTeams')->where('isDelete', 0)
            ->select('SysID', 'name', 'role');
        if (!empty($search)) {
            $result = $result->where('name', 'LIKE', "%$search%");
        }
        return $result;
    }

    public static function insert_genba_teams($teamName, $teamRole, $id_team)
    {
        $data_genba = self::get_genba_teams($teamName, $teamRole, $id_team);
        if ($data_genba->count() == 0) {

            return DB::table('GenbaTeams')->insert([
                'name'       => $teamName,
                'role'       => $teamRole,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } else {
            return DB::table('GenbaTeams')
                ->where('SysID', $id_team)
                ->update([
                    'name'       => $teamName,
                    'role'       => $teamRole,
                    'updated_at' => Carbon::now() // Update hanya timestamp, bisa ditambah field lain
                ]);
        }
    }

    public static function get_genba_teams($teamName, $teamRole, $id_team)
    {
        $result = DB::table('GenbaTeams')
            ->where('SysID', $id_team)
            ->where('name', $teamName)
            ->where('role', $teamRole);
        return $result;
    }

    public static function delete_teams($teamName, $teamRole, $id_team)
    {
        $data_genba = self::get_genba_teams($teamName, $teamRole, $id_team);
        if ($data_genba->count() > 0) {
            return DB::table('GenbaTeams')
                ->where('SysID', $id_team)
                ->update([
                    'isDelete' => 1
                ]);
        }
    }

    public static function get_team_member_list($search)
    {
        $result = DB::connection('sqlsrv')->table('GenbaTeamMembers as a')
            ->leftJoin('GenbaTeams as b', 'a.te am_id', '=', 'a.SysID')
            ->leftJoin('users as c', 'a.user_id', '=', 'c.id')
            ->select('a.SysID', 'a.team_id', 'a.user_id', 'c.username', 'c.full_name', 'c.email', 'c.phone_num');
        if (!empty($search)) {
            $result = $result->where('c.name', 'LIKE', "%$search%")
                ->orWhere('c.full_name', 'LIKE', "%$search%");
        }
        return $result;
    }

    #region ScheduleGenba
    public static function get_genba_schedule()
    {
        $result = DB::connection('sqlsrv')->table('ScheduleGenba as a')
            ->leftJoin('GenbaTeams as b', 'a.team_id', '=', 'b.SysID')
            ->select('a.SysID', 'a.title', 'a.team_id', 'color', 'a.genba_date', 'a.end_date', 'a.execution_date', 'a.execution_status', 'a.location', 'a.description', 'b.name as team_name');

        return $result;
    }

    public static function add_genba_schedule($title, $event_type, $status, $description, $genba_date, $end_date, $execution_date, $execution_status, $location, $color)
    {
        return DB::table('ScheduleGenba')->insert([
            'title'         => $title,
            'event_type'         => $event_type,
            'genba_date'      => $genba_date,
            'end_date'        => $end_date,
            'execution_date'  => $execution_date,
            'execution_status' => $execution_status,
            'location'        => 'SAI',
            'status'        => $status,
            'description'     => $description,
            'color'     => $color,
            'created_at'      => Carbon::now(),
            'updated_at'      => Carbon::now(),
        ]);
    }
    public static function check_date_activity($id_activity)
    {
        $result = DB::table('GenbaProcAudit as a')
            ->where('SysID', $id_activity)
            ->select('a.Date', 'a.Area_Checked', 'a.Auditor', 'a.process', 'a.station', 'a.Category_id');
        return $result;
    }

    public static function get_genba_schedule_by_id($id)
    {
        $result = DB::table('ScheduleGenba')
            ->where('SysID', $id);
        return $result;
    }
    #endregion

    #region GenbaActivity

    public static function get_genba_activity($id)
    {
        $result = DB::connection('sqlsrv')->table('GenbaProcAudit as a')
            ->leftJoin('GenbaCategory as b', 'b.SysID', '=', 'a.Category_id')
            ->select(
                'a.SysID',
                'a.Date',
                'a.Area_Checked',
                'a.Auditor',
                'a.process',
                'a.station',
                'a.Category_id',
                'b.Category as category',
                'b.Description as category_desc'
            )
            ->where('a.SysID', $id);
        return $result;
    }
    public static function get_mng_genba_activity($id)
    {
        $db = DB::connection('sqlsrv')->table('GenbaProcAuditDtl as a')
            ->select('a.genba_id')
            ->where('a.SysID', $id)
            ->limit(1)->get();
        $genba_id = 0;
        foreach ($db as $key) {
            $genba_id = $key->genba_id;
        }
        $result = DB::connection('sqlsrv')->table('GenbaProcAudit as a')
            ->leftJoin('GenbaCategory as b', 'b.SysID', '=', 'a.Category_id')
            ->select(
                'a.SysID',
                'a.Date',
                'a.Area_Checked',
                'a.Auditor',
                'a.process',
                'a.station',
                'a.Category_id',
                'b.Category as category',
                'b.Description as category_desc'
            )
            ->where('a.SysID', $genba_id);
        return $result;
    }
    public static function get_genba_category()
    {
        $result = DB::connection('sqlsrv')->table('GenbaCategory')
            ->select('SysID', 'Category', 'Description');
        return $result;
    }
    public static function add_genba_activity($Area_Checked, $Auditor, $category, $Date,  $sysID, $station, $process)
    {
        $result = DB::connection('sqlsrv')->table('GenbaProcAudit as a')->where('a.Date', $Date)
            ->where('a.SysID', $sysID)
            ->select('a.SysID');
        $data_genba = $result;
        if ($data_genba->count() == 0) {
            return DB::table('GenbaProcAudit')->insertGetId([
                'Area_Checked'  => $Area_Checked,
                'Date'  => $Date,
                'Auditor'       => $Auditor,
                'category_id'       => $category,
                'station'       => $station,
                'process'       => $process,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'status' => 4
            ]);
        } else {
            $update = DB::table('GenbaProcAudit')
                ->where('SysID', $sysID)
                ->update([
                    'Area_Checked'  => $Area_Checked,
                    'Date'  => $Date,
                    'Auditor'       => $Auditor,
                    'station'       => $station,
                    'process'       => $process,
                    'category_id'       => $category,
                    'updated_at' => Carbon::now() // Update hanya timestamp, bisa ditambah field lain
                ]);
            if ($update > 0) {
                return $sysID;
            } else {
                return 0;
            }
        }
    }
    public static function add_genba_mng_activity($Area_Checked, $Auditor, $Status, $Date, $note, $sysID)
    {
        $result = DB::connection('sqlsrv')->table('GenbaMngProcAudit as a')->where('a.Date', $Date)
            ->where('a.SysID', $sysID)
            ->select('a.SysID');
        $data_genba = $result;
        if ($data_genba->count() == 0) {
            return DB::table('GenbaMngProcAudit')->insertGetId([
                'Area_Checked'  => $Area_Checked,
                'Date'  => $Date,
                'Auditor'       => $Auditor,
                'Status'       => $Status,
                'note'       => $note,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } else {
            $update =  DB::table('GenbaMngProcAudit')
                ->where('SysID', $sysID)
                ->update([
                    'Area_Checked'  => $Area_Checked,
                    'Date'  => $Date,
                    'Auditor'       => $Auditor,
                    'note'       => $note,
                    'Status'       => $Status,
                    'updated_at' => Carbon::now() // Update hanya timestamp, bisa ditambah field lain
                ]);
            if ($update > 0) {
                return $sysID;
            } else {
                return 0;
            }
        }
    }
    public static function submit_genba_mng_activity($Status,  $sysID)
    {

        $update =  DB::table('GenbaMngProcAudit')
            ->where('SysID', $sysID)
            ->update([
                'Status'       => $Status,
                'updated_at' => Carbon::now() // Update hanya timestamp, bisa ditambah field lain
            ]);
        if ($update > 0) {
            return $sysID;
        } else {
            return 0;
        }
    }
    public static function submit_genba__activity($Status,  $sysID)
    {

        $update =  DB::table('GenbaProcAudit')
            ->where('SysID', $sysID)
            ->update([
                'Status'       => $Status,
                'updated_at' => Carbon::now() // Update hanya timestamp, bisa ditambah field lain
            ]);
        if ($update > 0) {
            return $sysID;
        } else {
            return 0;
        }
    }

    public static function delete_genba_activity($id)
    {
        $result = DB::table('GenbaProcAudit')
            ->where('SysID', $id)
            ->update([
                'IsDelete' => 1,
                'updated_at' => Carbon::now()->toDateTimeString()
                // Update hanya timestamp, bisa ditambah field lain
            ]);;
        return $result;
    }
    public static function delete_mng_genba_activity($id)
    {
        $result = DB::table('GenbaMngProcAudit')
            ->where('SysID', $id)
            ->update([
                'IsDelete' => 1,
                'updated_at' => Carbon::now()->toDateTimeString()
                // Update hanya timestamp, bisa ditambah field lain
            ]);;
        return $result;
    }
    public static function get_genba_scope_item()
    {
        $scopes = DB::table('GenbaLPAScope')
            ->Join('GenbaAuditItem', 'GenbaAuditItem.scope_id', '=', 'GenbaLPAScope.SysID')
            ->select(
                'GenbaLPAScope.LPA_Scope',
                'GenbaAuditItem.Check_item',
                'GenbaAuditItem.Check_item_eng',
                'GenbaAuditItem.SysID as check_item_id',
                'GenbaLPAScope.SysID as scope_id'
            );

        $result = DB::table('GenbaLPAScope as a')
            ->leftJoin('GenbaAuditItem as b', 'b.scope_id', '=', 'a.SysID')
            ->leftJoin('GenbaProcAuditDtl as c', function ($join) {
                $join->on('c.scope_id', '=', 'a.SysID')
                    ->on('c.check_item_id', '=', 'b.SysID')
                    ->where('c.genba_id', '=', 0);
            })
            ->leftJoin('GenbaProcAudit as d', function ($join) {
                $join->on('d.SysID', '=', 'c.genba_id')
                    ->where('d.IsDelete', '=', 0);
            })
            ->select('a.LPA_Scope', 'b.SysID as check_item_id' . 'b.Check_item', 'b.Check_item_eng', 'c.result', 'c.Path');

        return $scopes;
    }

    public static function save_genba_activity_detail($my_id, $id_activity, $scope_id, $check_item_id,  $answer, $due_date)
    {
        $result = DB::connection('sqlsrv')->table('GenbaProcAuditDtl as a')->where('a.genba_id', $id_activity)
            ->where('a.scope_id', $scope_id)
            ->where('a.check_item_id', $check_item_id)
            ->select('a.SysID');
        $data_genba = $result;
        if ($data_genba->count() == 0) {
            $updateHeader = DB::table('GenbaProcAudit')
                ->where('SysID', $id_activity)
                ->update([
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'status' => 4
                ]);
            return DB::table('GenbaProcAuditDtl')->insert([
                'genba_id'  => $id_activity,
                'scope_id'  => $scope_id,
                'check_item_id' => $check_item_id,
                'due_date' => $due_date,
                'result'       => $answer,
                'user_id'       => $my_id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } else {
            $updateHeader = DB::table('GenbaProcAudit')
                ->where('SysID', $id_activity)
                ->update([
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    'status' => 4
                ]);
            return DB::table('GenbaProcAuditDtl')
                ->where('SysID', $data_genba->first()->SysID)
                ->where('genba_id', $id_activity)
                ->where('scope_id', $scope_id)
                ->where('check_item_id', $check_item_id)
                ->update([
                    'due_date' => $due_date,
                    'result'       => $answer,
                    'updated_at' => Carbon::now()->toDateTimeString()
                    // Update hanya timestamp, bisa ditambah field lain
                ]);
        }
    }
    public static function save_genba_mng_activity_detail($my_id, $id_activity, $scope_id, $check_item_id,  $answer, $due_date)
    {
        $result = DB::connection('sqlsrv')->table('GenbaMngProcAuditDtl as a')->where('a.genba_id', $id_activity)
            ->where('a.scope_id', $scope_id)
            ->where('a.check_item_id', $check_item_id)
            ->select('a.SysID');
        $data_genba = $result;
        if ($data_genba->count() == 0) {
            return DB::table('GenbaMngProcAuditDtl')->insert([
                'genba_id'  => $id_activity,
                'scope_id'  => $scope_id,
                'check_item_id' => $check_item_id,
                'due_date'       => $due_date,
                'result'       => $answer,
                'user_id'       => $my_id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        } else {
            return DB::table('GenbaProcAuditDtl')
                ->where('SysID', $data_genba->first()->SysID)
                ->where('genba_id', $id_activity)
                ->where('scope_id', $scope_id)
                ->where('check_item_id', $check_item_id)
                ->update([
                    'due_date'     => $due_date,
                    'result'     => $answer,
                    'updated_at' => Carbon::now()->toDateTimeString()
                    // Update hanya timestamp, bisa ditambah field lain
                ]);
        }
    }
    public static function get_genba_activity_detail($activity_id, $scope_id, $check_item_id)
    {
        $result = DB::connection('sqlsrv')->table('GenbaProcAuditDtl as a')
        ->leftJoin('GenbaProcAudit as b', 'b.SysID', '=', 'a.genba_id')
            ->select(
                'a.findings',
                'a.Path',
                'a.asign_to',
                'a.asign_to_name',
                'a.asign_to_dept',
                'a.asign_to_dept_name',
                'a.priority',
                'a.area_detail',
                'b.process',
                'b.station'
            )
            ->where('genba_id', $activity_id)
            ->where('scope_id', $scope_id)
            ->where('check_item_id', $check_item_id);
        return $result;
    }
    public static function get_genba_activity_list($search, $status_id)
    {
        $my_id = Auth::user()->username;
        $my_name = Auth::user()->fullname;
        $result = DB::connection('sqlsrv')->table('GenbaProcAudit as a')
            ->join('GenbaCategory as b ', 'b.SysID', '=', 'a.Category_id')
            ->select(
                'a.SysID',
                'a.Date',
                'a.process',
                'a.station',
                'a.Area_checked',
                'a.Auditor',
                'a.Category_id',
                'b.Description as category'
            );
        if (!empty($search)) {
            $result = $result->where('Date', 'LIKE', "%$search%");
        }
        if ($my_id != '270723-001' && $my_id != '260422-001') {
            $result = $result->where('a.Auditor', $my_name);
        }

        if ($status_id == 4) {
            $result = $result->where('a.status', 4);
        } else if ($status_id == 3) {
            $result = $result->where('a.status', 3);
        }

        return $result->where('IsDelete', 0);
    }
    public static function get_genba_verifiaction_list($search, $date_from = null, $date_to = null, $auditor = null)
    {
        $result = DB::connection('sqlsrv')->table('GenbaProcAudit as a')
            ->join('GenbaCategory as b ', 'b.SysID', '=', 'a.Category_id')
            // ->where('a.status', 3)
            ->select(
                'a.SysID',
                'a.Date',
                'a.process',
                'a.station',
                'a.Area_checked',
                'a.Auditor',
                'a.Category_id',
                'b.Description as category',
                // DB::raw("FORMAT(a.Date, 'ddMMyy') + '-' + CAST(a.SysID AS VARCHAR(20)) as DocNum")
                )
            ->where('IsDelete', 0);
        
        // Filter berdasarkan range tanggal
        if (!empty($date_from) && !empty($date_to)) {
            $result->where(DB::raw('CAST(a.Date AS DATE)'), '>=', $date_from)->where(DB::raw('CAST(a.Date AS DATE)'), '<=', $date_to);
        } elseif (!empty($date_from)) {
            $result->where(DB::raw('CAST(a.Date AS DATE)'), '>=', $date_from);
        } elseif (!empty($date_to)) {
            $result->where(DB::raw('CAST(a.Date AS DATE)'), '<=', $date_to);
        }
        
        // Filter berdasarkan auditor
        if (!empty($auditor)) {
            $result->where('a.Auditor', 'LIKE', '%' . $auditor . '%');
        }
        
        return $result;
    }

    public static function get_genba_area()
    {
       
        $result = DB::connection('sqlsrv')->table('Genba_Area')
            ->select('SysID', 'Area_name', 'Process'); 
            
        return $result;
    }
    public static function get_user()
    {
        $names = [
            'RIZKY WISHNU PERMADHI',
            'BUDI HARTO',
            'PUDJIYANTO',
            'ADI PRIYANTO',
            'Dedy Liu',
            'Dodi Kuncoro',
            'Ristiyono',
            'ADRIAN HARISUSILO'
        ];
        $result = DB::connection('sqlsrv')->table('users')
            ->select('username', 'full_name')
            // ->whereIn('full_name',$names)
            ->orderBy('full_name', 'asc');
        return $result;
    }
    public static function get_section_list()
    {
        $result = DB::connection('sqlsrv')->table('GenbaDept')
            ->select('Key1 as id', 'Desc as desc')
            ->where('CheckBox01', 1);
        return $result;
    }
    #endregion
    #region GenbaMng
    public static function get_genba_mng_activity_list($search, $date_from = null, $date_to = null, $auditor = null)
    {
        $my_id = Auth::user()->username;
        $qems = ['121020-002', '031114-001', '260422-001'];
        $result = DB::connection('sqlsrv')->table('GenbaProcAuditDtl as a')
            ->leftJoin('GenbaProcAudit as b', 'b.SysID', '=', 'a.genba_id')
            ->join('GenbaCategory as c', 'c.SysID', '=', 'b.Category_id')
            ->select(
                'a.SysID',
                'b.Date',
                'b.Area_Checked',
                'a.findings',
                'a.Path',
                'a.asign_to',
                'a.asign_to_dept',
                'a.priority',
                'a.area_detail',
                'a.corrective_action',
                'a.evidence',
                'a.status',
                'a.due_date',
                'a.complete_date',
                'a.execution_comment',
                'a.execution_path',
                'a.verification_result',
                'a.status',
                'b.Auditor',
                DB::raw("FORMAT(b.Date, 'ddMMyy') + '-' + CAST(a.SysID AS VARCHAR(20)) as DocNum")
            )
            ->where('b.IsDelete', 0)
            ->orderBy('a.created_at', 'DESC')
            ->where('a.result', '!=', 1)
            ->where('IsDelete', 0);
        
        // Filter berdasarkan range tanggal
        if (!empty($date_from) && !empty($date_to)) {
            $result->where(DB::raw('CAST(b.Date AS DATE)'), '>=', $date_from)->where(DB::raw('CAST(b.Date AS DATE)'), '<=', $date_to);
        } elseif (!empty($date_from)) {
            $result->where(DB::raw('CAST(b.Date AS DATE)'), '>=', $date_from);
        } elseif (!empty($date_to)) {
            $result->where(DB::raw('CAST(b.Date AS DATE)'), '<=', $date_to);
        }
        
        // Filter berdasarkan auditor
        if (!empty($auditor)) {
            $result->where('b.Auditor', 'LIKE', '%' . $auditor . '%');
        }
        
        // if (!in_array($my_id, $qems)) {
        //     $result =  $result->where('a.asign_to', $my_id);
        // }
        return $result;
    }
    public static function get_genba_mng_activity($id)
    {
        $result = DB::connection('sqlsrv')->table('GenbaMngProcAudit as a')
            ->where('SysID', $id);
        return $result;
    }
    #endregion  

    public static function get_genba_execution_list($search = null)
    {
        $my_id = Auth::user()->username;

        $result = DB::table('GenbaProcAudit as a')
            ->join('GenbaCategory as b', 'b.SysID', '=', 'a.Category_id')
            ->join('GenbaProcAuditDtl as d', 'd.genba_id', '=', 'a.SysID')
            ->select(
                'a.SysID',
                'a.Date',
                'a.Area_Checked',
                'a.Auditor',
                'a.status',
                'a.Category_id',
                'b.Category as category',
                'b.Description as category_desc',
                DB::raw("CASE WHEN d.execution_comment IS NOT NULL AND d.execution_comment != '' THEN 1 ELSE Null END as corrective_action"),
                DB::raw("CASE WHEN d.execution_path IS NOT NULL AND d.execution_path != '' THEN 1 ELSE Null END as evidence"),
                DB::raw("CASE WHEN d.status IS NOT NULL AND d.status != '' THEN 1 ELSE Null END as close_status"),
                'a.status'
            )
            ->where('a.IsDelete', 0)
            ->whereNotNull('Path')
            ->where('a.status', 3);

        if (!empty($search)) {
            $result = $result->where('Scope_item', 'LIKE', "%$search%");
        }

        return $result;
    }

    public static function get_data_rusty($id_activity, $search)
    {
        $data = DB::table('GenbaProcAuditDtl')
            ->select(
                'genba_id',
                'scope_id',
                'check_item_id',
                'findings',
                'user_id',
                'asign_to',
                'asign_to_name',
                'asign_to_dept',
                'due_date',
                'verification_result',
                'Path'
            )
            ->where('genba_id', $id_activity);
        if (!empty($search)) {
            $data->where('asign_to_dept', $search);
        }
        return $data;
    }
    public static function get_genba_realisation_list($search, $genba_id)
    {
        $my_id = Auth::user()->username;
        $result = DB::table('GenbaProcAuditDtl as a')
            ->leftJoin('GenbaProcAudit as b', 'b.SysID', '=', 'a.genba_id')
            ->where('a.genba_id', '=', $genba_id)
            ->where('a.result', '<>', 1)
            ->select(
                'a.SysID',
                'b.auditor',
                'a.findings',
                'a.Path',
                'a.asign_to',
                'a.asign_to_dept',
                'a.priority',
                'a.area_detail',
                'a.corrective_action',
                'a.evidence',
                'a.status',
                'a.due_date',
                'a.complete_date',
                'a.execution_comment',
                'a.verification_result',
                'a.execution_path',

            );
        if (!empty($search)) {
            $result = $result->where('findings', 'LIKE', "%$search%");
        }
        return $result;
    }
    public static function get_genba_worksheet($search, $genba_id)
    {
        $my_id = Auth::user()->username;
        $qems = ['121020-002', '031114-001', '260422-001', '270723-001'];
        $result = DB::table('GenbaProcAuditDtl as a')
            ->where('a.genba_id', '=', $genba_id)
            ->select(
                'a.SysID',
                'a.findings',
                'a.Path',
                'a.asign_to',
                'a.asign_to_dept',
                'a.priority',
                'a.area_detail',
                'a.corrective_action',
                'a.evidence',
                'a.status'
            )
            ->where('a.result', '!=', 1);
        if (!in_array($my_id, $qems)) {
            $result =  $result->where('a.asign_to', $my_id);
        }
        if (!empty($search)) {
            $result = $result->where('findings', 'LIKE', "%$search%");
        }
        return $result;
    }
    public static function get_findings($SysID)
    {
        $result = DB::table('GenbaProcAuditDtl')
            ->select(
                'findings',
                'Path',
                'asign_to',
                'asign_to_dept',
                'priority',
                'area_detail',
                'corrective_action',
                'evidence',
                'status'
            )
            ->where('SysID', '=', $SysID);
        return $result;
    }
    public static function get_genba_mng_execution_list($search)
    {
        $my_id = Auth::user()->username;
        $result = DB::table('GenbaMngProcAuditDtl as A')
            ->join('GenbaMngProcAudit as p', 'p.SysID', '=', 'A.genba_id')
            ->join('GenbaMngLPAScope as B', 'B.SysID', '=', 'A.scope_id')
            ->leftjoin('GenbaMngAuditItem as C', function ($join) {
                $join->on('C.scope_id', '=', 'A.scope_id')
                    ->on('C.SysID', '=', 'A.check_item_id');
            })->where('A.asign_to',  $my_id)
            ->select(
                'A.SysID',
                'A.scope_id',
                'A.genba_id',
                'P.Date',
                'B.LPA_Scope',
                'P.Auditor',
                'C.Check_item',
                'p.Area_Checked',
                'A.result',
                'A.findings',
                'A.findings_img',
                'A.Path',
                'A.asign_to',
                'A.asign_to_name',
                'A.asign_to_dept'
            )->orderBy('P.Date', 'desc');
        if (!empty($search)) {
            $result = $result->where('LPA_Scope', 'LIKE', "%$search%");
        }
        return $result;
    }

    public static function get_findings_($SysID)
    {
        $result = DB::table('GenbaProcAuditDtl')
            ->select('findings', 'Path')
            ->where('SysID', '=', $SysID);
        return $result;
    }
    public static function get_findings_mng($SysID)
    {
        $result = DB::table('GenbaMngProcAuditDtl')
            ->select('findings', 'Path')
            ->where('SysID', '=', $SysID);
        return $result;
    }
}
