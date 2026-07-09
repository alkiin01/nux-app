<?php

namespace App\Http\Controllers;

use App\Models\AppModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\GenbaManagement;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class GenbaManagementController extends Controller
{
    //
    public function index()
    {
        $my_id = Auth::user()->id;
        $uri = explode("/", url()->current());
        if (count($uri) < 5) {
            $menu = $this->menu($my_id, 'Team');
        } else {
            $menu = $this->menu($my_id, $uri[4]);
        }
        $data['head_title'] = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'];
        $data['menu_level_2'] = $menu['menu_level_2'];
        $data['menu_level_3'] = $menu['menu_level_3'];
        $data['menu_level_4'] = $menu['menu_level_4'];
        $data['doc_access_list'] = DB::table('t100_user_doc_access')->where('user_id', $my_id)->get();
        return view('genba_management/setup/teams', $data);
    }

    public function add_team(Request $request)
    {
        if ($request->trc_unix_id == 0) {
            $data['test'] = '';
            return view('genba_management/setup/teams_form', $data);
        } else {

            $str_req = explode("_", $request->trc_unix_id);
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $id_team = $str[0];
            $data['test'] = '';
            return view('genba_management/setup/teams_form', $data);
        }
    }

    public function get_team_data(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $search = $request->front_table_search;
        $columns = array(
            0 => 'SysID',
            1 => 'name',
            2 => 'role',
            3 => ''
        );
        $totalData = GenbaManagement::get_team_list($search)->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column') == 0 ? $columns[1] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column') == 0 ? 'desc' : $request->input('order.0.dir'));
        if (empty($search)) {
            $posts = GenbaManagement::get_team_list($search)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $posts = GenbaManagement::get_team_list($search)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = GenbaManagement::get_team_list($search)->count();
        }
        $data = array();

        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $trc_id = Crypt::encryptString($post->SysID);

                $sys_id = str_replace("=", "-", $trc_id) . '_' . $no;
                $button = '<button type="button" title="Preview Team" class="btn btn-light-primary btn-sm" id="btn_form_view_doc_' . $sys_id . '" onclick="open_document(' . "'$post->name'" . ",'$post->role'" . ",'$sys_id'" . ')" style="text-align: center; width: 40px; height: 35px;">
                                <span id="svg_form_view_doc_' . $sys_id . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path>
                                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path>
                                </svg>
                                </span>
                                <span id="spinner_form_view_doc_' . $sys_id . '" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
                            </button>';
                $buttonDelete = '<button type="button" title="Delete Team" class="btn btn-light-danger btn-sm" id="btn_form_delete_doc_' . $sys_id . '" onclick="delete_document(' . "'$post->name'" . ",'$post->role'" . ",'$sys_id'" . ')" style="text-align: center; width: 40px; height: 35px;">
                                <span id="svg_form_delete_doc_' . $sys_id . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                    <defs/>
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                        <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                    </g>
                                </svg>
                                </span>
                                <span id="spinner_form_delete_doc_' . $sys_id . '" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
                            </button>';
                $TeamName = $post->name;
                $TeamRole = $post->role;
                $my_username = Auth::user()->username;
                $nestedData['no'] = $no;
                $nestedData['name'] = $TeamName;
                $nestedData['role'] = $TeamRole;
                $nestedData['action'] = $button . ' ' . $buttonDelete;
                $data[] = $nestedData;
            }
        } else {

            $nestedData['no'] = '';
            $nestedData['name'] = '';
            $nestedData['role'] = '';
            $nestedData['action'] = '';
            $data[] = $nestedData;
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }

    public function InsertTeam(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);

        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $id_team = $str[0];
        } else {
            $id_team = $str_req[0];
        }
        $TeamName = $request->input('team_name');
        $TeamRole = $request->input('team_role');
        $count = GenbaManagement::get_genba_teams($TeamName, $TeamRole, $id_team);
        if ($count->Count() > 0) {
            $insert = GenbaManagement::insert_genba_teams($TeamName, $TeamRole, $id_team);
        } else {
            $insert = GenbaManagement::insert_genba_teams($TeamName, $TeamRole, $id_team);
        }
        if ($insert) {
            $data["code"] = 200;
        } else {
            $data["code"] = 500;
        }
        return $data;
    }
    public function delete_document(Request $request)
    {
        $TeamName = $request->input('team_name');
        $TeamRole = $request->input('team_role');
        $str_req = explode("_", $request->trc_unix_id);
        $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
        $id_team = $str[0];
        $delete = GenbaManagement::delete_teams($TeamName, $TeamRole, $id_team);
    }

    public function get_member_team(Request $request)
    {
        $search = $request->front_table_search;
        $columns = array(
            0 => 'SysID',
            1 => 'name',
            2 => 'role',
            3 => ''
        );
        $totalData = GenbaManagement::get_team_member_list($search)->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column') == 0 ? $columns[1] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column') == 0 ? 'desc' : $request->input('order.0.dir'));
        if (empty($search)) {
            $posts = GenbaManagement::get_team_member_list($search)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $posts = GenbaManagement::get_team_member_list($search)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = GenbaManagement::get_team_member_list($search)->count();
        }
        $data = array();

        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $trc_id = Crypt::encryptString($post->SysID);
                $sys_id = str_replace("=", "-", $trc_id) . '_' . $no;
                $button = '<button type="button" title="Remove Member" class="btn btn-light-danger btn-sm" id="btn_form_delete_doc_' . $sys_id . '" onclick="delete_document(' . "'$post->team_id'" . ",'$post->user_id'" . ",'$sys_id'" . ')" style="text-align: center; width: 40px; height: 35px;">
                    <span id="svg_form_delete_doc_' . $sys_id . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                    <defs/>
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                        <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                    </g>
                                </svg>
                                </span>
                    <span id="spinner_form_delete_doc_' . $sys_id . '" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
                            </button>';

                $my_username = Auth::user()->username;
                $nestedData['no'] = $no;
                $nestedData['nik'] = $post->username;
                $nestedData['full_name'] = $post->full_name;
                $nestedData['action'] = $button;
                $data[] = $nestedData;
            }
        } else {

            $nestedData['no'] = '';
            $nestedData['nik'] = '';
            $nestedData['full_name'] = '';
            $nestedData['action'] = '';
            $data[] = $nestedData;
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }
    public function add_member_team(Request $Request)
    {
    }
    #region GenbaSchedule
    public function schedule()
    {
        $my_id = Auth::user()->id;
        $uri = explode("/", url()->current());
        if (count($uri) < 5) {
            $menu = $this->menu($my_id, 'Schedule');
        } else {
            $menu = $this->menu($my_id, $uri[4]);
        }
        $data['head_title'] = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'];
        $data['menu_level_2'] = $menu['menu_level_2'];
        $data['menu_level_3'] = $menu['menu_level_3'];
        $data['menu_level_4'] = $menu['menu_level_4'];
        $data['doc_access_list'] = DB::table('t100_user_doc_access')->where('user_id', $my_id)->get();
        return view('genba_management/setup/genba_schedule', $data);
    }
    public function createSchedule(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);

        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $sysID = $str[0];
        } else {
            $sysID = $str_req[0];
        }
        $title = $request->input('title');
        $event_type = $request->input('event_type');
        $description = $request->input('description');
        $genba_date = $request->input('genba_date');
        $end_date = $request->input('end_date');
        $execution_date = $request->input('execution_date');
        $execution_status = 'Pending';
        $location = $request->input('location');
        $color = $request->input('color');
        $status = $request->input('status');
        $insert = GenbaManagement::add_genba_schedule($title, $event_type, $status, $description, $genba_date, $end_date, $execution_date, $execution_status, $location, $color);
        if ($insert) {
            $data["code"] = 200;
        } else {
            $data["code"] = 500;
        }
        return $data;
    }
    public function get_schedule_by_id(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
        $sysID = $str[0];
        $data = GenbaManagement::get_genba_schedule_by_id($sysID)->get();
        return $data;
    }
    public function get_schedule()
    {
        $data = GenbaManagement::get_genba_schedule()->get()->map(function ($item) {
            // $trc_id = Crypt::encryptString($item->po_num);
            // $sys_id = "'" . str_replace("=", "-", $trc_id);
            return [
                'id' => str_replace("=", "-", Crypt::encryptString($item->SysID)),
                'title' => $item->title,
                'start' => Carbon::parse($item->genba_date)->format('Y-m-d\TH:i:s'),
                'description' => $item->description,
                'end' => Carbon::parse($item->end_date)->format('Y-m-d\TH:i:s'),
                'location' => $item->location,
                'className' => $item->color,
            ];
        });
        echo json_encode($data);
    }
    public function genba_activity()
    {
        $my_id = Auth::user()->id;
        $my_name = Auth::user()->full_name;
        $uri = explode("/", url()->current());
        if (count($uri) < 5) {
            $menu = $this->menu($my_id, 'Activity');
        } else {
            $menu = $this->menu($my_id, $uri[4]);
        }
        $data['head_title'] = $menu['head_title'];
        $data['my_name'] = $my_name;
        $data['menu_level_1'] = $menu['menu_level_1'];
        $data['menu_level_2'] = $menu['menu_level_2'];
        $data['menu_level_3'] = $menu['menu_level_3'];
        $data['menu_level_4'] = $menu['menu_level_4'];
        $data['doc_access_list'] = DB::table('t100_user_doc_access')->where('user_id', $my_id)->get();
        return view('genba_management/activity/genba_activity', $data);
    }
    public function genba_form(Request $request)
    {
        if ($request->trc_unix_id == 0) {
            $data['test'] = '';
            return view('genba_management/activity/activity_form', $data);
        } else {

            $str_req = explode("_", $request->trc_unix_id);
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $id_team = $str[0];
            $data['test'] = '';
            return view('genba_management/activity/activity_form', $data);
        }
    }

    public function add_genba(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);

        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $sysID = $str[0];
        } else {
            $sysID = $str_req[0];
        }

        $area_checked = $request->input('area_checked');
        $date = $request->input('date');
        $auditor = $request->input('auditor');
        $category = $request->input('genba_category');
        $station = $request->input('station');
        $process = $request->input('process');

        $insert = GenbaManagement::add_genba_activity($area_checked, $auditor, $category, $date, $sysID, $station, $process);

        if ($insert > 0) {
            $db = DB::table('GenbaProcAudit')
                ->where('SysID', $insert)
                ->select('SysID', 'Category_id');

            if ($db->count() > 0) {
                $category_id = $db->first()->Category_id;

                if ($category_id == 4) {
                    return $this->add_genba_rusty($db->first()->SysID);
                }

                $db_activity = DB::table('GenbaProcAuditDtl')
                    ->where('SysID', $insert)
                    ->select('SysID');

                $data["code"] = 200;
                $data["id_activity"] = $db->first()->SysID;
                $data["process"] = $process . '-' . $station;

                $dbScopes = DB::table('GenbaAuditItem as b')
                    ->leftJoin('GenbaProcAuditDtl as c', function ($join) use ($insert) {
                        $join->on('b.SysID', '=', 'c.check_item_id')
                            ->where('c.genba_id', '=', $insert);
                    })
                    ->leftJoin('GenbaProcAudit as d', function ($join) {
                        $join->on('d.SysID', '=', 'c.genba_id')
                            ->where('d.IsDelete', '=', 0);
                    })
                    ->where('b.Category', '=', $category_id)
                    ->select(
                        'b.scope_id as scope_id',
                        'b.scope_item',
                        'b.SysID as check_item_id',
                        'b.Photos as foto',
                        'b.Check_item',
                        'b.Check_item_eng',
                        'c.result as Hasil',
                        'c.Path'
                    )
                    ->get();

                $scopes = [];
                foreach ($dbScopes as $item) {
                    $scopes[$item->scope_item][] = [
                        'scope_id' => $item->scope_id,
                        'check_item_id' => $item->check_item_id,
                        'check_item' => $item->Check_item,
                        'check_item_eng' => $item->Check_item_eng,
                        'foto' => $item->foto,
                        'result' => $item->Hasil,
                        'photo' => $item->Path
                    ];
                }

                $data["scopes"] = $scopes;
                return view('genba_management/activity/activity_form', $data);
            }
        } else {
            $data["code"] = 500;
        }
        return $data;
    }

    public function add_genba_rusty($id_activity)
    {
        $audit = DB::table('GenbaProcAudit')->where('SysID', $id_activity)->first();

        if (!$audit) {
            abort(404, 'Data audit dengan ID tersebut tidak ditemukan.');
        }
        $category_id = $audit->Category_id;
        $dbScopes = DB::table('GenbaAuditItem as b')
            ->leftJoin('GenbaProcAuditDtl as s', 'b.scope_id', '=', 's.SysID')
            ->where('b.Category', $category_id)
            ->select('b.SysID as check_item_id', 'b.scope_id')
            ->select(
                'b.scope_id as scope_id',
                'b.scope_item'
            )
            ->get();

        $scopes = [];
        foreach ($dbScopes as $item) {
            $scopes[$item->scope_item][] = [
                'scope_id' => $item->scope_id,
            ];
        }

        return view('genba_management/activity/activity_rusty', [
            'id_activity' => $id_activity,
            'scopes' => $scopes
        ]);
    }
    public function finish_activity(Request $request)
    {
        $id_activity = $request->input('id_activity');
        DB::table('GenbaProcAudit')
            ->where('SysID', $id_activity)
            ->update([
                'status' => 3,
                'updated_at' => now(),
            ])
        ;
        return response()->json([
            'code' => 200,
            'message' => 'Data berhasil disimpan!',
        ]);
    }

    public function upload_photo(Request $request)
    {
        $my_id = Auth::user()->username;
        $due_date = Carbon::now()->addWeeks(2)->format('Y-m-d');
        $scope_id = $request->input('scope_id');

        $photoPaths = [];

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $filename = uniqid() . '_' . time() . '.' . $photo->getClientOriginalExtension();
                $photo->storeAs('public/photos', $filename);
                $photoPaths[] = 'photos/' . $filename;
            }
        }

        $assignToUser = DB::table('users')
            ->where('username', $request->assign_to)
            ->first();

        if (!$assignToUser) {
            return response()->json([
                'code' => 404,
                'message' => 'User dengan NPK/Username tersebut tidak ditemukan.'
            ], 404);
        }

        $assignToDeptModel = DB::connection('sqlsrv')
            ->table('GenbaDept')
            ->where('Key1', $request->assign_to_dept)
            ->first();

        if (!$assignToDeptModel) {
            return response()->json([
                'code' => 500,
                'message' => 'Departemen tidak ditemukan.'
            ], 500);
        }

        $photoPathString = implode(',', $photoPaths);

        $lastCheckItem = DB::table('GenbaProcAuditDtl')
            ->where('genba_id', $request->activity_id)
            ->max('check_item_id');

        $check_item_id = $lastCheckItem ? $lastCheckItem + 1 : 1;

        DB::table('GenbaProcAuditDtl')->insert([
            'user_id' => $my_id,
            'result' => 3,
            'scope_id' => $scope_id,
            'genba_id' => $request->activity_id,
            'check_item_id' => $check_item_id,
            'Path' => $photoPathString,
            'due_date' => $due_date,
            'findings' => $request->findings,
            'asign_to' => $assignToUser->username,
            'asign_to_name' => $assignToUser->full_name,
            'asign_to_dept' => $assignToDeptModel->Key1,
            'asign_to_dept_name' => $assignToDeptModel->Desc,
            'area_detail' => $request->detail_area_rusty,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'code' => 200,
            'message' => 'Data finding berhasil disimpan!',
            'path' => $photoPathString,
            'check_item_id' => $check_item_id,
        ]);
    }



    public function get_data_rusty(Request $request)
    {
        $search = $request->front_table_search;
        $id_activity = $request->input('id_activity');


        $columns = array(
            0 => 'genba_id',
            1 => 'genba_id',
            2 => 'findings',
            3 => 'asign_to_dept',
            4 => 'asign_to_name',
            5 => 'due_date',
            6 => 'verification_result',
            7 => 'action',

        );
        $totalData = GenbaManagement::get_data_rusty($id_activity, $search)->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column') == 0 ? $columns[1] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column') == 0 ? 'desc' : $request->input('order.0.dir'));
        if (empty($search)) {
            $posts = GenbaManagement::get_data_rusty($id_activity, $search)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $posts = GenbaManagement::get_data_rusty($id_activity, $search)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = GenbaManagement::get_data_rusty($id_activity, $search)->count();
        }
        $data = array();

        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $nestedData['no'] = $no;
                $nestedData['genba_id'] = $post->genba_id;
                $nestedData['check_item_id'] = $post->check_item_id;
                $nestedData['findings'] = $post->findings;
                $nestedData['user_id'] = $post->user_id;
                $nestedData['asign_to_dept'] = $post->asign_to_dept;
                $nestedData['asign_to_name'] = $post->asign_to_name;
                $nestedData['due_date'] = $post->due_date;
                $nestedData['Path'] = $post->Path;

                if (is_null($post->verification_result)) {
                    $nestedData['verification_result'] = 'OPEN';
                } elseif ($post->verification_result == 1) {
                    $nestedData['verification_result'] = 'CLOSE';
                } else {
                    $nestedData['verification_result'] = $post->verification_result;
                }

                $sys_id = $post->check_item_id;
                $nestedData['action'] = '<button type="button" 
                                    title="Delete Data" 
                                    class="btn btn-light-danger btn-sm" 
                                    id="btn_form_delete_rusty_' . $sys_id . '" 
                                    onclick="delete_document_rusty(\'' . $sys_id . '\')" 
                                    style="text-align: center; width: 40px; height: 35px;">
                                        <span id="svg_form_delete_rusty_' . $sys_id . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24"/>
                                                    <path d="M6,8 L18,8 L17.106535,19.6150447 
                                                    C17.04642,20.3965405 16.3947578,21 
                                                    15.6109533,21 L8.38904671,21 
                                                    C7.60524225,21 6.95358004,20.3965405 
                                                    6.89346498,19.6150447 L6,8 Z 
                                                    M8,10 L8.45438229,14.0894406 
                                                    L15.5517885,14.0339036 L16,10 
                                                    L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                                    <path d="M14,4.5 L14,3.5 
                                                    C14,3.22385763 13.7761424,3 
                                                    13.5,3 L10.5,3 
                                                    C10.2238576,3 10,3.22385763 
                                                    10,3.5 L10,4.5 
                                                    L5.5,4.5 C5.22385763,4.5 
                                                    5,4.72385763 5,5 
                                                    L5,5.5 C5,5.77614237 
                                                    5.22385763,6 5.5,6 
                                                    L18.5,6 C18.7761424,6 
                                                    19,5.77614237 19,5.5 
                                                    L19,5 C19,4.72385763 
                                                    18.7761424,4.5 18.5,4.5 
                                                    L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                                </g>
                                            </svg>
                                        </span>
                                        <span id="spinner_form_delete_rusty_' . $sys_id . '" 
                                              class="spinner-border spinner-border-sm svg-icon svg-icon-1x" 
                                              style="display: none;"></span>  
                                </button>';

                $data[] = $nestedData;
            }
        } else {
            $nestedData['no'] = '';
            $nestedData['genba_id'] = '';
            $nestedData['findings'] = '';
            $nestedData['user_id'] = '';
            $nestedData['asign_to_dept'] = '';
            $nestedData['asign_to_name'] = '';
            $nestedData['due_date'] = '';
            $nestedData['Path'] = '';
            $nestedData['verification_result'] = 'OPEN';
            $data[] = $nestedData;
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }


    public function post_form_spv(Request $request)
    {
        $my_id = Auth::user()->username;
        $id_activity = $request->input('activity_id');
        $scope_id = $request->input('scope_id');
        $check_item_id = $request->input('check_item_id');
        $result = $request->input('answer');
        $check_date = GenbaManagement::check_date_activity($id_activity);
        foreach ($check_date->get() as $d) {
            $date = $d->Date;
        }
        $due_date = Carbon::parse($date)->addWeeks(2)->format('Y-m-d');
        ;
        $insert = GenbaManagement::save_genba_activity_detail($my_id, $id_activity, $scope_id, $check_item_id, $result, $due_date);
        if ($insert) {
            $data['code'] = 200;
            $data['check_item_id'] = $check_item_id;
            $data['check_item_id'] = $check_item_id;
            $data['message'] = 'Data berhasil disimpan';
            $data['result'] = $result;
        } else {
            $data['code'] = 500;
            $data['check_item_id'] = $check_item_id;
            $data['message'] = 'Data gagal disimpan';
            $data['result'] = '';
        }
        return json_encode($data);
    }
    public function post_photo_spv(Request $request)
    {
        $my_id = Auth::user()->username;
        $id_activity = $request->input('activity_id');
        $scope_id = $request->input('scope_id');
        $check_item_id = $request->input('check_item_id');
        $findings = $request->input('findings');
        $photo = $request->input('photos', []);
        $asign_to = $request->input('asign_to');
        $asign_to_name = $request->input('asign_to_name');
        $asign_to_dept_name = $request->input('asign_to_dept_name');
        $asign_to_dept = $request->input('asign_to_dept');
        $detail_area = $request->input('detail_area');
        $photoPaths = [];
        $dataphoto = $request->input('dataphoto', []);
        if ($dataphoto == null) {
            $check = DB::table('GenbaProcAuditDtl')
                ->where('genba_id', $id_activity)
                ->where('scope_id', $scope_id)
                ->where('check_item_id', $check_item_id)->select('Path')->get();
            foreach ($check as $c) {
                if ($c->Path != '') {
                    $photoPathsString = $c->Path;
                    $path_asli = explode(',', $c->Path);
                } else {
                    return response()->json([
                        'message' => 'Foto gagal disimpan.',
                        'photos' => ''
                    ]);
                }
            }
        } else {
            foreach ($dataphoto as $index => $image) {
                if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
                    $image = substr($image, strpos($image, ',') + 1);
                    $type = strtolower($type[1]);
                    if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                        continue;
                    }
                    $imageData = base64_decode($image);
                    $imageName = uniqid() . '_' . time() . ".{$type}";
                    $path = 'photos/' . $imageName;
                    Storage::disk('public')->put($path, $imageData);
                    $photoPaths[] = $path;
                } elseif ($image instanceof \Illuminate\Http\UploadedFile) {
                    $imageName = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('photos', $imageName, 'public');
                    $photoPaths[] = $path;
                }
            }
        }
        if ($photoPaths != null) {
            $photoPathsString = implode(',', $photoPaths);
        }
        $updates = DB::table('GenbaProcAuditDtl')
            ->where('genba_id', $id_activity)
            ->where('scope_id', $scope_id)
            ->where('check_item_id', $check_item_id)
            ->update([
                'Path' => $photoPathsString,
                'findings' => $findings,
                'asign_to' => $asign_to,
                'asign_to_name' => $asign_to_name,
                'asign_to_dept' => $asign_to_dept,
                'asign_to_dept_name' => $asign_to_dept_name,
                'area_detail' => $detail_area,
                'updated_at' => Carbon::now()
            ]);

        if ($updates) {
            return response()->json([
                'message' => 'Foto berhasil disimpan.',
                'photos' => $photoPaths  // Mengembalikan daftar path foto yang disimpan
            ]);
        } else {
            return response()->json([
                'message' => 'Foto gagal disimpan.',
                'photos' => ''  // Mengembalikan daftar path foto yang disimpan
            ]);
        }
    }
    public function get_data_photo(Request $request)
    {
        $activity_id = $request->input('activity_id');
        $scope_id = $request->input('scope_id');
        $check_item_id = $request->input('check_item_id');
        $db = GenbaManagement::get_genba_activity_detail($activity_id, $scope_id, $check_item_id);
        if ($db->count() == 0) {
            $data['photo'] = '';
            $data['findings'] = '';
            $data['asign_to'] = '';
            $data['asign_to_name'] = '';
            $data['asign_to_dept'] = '';
            $data['asign_to_dept_name'] = '';
            $data['priority'] = '';
            $data['area_detail'] = '';
            echo json_encode($data);
        } else {
            $dt = $db->get();
            foreach ($dt as $item) {
                $photo = explode(',', $item->Path);
                $data['photo'] = $photo;
                $data['findings'] = $item->findings;
                $data['asign_to'] = $item->asign_to;
                $data['asign_to_name'] = $item->asign_to_name;
                $data['asign_to_dept'] = $item->asign_to_dept;
                $data['asign_to_dept_name'] = $item->asign_to_dept_name;
                $data['priority'] = $item->priority;
                $data['area_detail'] = $item->area_detail;
            }
            echo json_encode($data);
        }
    }
    public function submit_form_genba(Request $request)
    {
        $activity_id = $request->input('genba_id');
        $insert = DB::table('GenbaProcAudit')
            ->where('SysID', $activity_id)
            ->update([
                'status' => 3,
                'updated_at' => Carbon::now()
            ]);
        if ($insert) {
            $data['code'] = 200;
            $data['message'] = 'Data berhasil disimpan';
        } else {
            $data['code'] = 500;
            $data['message'] = 'Data gagal disimpan';
        }
        return json_encode($data);
    }
    public function post_photo_mng(Request $request)
    {
        $my_id = Auth::user()->username;
        $id_activity = $request->input('activity_id');
        $scope_id = $request->input('scope_id');
        $check_item_id = $request->input('check_item_id');
        $findings = $request->input('findings');

        $photo = $request->input('photos', []); // Mendapatkan data foto yang dikirimkan
        $photoPaths = []; // Inisialisasi array untuk menampung path foto

        // Proses gambar yang diambil dengan kamera (Base64)
        foreach ($photo as $image) {
            if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
                $image = substr($image, strpos($image, ',') + 1);
                $type = strtolower($type[1]);

                if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                    continue;
                }

                $imageData = base64_decode($image);
                $imageName = uniqid() . '_' . time() . ".{$type}";
                $path = 'photos/' . $imageName;

                Storage::disk('public')->put($path, $imageData);
                $photoPaths[] = $path;
            }
            // Proses gambar yang diupload via input file (bukan base64)
            elseif ($image instanceof \Illuminate\Http\UploadedFile) {
                $imageName = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('photos', $imageName, 'public');
                $photoPaths[] = $path; // Menyimpan path gambar file ke array
            }
        }
        $photoPathsString = implode(',', $photoPaths);
        DB::table('GenbaMngProcAuditDtl')
            ->where('genba_id', $id_activity)
            ->where('scope_id', $scope_id)
            ->where('check_item_id', $check_item_id)
            ->update([
                'Path' => $photoPathsString,
                'findings' => $findings,
                'updated_at' => Carbon::now()
            ]);

        return response()->json([
            'message' => 'Foto berhasil disimpan.',
            'photos' => $photoPaths
        ]);
    }

    public function view_table_spv()
    {
        $my_id = Auth::user()->id;
        $uri = explode("/", url()->current());
        if (count($uri) < 5) {
            $menu = $this->menu($my_id, 'genba_management');
        } else {
            $menu = $this->menu($my_id, $uri[4]);
        }
        $data['head_title'] = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'];
        $data['menu_level_2'] = $menu['menu_level_2'];
        $data['menu_level_3'] = $menu['menu_level_3'];
        $data['menu_level_4'] = $menu['menu_level_4'];
        // $countDraft = DB::table('GenbaProcAudit')
        //     ->where('status', 4)
        //     ->orWhere('status', null)
        //     ->where('created_by', $my_id)
        //     ->count();
        // $countSubmit = DB::table('GenbaProcAudit')
        //     ->where('status', 3)
        //     ->where('created_by', $my_id)
        //     ->count();
        $data['doc_access_list'] = DB::table('t100_user_doc_access')->where('user_id', $my_id)->get();
        return view('genba_management/activity/spv_view_table', $data);
    }
    public function front_table(Request $request)
    {
        $search = $request->front_table_search;
        $status_id = $request->status_id;
        $columns = array(
            0 => 'SysID',
            1 => 'date',
            2 => 'process',
            3 => 'station',
            4 => 'Area_checked',
            5 => 'auditor',
            6 => 'category',
        );
        $totalData = GenbaManagement::get_genba_activity_list($search, $status_id)->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column') == 0 ? $columns[0] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column') == 0 ? 'desc' : $request->input('order.0.dir'));
        if (empty($search)) {
            $posts = GenbaManagement::get_genba_activity_list($search, $status_id)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $posts = GenbaManagement::get_genba_activity_list($search, $status_id)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = GenbaManagement::get_genba_activity_list($search, $status_id)->count();
        }
        $data = array();
        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $trc_id = Crypt::encryptString($post->SysID);
                $sys_id = "'" . str_replace("=", "-", $trc_id) . '_' . $no . "'";
                $button = '<button type="button" title="Preview" class="btn btn-light-primary btn-sm" id="btn_form_view_doc_' . $no . '" onclick="document_preview(' . $sys_id . ',' . $no . ')" style="text-align: center; width: 40px; height: 35px;">
                                <span id="svg_form_view_doc_' . $no . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path>
                                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path>
                                </svg>
                                </span>
                                <span id="spinner_form_view_doc_' . $no . '" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
                            </button>';
                $buttonDelete = '<button type="button" title="Delete Team" class="btn btn-light-danger btn-sm" id="btn_form_delete_doc_' . $no . '" onclick="delete_document(' . $sys_id . ',' . $no . ')" style="text-align: center; width: 40px; height: 35px;">
                                <span id="svg_form_delete_doc_' . $no . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                    <defs/>
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                        <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                    </g>
                                </svg>
                                </span>
                                <span id="spinner_form_delete_doc_' . $no . '" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
                            </button>';
                $area_checked = $post->Area_checked;
                $auditor = $post->Auditor;
                $date = AppModel::local_date_formate_name(substr($post->Date, 0, 10));

                $nestedData['no'] = $no;
                $nestedData['area_checked'] = $area_checked;
                $nestedData['auditor'] = $auditor;
                $nestedData['date'] = $date;
                $nestedData['process'] = $post->process;
                $nestedData['station'] = $post->station;
                $nestedData['category_id'] = $post->Category_id;
                $nestedData['category'] = $post->category;
                $nestedData['action'] = $button . ' ' . $buttonDelete;
                $data[] = $nestedData;
            }
        } else {

            $nestedData['no'] = '';
            $nestedData['area_checked'] = '';
            $nestedData['auditor'] = '';
            $nestedData['date'] = '';
            $nestedData['process'] = '';
            $nestedData['station'] = '';
            $nestedData['category_id'] = '';
            $nestedData['category'] = '';
            $nestedData['action'] = '';
            $data[] = $nestedData;
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }
    public function form_genba_activity(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);

        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $sysID = $str[0];
        } else {
            $sysID = $str_req[0];
        }
        $data = GenbaManagement::get_genba_activity($sysID)->get();
        $count = GenbaManagement::get_genba_activity($sysID)->count();
        $form_genba = [];
        if ($count > 0) {
            foreach ($data as $d) {
                $form_genba['area_checked'] = $d->Area_Checked;
                $form_genba['auditor'] = $d->Auditor;
                $form_genba['date'] = $d->Date;
                $form_genba['process'] = $d->process;
                $form_genba['station'] = $d->station;
                $form_genba['category_id'] = $d->Category_id;
                $form_genba['category'] = $d->category . ' - ' . $d->category_desc;
                // $form_genba['note'] = $d->Note;
            }
        } else {
            $form_genba['area_checked'] = '';
            $form_genba['auditor'] = '';
            $form_genba['date'] = '';
            $form_genba['process'] = '';
            $form_genba['station'] = '';
            $form_genba['category_id'] = 0;
            $form_genba['category'] = '';
            // $form_genba['note'] = '';
        }
        $form_genba['trc_unix_id'] = $request->trc_unix_id;
        $form_genba['head_title'] = "Genba Activity";

        return view('genba_management/activity/genba_activity', $form_genba);
    }
    public function get_genba_category(Request $request)
    {
        $search = $request->search;
        $page = $request->post('page', 1);
        $pageSize = 10;
        $query = GenbaManagement::get_genba_category();
        if ($search) {
            $query->where('Description', 'LIKE', '%' . $search . '%');
        }
        $categories = $query->paginate($pageSize, ['*'], 'page', $page);
        return response()->json([
            'items' => $categories->map(function ($categories) {
                return [
                    'id' => $categories->SysID,
                    'name' => $categories->Category . '-' . $categories->Description
                ];
            }),
            'pagination' => [
                'more' => $categories->hasMorePages(),
            ]
        ]);
    }
    public function get_genba_area(Request $request)
    {
        $search = $request->search;
        $processFilter = $request->process;
        $page = $request->post('page', 1);
        $pageSize = 10;

        $query = GenbaManagement::get_genba_area();

        if (!empty($processFilter)) {
            $query->where('Process', $processFilter);
        }

        if ($search) {
            $query->where('Area_name', 'LIKE', '%' . $search . '%');
        }

        $areas = $query->paginate($pageSize, ['*'], 'page', $page);

        return response()->json([
            'items' => $areas->map(function ($area) {
                return [
                    'id' => $area->SysID,
                    'name' => $area->Area_name
                ];
            }),
            'pagination' => [
                'more' => $areas->hasMorePages(),
            ]
        ]);
    }
    public function delete_genba(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
        $sysID = $str[0];
        $delete = GenbaManagement::delete_genba_activity($sysID);
        if ($delete == 1) {
            $data["code"] = 200;
        } else {
            $data["code"] = 500;
        }
        return json_encode($data);
    }

    public function view_table_mng()
    {

        $my_id = Auth::user()->id;
        $uri = explode("/", url()->current());
        if (count($uri) < 5) {
            $menu = $this->menu($my_id, 'genba_mng_management');
        } else {
            $menu = $this->menu($my_id, $uri[4]);
        }
        $data['head_title'] = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'];
        $data['menu_level_2'] = $menu['menu_level_2'];
        $data['menu_level_3'] = $menu['menu_level_3'];
        $data['menu_level_4'] = $menu['menu_level_4'];
        $data['doc_access_list'] = DB::table('t100_user_doc_access')->where('user_id', $my_id)->get();
        return view('genba_management/activity/mng_view_table', $data);
    }

    public function front_mng_table(Request $request)
    {
        $search = $request->front_table_search;
        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $auditor = $request->auditor;
        $columns = array(
            0 => 'SysID',
            1 => 'Area_Checked',
            2 => 'auditor',
            3 => 'date',
            4 => 'status',
            5 => 'action'
        );
        $totalData = GenbaManagement::get_genba_mng_activity_list($search, $date_from, $date_to, $auditor)->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column') == 0 ? $columns[0] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column') == 0 ? 'desc' : $request->input('order.0.dir'));
        if (empty($search)) {
            $posts = GenbaManagement::get_genba_mng_activity_list($search, $date_from, $date_to, $auditor)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $posts = GenbaManagement::get_genba_mng_activity_list($search, $date_from, $date_to, $auditor)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = GenbaManagement::get_genba_mng_activity_list($search, $date_from, $date_to, $auditor)->count();
        }
        $data = array();
        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $trc_id = Crypt::encryptString($post->SysID);
                $sys_id = "'" . str_replace("=", "-", $trc_id) . '_' . $no . "'";
                $button = '<button type="button" title="Preview" class="btn btn-light-primary btn-sm" id="btn_form_view_doc_' . $no . '" onclick="document_preview(' . $sys_id . ',' . $no . ')" style="text-align: center; width: 40px; height: 35px;">
                                <span id="svg_form_view_doc_' . $no . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path>
                                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path>
                                </svg>
                                </span>
                                <span id="spinner_form_view_doc_' . $no . '" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
                                <button type="button" title="Delete" class="btn btn-light-danger btn-sm mx-2" 
    id="btn_f_genba_conform_delete_' . $no . '" 
    onclick="f_genba_conform_delete(' . $sys_id . ',' . $no . ')" 
    style="text-align: center; width: 40px; height: 35px;">
    
    <span id="icon_f_genba_conform_delete_' . $no . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path opacity="0.3" d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
            <path d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V7H5V5Z" fill="currentColor"/>
            <path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
        </svg>
    </span>
    
    <span id="loader_f_genba_conform_delete_' . $no . '" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>
</button>
                            ';

                $date = AppModel::local_date_formate_name(substr($post->Date, 0, 10));
                $corrective_action = $post->corrective_action;
                $execution_comment = $post->execution_comment;
                $verification_result = $post->verification_result;
                $execution_path = $post->execution_path;
                if ($execution_comment == '' || $execution_comment == null) {
                    $status = 'Need Action Plan';
                    $badge = 'badge-warning';
                } else if ($execution_path == '' || $execution_path == null) {
                    $status = 'Need Evidence';
                    $badge = 'badge-warning';
                } else if ($verification_result == '' || $verification_result == null) {
                    $status = 'Proccess Verification';
                    $badge = 'badge-primary';
                } else {
                    $status = "Close";
                    $badge = 'badge-success';
                }
                // switch ($post->status) {
                //     case 1:
                //         $status = 'Open';
                //         $badge = 'badge-primary ';
                //         break;
                //     case 2:
                //         $status = 'ongoing';
                //         $badge = 'badge-warning';
                //         break;
                //     case 3:
                //         $status = 'close';
                //         $badge = 'badge-success';
                //         break;
                //     default:
                //         $badge = 'badge-secondary ';
                //         $status = 'Submitted';
                // }
                $nestedData['no'] = $no;
                $nestedData['DocNum'] = $post->DocNum;
                $nestedData['date'] = $date;

                $nestedData['area_checked'] = $post->Area_Checked;
                $nestedData['path'] = $post->Path;
                $nestedData['findings'] = $post->findings;
                $nestedData['due_date'] = $post->due_date;
                $nestedData['execution_comment'] = $post->execution_comment;
                $nestedData['execution_path'] = '<button class="btn btn-primary btn-sm" id="btn_corrective_path_' . $no . '" onclick="btn_corrective(' . $sys_id . ',' . $no . ')" style="text-align: center; width: 40px; height: 35px;><i class="fa fa-camera"></i></button>';
                $nestedData['status'] = '<span class="badge badge-lg font-weight-bold ' . $badge . ' badge-inline">' . $status . '</span>';
                $nestedData['action'] = $button;
                $nestedData['auditor'] = $post->Auditor;
                $data[] = $nestedData;
            }
        } else {

            $nestedData['no'] = '';
            $nestedData['area_checked'] = '';
            $nestedData['path'] = '';
            $nestedData['date'] = '';
            $nestedData['status'] = '';
            $nestedData['action'] = '';
            $data[] = $nestedData;
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }
    public function form_genba_mng_activity(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);

        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $sysID = $str[0];
        } else {
            $sysID = $str_req[0];
        }
        $data = GenbaManagement::get_mng_genba_activity($sysID)->get();
        $count = GenbaManagement::get_mng_genba_activity($sysID)->count();
        $form_genba = [];
        if ($count > 0) {
            foreach ($data as $d) {
                $form_genba['area_checked'] = $d->Area_Checked;
                $form_genba['auditor'] = $d->Auditor;
                $form_genba['date'] = $d->Date;
                $form_genba['process'] = $d->process;
                $form_genba['station'] = $d->station;
                $form_genba['category_id'] = $d->Category_id;
                $form_genba['category'] = $d->category . ' - ' . $d->category_desc;
                // $form_genba['note'] = $d->Note;
            }
        } else {
            $form_genba['area_checked'] = '';
            $form_genba['auditor'] = '';
            $form_genba['date'] = '';
            $form_genba['process'] = '';
            $form_genba['station'] = '';
            $form_genba['category_id'] = 0;
            $form_genba['category'] = '';
            // $form_genba['note'] = '';
        }
        $form_genba['trc_unix_id'] = $request->trc_unix_id;
        $form_genba['head_title'] = "Genba Activity";

        return view('genba_management/activity/genba_mng_activity', $form_genba);
    }
    public function get_photo_findings(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $sysID = $str[0];
        } else {
            $sysID = $str_req[0];
        }
        $d = DB::table('GenbaProcAuditDtl')
            ->where('SysID', $sysID)
            ->select('Path', 'findings', 'asign_to', 'asign_to_dept', 'priority', 'area_detail', 'due_date', 'complete_date', 'verification_result', 'execution_path', 'execution_comment');
        $data['count'] = $d->count();
        if ($d->count() > 0) {
            foreach ($d->get() as $db) {
                $data['photo'] = $db->Path;
                $data['findings'] = $db->findings;
                $data['asign_to'] = $db->asign_to;
                $data['asign_to_dept'] = $db->asign_to_dept;
                $data['priority'] = $db->priority;
                $data['area_detail'] = $db->area_detail;
                $data['execution_path'] = $db->execution_path;
                $data['execution_comment'] = $db->execution_comment;
                $data['due_date'] = $db->due_date;
                $data['complete_date'] = $db->complete_date;
            }
        } else {
            $data['photo'] = '';
            $data['findings'] = '';
            $data['asign_to'] = '';
            $data['asign_to_dept'] = '';
            $data['priority'] = '';
            $data['area_detail'] = '';
            $data['execution_path'] = '';
            $data['execution_comment'] = '';
            $data['due_date'] = '';
            $data['complete_date'] = '';
        }
        return view('genba_management/activity/attachment_findings', $data);
    }
    public function submit_form_spv(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);

        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $sysID = $str[0];
        } else {
            $sysID = $str_req[0];
        }
        $status = $request->input('status');

        $insert = GenbaManagement::submit_genba_spv_activity($status, $sysID);
        if ($insert) {
            $data['code'] = 200;
            $data['message'] = 'Data berhasil disimpan';
            return json_encode($data);
        } else {
            $data['code'] = 500;
            $data['message'] = 'Data gagal disimpan';
            return json_encode($data);
        }
    }
    public function submit_form_mng(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);

        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $sysID = $str[0];
        } else {
            $sysID = $str_req[0];
        }
        $status = $request->input('status');

        $insert = GenbaManagement::submit_genba_mng_activity($status, $sysID);
        if ($insert) {
            $data['code'] = 200;
            $data['message'] = 'Data berhasil disimpan';
            return json_encode($data);
        } else {
            $data['code'] = 500;
            $data['message'] = 'Data gagal disimpan';
            return json_encode($data);
        }
    }
    public function add_mng_genba(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);

        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $sysID = $str[0];
        } else {
            $sysID = $str_req[0];
        }
        $area_checked = $request->input('area_checked');
        $date = $request->input('date');
        $auditor = $request->input('auditor');
        $status = $request->input('status');
        $note = $request->input('note');

        $insert = GenbaManagement::add_genba_mng_activity($area_checked, $auditor, $status, $date, $note, $sysID);

        if ($insert > 0) {
            $db = DB::table('GenbaMngProcAudit')
                ->where('SysID', $insert)
                ->select('SysID');
            if ($db->count() > 0) {
                $db_activity = DB::table('GenbaMngProcAuditDtl')
                    ->where('SysID', $insert)
                    ->select('SysID');

                $data["code"] = 200;
                $data["id_activity"] = $db->first()->SysID;
                $dbScope = DB::table('GenbaMngLPAScope as a')
                    ->Join('GenbaMngAuditItem as b', 'b.scope_id', '=', 'a.SysID')
                    ->leftJoin('GenbaMngProcAuditDtl as c', function ($join) use ($db) {
                        $join->on('c.scope_id', '=', 'a.SysID')
                            ->on('c.check_item_id', '=', 'b.SysID')
                            ->on('c.genba_id', '=', DB::raw($db->first()->SysID));
                    })
                    ->leftJoin('GenbaMngProcAudit as d', function ($join) {
                        $join->on('d.SysID', '=', 'c.genba_id')
                            ->where('d.IsDelete', '=', 0);
                    })
                    ->where('b.type', '=', 1)
                    ->select('a.SysID as scope_id', 'a.LPA_Scope', 'b.SysID as check_item_id', 'b.Check_item', 'b.Check_item_eng', 'c.result as Hasil', 'c.Path')
                    ->get();
                $scopes = [];
                foreach ($dbScope as $item) {
                    $scopes[$item->LPA_Scope][] = [
                        'scope_id' => $item->scope_id,
                        'check_item_id' => $item->check_item_id,
                        'check_item' => $item->Check_item,
                        'check_item_eng' => $item->Check_item_eng,
                        'result' => $item->Hasil,
                        'photo' => $item->Path
                    ];
                }
                $data["scopes"] = $scopes;
                return view('genba_management/activity/mng_activity_form', $data);
            }
        } else {
            $data["code"] = 500;
            return $data;
        }
    }
    public function delete_mng_genba(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
        $sysID = $str[0];
        $delete = GenbaManagement::delete_mng_genba_activity($sysID);
        if ($delete == 1) {
            $data["code"] = 200;
        } else {
            $data["code"] = 500;
        }
        return json_encode($data);
    }
    public function delete_mng_genba_dtl(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
        $sysID = $str[0];

        $delete = DB::table('GenbaProcAuditDtl')
            ->where('SysID', $sysID)
            ->delete();

        if ($delete) {
            $data["code"] = 200;
        } else {
            $data["code"] = 500;
        }

        return json_encode($data);
    }
    public function delete_rusty(Request $request)
    {
        $id = $request->check_item_id;

        $deleted = DB::table('GenbaProcAuditDtl')
            ->where('check_item_id', $id)
            ->delete();

        if ($deleted) {
            return response()->json([
                'code' => 200,
                'message' => 'Data berhasil dihapus'
            ]);

        } else {
            return response()->json([
                'code' => 404,
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }
    public function post_form_mng(Request $request)
    {
        $my_id = Auth::user()->username;
        $id_activity = $request->input('activity_id');
        $scope_id = $request->input('scope_id');
        $check_item_id = $request->input('check_item_id');
        $result = $request->input('answer');

        $check_date = GenbaManagement::check_date_activity($id_activity);
        foreach ($check_date as $d) {
            $date = $d->Date;
        }
        $due_date = Carbon::parse($date)->addWeeks(2)->format('Y-m-d');
        ;
        dd($date, $due_date);
        $insert = GenbaManagement::save_genba_mng_activity_detail($my_id, $id_activity, $scope_id, $check_item_id, $result, $due_date);
        if ($insert) {
            $data['code'] = 200;
            $data['check_item_id'] = $check_item_id;
            $data['message'] = 'Data berhasil disimpan';
            $date['due_date'] = $due_date;
            $data['result'] = $result;
        } else {
            $data['code'] = 500;
            $data['check_item_id'] = $check_item_id;
            $data['message'] = 'Data gagal disimpan';
            $data['due_date'] = '';
            $data['result'] = '';
        }
        return json_encode($data);
    }
    public function post_photo_execution(Request $request)
    {
        $my_id = Auth::user()->username;
        $id_activity = $request->input('activity_id');
        $scope_id = $request->input('scope_id');
        $check_item_id = $request->input('check_item_id');
        $status_id = $request->input('status_id');

        $photo = $request->input('photos', []); // Mendapatkan data foto yang dikirimkan
        $photoPaths = []; // Inisialisasi array untuk menampung path foto

        // Proses gambar yang diambil dengan kamera (Base64)
        foreach ($photo as $image) {
            if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
                $image = substr($image, strpos($image, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, jpeg

                if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                    continue;
                }

                $imageData = base64_decode($image);
                $imageName = uniqid() . '_' . time() . ".{$type}";
                $path = 'photos/' . $imageName;

                Storage::disk('public')->put($path, $imageData);
                $photoPaths[] = $path; // Menyimpan path gambar base64 ke array
            }
            // Proses gambar yang diupload via input file (bukan base64)
            elseif ($image instanceof \Illuminate\Http\UploadedFile) {
                $imageName = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('photos', $imageName, 'public');
                $photoPaths[] = $path; // Menyimpan path gambar file ke array
            }
        }

        $photoPathsString = implode(',', $photoPaths); // Menggabungkan semua path menjadi string
        if ($status_id == 'Manager') {
            // Update ke database dengan path foto yang telah disimpan
            DB::table('GenbaMngProcAuditDtl')
                ->where('genba_id', $id_activity)
                ->where('scope_id', $scope_id)
                ->where('check_item_id', $check_item_id)
                ->update([
                    'Path' => $photoPathsString,
                    'updated_at' => Carbon::now() // Update timestamp
                ]);
        } else if ($status_id == 'SPV') {
            DB::table('GenbaProcAuditDtl')
                ->where('genba_id', $id_activity)
                ->where('scope_id', $scope_id)
                ->where('check_item_id', $check_item_id)
                ->update([
                    'Path' => $photoPathsString,
                    'updated_at' => Carbon::now()
                ]);
        }
        return response()->json([
            'message' => 'Foto berhasil disimpan.',
            'photos' => $photoPaths
        ]);
    }
    public function save_action_plan(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $sysID = $str[0];
        } else {
            $sysID = $str_req[0];
        }

        $my_id = Auth::user()->username;
        $execution_path = $request->input('action_plan');
        $photoPaths = [];
        $dataphoto = $request->input('dataphoto', []);

        if ($dataphoto == null) {
            $check = DB::table('GenbaProcAuditDtl')
                ->where('SysID', $sysID)
                ->select('execution_path')
                ->get();

            foreach ($check as $c) {
                if ($c->execution_path != '') {
                    $photoPathsString = $c->execution_path;
                    $path_asli = explode(',', $c->execution_path);
                } else {
                    $photoPathsString = null;
                    // return response()->json([
                    //     'message' => 'Foto gagal disimpan.',
                    //     'photos' => ''
                    // ]);
                }
            }
        } else {
            foreach ($dataphoto as $index => $image) {
                if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
                    $image = substr($image, strpos($image, ',') + 1);
                    $type = strtolower($type[1]);
                    if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                        continue;
                    }
                    $imageData = base64_decode($image);
                    $imageName = uniqid() . '_' . time() . ".{$type}";
                    $path = 'photos/' . $imageName;
                    Storage::disk('public')->put($path, $imageData);
                    $photoPaths[] = $path;
                } elseif ($image instanceof \Illuminate\Http\UploadedFile) {
                    $imageName = uniqid() . '_' . time() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('photos', $imageName, 'public');
                    $photoPaths[] = $path;
                }
            }
        }

        if ($photoPaths != null) {
            $photoPathsString = implode(',', $photoPaths);
        } else {
            $photoPathsString = null;
        }

        // Cek evidence & corrective_action
        $evidence = !empty($photoPathsString) ? 1 : 0;
        $corrective_action = !empty($execution_path) ? 1 : 0;

        $updates = DB::table('GenbaProcAuditDtl')
            ->where('SysID', $sysID)
            ->update([
                'execution_path' => $photoPathsString,
                'execution_comment' => $execution_path,
                'evidence' => $evidence,
                'corrective_action' => $corrective_action,
                'complete_date' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

        if ($updates) {
            return response()->json([
                'code' => 200,
                'message' => 'Foto berhasil disimpan.',
                'photos' => $photoPaths
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'message' => 'Foto gagal disimpan.',
                'photos' => ''
            ]);
        }
    }
}
