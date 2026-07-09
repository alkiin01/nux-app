<?php

namespace App\Http\Controllers;

use App\Models\AppModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\GenbaManagement;
use App\Models\PRApproval;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class GenbaVerificationController extends Controller
{
    //
    public function index()
    {
        $my_id = Auth::user()->id;
        $uri = explode("/", url()->current());
        if (count($uri) < 5) {
            $menu = $this->menu($my_id, 'Verification');
        } else {
            $menu = $this->menu($my_id, $uri[4]);
        }
        $data['head_title'] = 'Finding Result';
        $data['menu_level_1'] = $menu['menu_level_1'];
        $data['menu_level_2'] = $menu['menu_level_2'];
        $data['menu_level_3'] = $menu['menu_level_3'];
        $data['menu_level_4'] = $menu['menu_level_4'];
        $data['doc_access_list'] = DB::table('t100_user_doc_access')->where('user_id', $my_id)->get();
        return view('genba_management/verification/spv_verification_index', $data);
    }
    public function verification_list(Request $request)
    {
        $search = $request->front_table_search;
        $columns = array(
            0 => 'SysID',
            1 => 'Area_Checked',
            2 => 'auditor',
            3 => 'date',
            4 => 'status',
            5 => ''
        );

        $totalData = GenbaManagement::get_genba_verifiaction_list($search)->count();

        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column') == 0 ? $columns[1] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column') == 0 ? 'desc' : $request->input('order.0.dir'));

        $posts = GenbaManagement::get_genba_verifiaction_list($search)
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
        if (!empty($search)) {
            $totalFiltered = GenbaManagement::get_genba_verifiaction_list($search)->count();
        }
        $data = array();
        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $trc_id = Crypt::encryptString($post->SysID);
                $sys_id = "'" . str_replace("=", "-", $trc_id) . "_" . $no . "'";
                $button = '<button type="button" title="Preview" class="btn btn-light-primary btn-sm" id="btn_form_view_doc_' . $no . '" onclick="document_preview(' . $sys_id . ',' . $no . ')" style="text-align: center; width: 40px; height: 35px;">
                                <span id="svg_form_view_doc_' . $no . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path>
                                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path>
                                </svg>
                                </span>
                                <span id="spinner_form_view_doc_' . $no . '" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
                            </button>';

                $area_checked = $post->Area_checked;
                $auditor = $post->Auditor;
                $date = AppModel::local_date_formate_name(substr($post->Date, 0, 10));
                $category = $post->category;
                $nestedData['no'] = $no;
                $nestedData['area_checked'] = $area_checked;
                $nestedData['auditor'] = $auditor;
                $nestedData['date'] = $date;
                $nestedData['process'] = $post->process;
                $nestedData['station'] = $post->station;
                $nestedData['category'] =  $category;
                $nestedData['action'] = $button;
                $data[] = $nestedData;
            }
        } else {

            $nestedData['no'] = '';
            $nestedData['area_checked'] = '';
            $nestedData['auditor'] = '';
            $nestedData['auditor'] = '';
            $nestedData['date'] = '';
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

    public function verification_activity(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        $status_id = $request->status_id;
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
                $form_genba['category'] = $d->category_desc;
            }
        } else {
            $form_genba['area_checked'] = '';
            $form_genba['auditor'] = '';
            $form_genba['date'] = '';
            $form_genba['category'] = '';
            $form_genba['note'] = '';
        }
        $form_genba['trc_unix_id'] = $request->trc_unix_id;

        $form_genba['head_title'] = "Manager Genba Activity";
        return view('genba_management/verification/verification_activity', $form_genba);
    }
    public function verification_activity_list(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);

        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $sysID = $str[0];
        } else {
            $sysID = $str_req[0];
        }

        $db = DB::table('GenbaProcAudit')
            ->where('SysID', $sysID)
            ->select('SysID');

        if ($db->count() > 0) {

            // query base
            $query = DB::table('GenbaAuditItem as b')
                ->leftJoin('GenbaProcAuditDtl as c', function ($join) use ($db) {
                    $join
                        ->on('c.check_item_id', '=', 'b.SysID')
                        ->on('c.genba_id', '=', DB::raw($db->first()->SysID));
                })
                ->leftJoin('GenbaProcAudit as d', function ($join) {
                    $join->on('d.SysID', '=', 'c.genba_id')
                        ->where('d.IsDelete', '=', 0);
                })
                ->select(
                    'b.scope_id as scope_id',
                    'c.genba_id',
                    'b.Scope_item as LPA_Scope',
                    'b.SysID as check_item_id',
                    'b.Check_item',
                    'b.Check_item_eng',
                    'c.result as Hasil',
                    'c.Path',
                    'c.findings',
                    'c.asign_to',
                    'c.asign_to_dept'
                )
                ->where('c.result', '>', 1);

            // total data sebelum filter
            $totalData = $query->count();

            // ambil param datatables
            $start  = $request->input('start');
            $length = $request->input('length');
            $draw   = $request->input('draw');

            // ambil data dengan paging
            $dbScope = $query
                ->skip($start)
                ->take($length)
                ->get();

            $data = array();
            $no = $start;
            foreach ($dbScope as $item) {
                $no++;
                $trc_id = Crypt::encryptString($item->check_item_id);
                $genba_id = $item->genba_id;
                $scope_id = $item->scope_id;
                $sys_id = "'" . str_replace("=", "-", $trc_id) . "_" . $no . "'";

                $button = '<button type="button" title="Verification" class="btn btn-light-primary btn-sm" id="btn_verify_doc_' . $no . '" onclick="document_verify(' . $sys_id . ',' . $no . ',' . $genba_id . ',' . $scope_id . ')" style="text-align: center; width: 40px; height: 35px;">
                <span id="svg_verify_doc_' . $no . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24"/>
                        <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                        <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                    </g></svg>
                </span>
                <span id="spinner_verify_doc_' . $no . '" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
            </button>';

                $nestedData['no'] = $no;
                $nestedData['genba_id'] = $item->genba_id;
                $nestedData['scope_id'] = $item->scope_id;
                $nestedData['check_item_id'] = $item->check_item_id;
                $nestedData['check_item'] = $item->Check_item;
                $nestedData['check_item_eng'] = $item->Check_item_eng;
                $nestedData['findings'] = $item->findings;
                $nestedData['asign_to'] = $item->asign_to;
                $nestedData['asign_to_dept'] = $item->asign_to_dept;
                $nestedData['result'] = $item->Hasil;
                $nestedData['photo'] = $item->Path;
                $nestedData['action'] = $button;

                $data[] = $nestedData;
            }

            $json_data = array(
                "draw" => intval($draw),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalData),
                "data" => $data
            );

            echo json_encode($json_data);
        }
    }

    public function get_user_data(Request $request)
    {
        $search = $request->search;
        $page = $request->post('page', 1); // default to page 1
        $pageSize = 10; // Number of items per page

        $query = GenbaManagement::get_user();
        if ($search) {
            $query->where('full_name', 'LIKE', '%' . $search . '%');
        }
        $areas = $query->paginate($pageSize, ['*'], 'page', $page);
        return response()->json([
            'items' => $areas->map(function ($areas) {
                return [
                    'id' => $areas->username,
                    'name' => $areas->full_name
                ];
            }),
            'pagination' => [
                'more' => $areas->hasMorePages(),
            ]
        ]);
    }

    public function get_section(Request $request)
    {
        $search = $request->search;
        $page = $request->post('page', 1); 
        $pageSize = 10; 

        $query = GenbaManagement::get_section_list();
        if ($search) {
            $query->where('Desc', 'LIKE', '%' . $search . '%');
        }
        $areas = $query->paginate($pageSize, ['*'], 'page', $page);
        return response()->json([
            'items' => $areas->map(function ($areas) {
                $name = $areas->desc;


                return [
                    'id' => $areas->id,
                    'name' => $name
                ];
            })->values(),
            'pagination' => [
                'more' => $areas->hasMorePages(),
            ]
        ]);
    }
    public function getVerifiedform(Request $request)
    {
        $str_req = explode("_", $request->check_item_id);

        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $check_item_id = $str[0];
        } else {
            $check_item_id = $str_req[0];
        }
        $genba_id = $request->genba_id;
        $scope_id = $request->scope_id;
        $db = DB::table('GenbaSpvProcAuditDtl')
            ->where('genba_id', $genba_id)
            ->where('scope_id', $scope_id)
            ->where('check_item_id', $check_item_id)
            ->get();
        $data = array();
        foreach ($db as $d) {
            $data["asign_to"] = $d->asign_to;
            $data["asign_to_dept"] = $d->asign_to_dept;
        }
        return json_encode($data);
    }
    public function submit_genba_spv_activity(Request $request) {}
    public function save_verified(Request $request)
    {
        $str_req = explode("_", $request->check_item_id);

        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $check_item_id = $str[0];
            $status_id = $str_req[2];
        } else {
            $check_item_id = $str_req[0];
        }
        $scope_id = $request->scope_id;
        $genba_id = $request->genba_id;
        $priority = $request->priority;
        $asign_to_dept = $request->asign_to_dept;
        $asign_to_name = $request->asign_to_name;
        $asign_to = $request->asign_to;
        if ($status_id == 'SPV') {
            $update = DB::table('GenbaSpvProcAuditDtl')
                ->where('genba_id', $genba_id)
                ->where('scope_id', $scope_id)
                ->where('check_item_id', $check_item_id)
                ->update([
                    'asign_to_dept'  => $asign_to_dept,
                    'asign_to_name'  => $asign_to_name,
                    'asign_to'       => $asign_to,
                    'priority'       => $priority,
                    'updated_at' => Carbon::now() // Update timestamp
                ]);
            $update = DB::table('GenbaSpvProcAudit')
                ->where('SysID', $genba_id)
                ->update([
                    'Status'  => 2,
                    'updated_at' => Carbon::now() // Update timestamp
                ]);
        } else if ($status_id == 'Manager') {
            $update = DB::table('GenbaMngProcAuditDtl')
                ->where('genba_id', $genba_id)
                ->where('scope_id', $scope_id)
                ->where('check_item_id', $check_item_id)
                ->update([
                    'asign_to_dept'  => $asign_to_dept,
                    'asign_to_name'  => $asign_to_name,
                    'asign_to'       => $asign_to,
                    'priority'       => $priority,
                    'updated_at' => Carbon::now() // Update timestamp
                ]);
            $update = DB::table('GenbaMngProcAudit')
                ->where('SysID', $genba_id)
                ->update([
                    'Status'  => 2,
                    'updated_at' => Carbon::now() // Update timestamp
                ]);
        }

        if ($update) {
            return response()->json([
                'code' => 200,
                'message' => 'Data berhasil disimpan.',
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'message' => 'Data gagal disimpan.',
            ]);
        }
    }
    public function execution_genba(Request $request)
    {
        $my_id = Auth::user()->id;
        $uri = explode("/", url()->current());
        if (count($uri) < 5) {
            $menu = $this->menu($my_id, 'Verification');
        } else {
            $menu = $this->menu($my_id, $uri[4]);
        }
        $data['head_title'] = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'];
        $data['menu_level_2'] = $menu['menu_level_2'];
        $data['menu_level_3'] = $menu['menu_level_3'];
        $data['menu_level_4'] = $menu['menu_level_4'];
        $data['doc_access_list'] = DB::table('t100_user_doc_access')->where('user_id', $my_id)->get();
        return view('genba_management/verification/execution_genba_index', $data);
    }
    public function execution_activity_list(Request $request)
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
            5 => ''
        );
        // Determine total data count
        $totalData = GenbaManagement::get_genba_verifiaction_list($search, $date_from, $date_to, $auditor)->count();

        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column') == 0 ? $columns[1] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column') == 0 ? 'desc' : $request->input('order.0.dir'));
        $posts = GenbaManagement::get_genba_verifiaction_list($search, $date_from, $date_to, $auditor)
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
        if (!empty($search)) {
            $totalFiltered = GenbaManagement::get_genba_verifiaction_list($search, $date_from, $date_to, $auditor)->count();
        }
        $data = array();
        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $trc_id = Crypt::encryptString($post->SysID);
                $sys_id = "'" . str_replace("=", "-", $trc_id) . "_" . $no . "'";
                $button = '<button type="button" title="Preview" class="btn btn-light-primary btn-sm" id="btn_form_view_doc_' . $no . '" onclick="document_preview(' . $sys_id . ',' . $no . ')" style="text-align: center; width: 40px; height: 35px;">
                                    <span id="svg_form_view_doc_' . $no . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path>
                                        <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path>
                                    </svg>
                                    </span>
                                    <span id="spinner_form_view_doc_' . $no . '" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
                                </button>';
                $area_checked = $post->Area_checked;
                $auditor = $post->Auditor;
                $date = AppModel::local_date_formate_name(substr($post->Date, 0, 10));
                $category = $post->category;
                $nestedData['no'] = $no;
                $nestedData['area_checked'] = $area_checked;
                $nestedData['auditor'] = $auditor;
                $nestedData['date'] = $date;
                $nestedData['process'] = $post->process;
                $nestedData['station'] = $post->station;
                $nestedData['category'] =  $category;
                $nestedData['action'] = $button;
                $data[] = $nestedData;
            }
        } else {

            $nestedData['no'] = '';
            $nestedData['area_checked'] = '';
            $nestedData['auditor'] = '';
            $nestedData['auditor'] = '';
            $nestedData['date'] = '';
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
    public function show_findings(Request $request)
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
                $form_genba['date'] = $d->Date;
                $form_genba['category_id'] = $d->Category_id;
                $form_genba['category'] = $d->category . ' - ' . $d->category_desc;
                // $form_genba['note'] = $d->Note;
            }
        } else {
            $form_genba['area_checked'] = '';
            $form_genba['auditor'] = '';
            $form_genba['date'] = '';
            $form_genba['category_id'] = 0;
            $form_genba['category'] = '';
            // $form_genba['note'] = '';
        }
        $form_genba['trc_unix_id'] = $request->trc_unix_id;
        $form_genba['head_title'] = "Realisation Genba Activity";
        return view('genba_management/verification/realisation_genba', $form_genba);
    }

    public function get_waitting_findings(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $sysID = $str[0];
        } else {
            $sysID = $str_req[0];
        }
        $genba_id = $sysID;

        $data = DB::table('GenbaProcAuditDtl')
            ->selectRaw("
            COUNT(CASE WHEN corrective_action IS NULL THEN 1 END) AS corrective_action_waiting,
            COUNT(CASE WHEN evidence IS NULL THEN 1 END) AS evidence_waiting,
            COUNT(CASE WHEN verification_result = 1 THEN 0 END) AS verification_waiting,

            COUNT(CASE WHEN findings IS NOT NULL THEN 1 END) AS findings_exist
        ")
            ->whereNotNull('findings')
            ->where('genba_id', $genba_id)
            ->first();

        return response()->json([
            'total_action'   => $data->corrective_action_waiting,
            'total_evidence' => $data->evidence_waiting,
            'total_verified' => $data->verification_waiting,
            'total_findings' => $data->findings_exist,
        ]);
    }


    public function show_findings_list(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $sysID = $str[0];
        } else {
            $sysID = $str_req[0];
        }
        $search = $request->front_table_search;
        $genba_id = $sysID;
        $columns = array(
            0 => 'SysID',
            1 => 'Path',
            2 => 'findings',
            3 => 'area_detail',
            4 => 'execution_comment',
            5 => 'Asign_to_dept',
            6 => 'due_date',
            7 => 'complete_date',
            8 => 'verification_result',
            9 => 'status',
        );
        // Determine total data count
        $totalData = GenbaManagement::get_genba_realisation_list($search, $genba_id)->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column') == 0 ? $columns[0] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column') == 0 ? 'desc' : $request->input('order.0.dir'));
        $posts = GenbaManagement::get_genba_realisation_list($search, $genba_id)
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        if (!empty($search)) {
            $totalFiltered = GenbaManagement::get_genba_realisation_list($search, $genba_id)->count();
        }
        $data = array();
        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $trc_id = Crypt::encryptString($post->SysID);
                $sys_id = "'" . str_replace("=", "-", $trc_id) . '_' . $no . "'";

                $button = '<button type="button" title="Take Photo" class="btn btn-light-primary btn-sm" id="btn_worksheet_doc_' . $no . '" onclick="document_worksheet(' . $sys_id . ',' . $no . ')" style="text-align: center; width: 40px; height: 35px;">
                                <span id="svg_worksheet_doc_' . $no . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path>
                                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path>
                                    <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path>
                                </svg>
                                </span>
                                <span id="spinner_worksheet_doc_' . $no . '" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
                            </button>';

                $action_plan = $post->execution_comment;
                $verification_result = $post->verification_result;
                if ($action_plan == '' || $action_plan == null) {
                    $action_plan_text = 'Need action plan';
                    $action_plan_badge = 'badge-warning';
                } else if ($verification_result == '' || $verification_result == null) {
                    $action_plan_text = 'Need verification';
                    $action_plan_badge = 'badge-primary';
                } else {
                    $action_plan_text = 'Verified';
                    $action_plan_badge = 'badge-success';
                }

                $duedate = AppModel::local_date_formate_name(substr($post->due_date, 0, 10));
                $nestedData['no'] = $no;
                $nestedData['findings'] = $post->findings;
                $nestedData['asign_to_dept'] = $post->asign_to_dept;
                $nestedData['asign_to'] = $post->asign_to;
                $nestedData['Path'] = $post->Path;
                $nestedData['due_date'] = $duedate;
                $nestedData['complete_date'] = AppModel::local_date_formate_name(substr($post->complete_date, 0, 10));
                $nestedData['auditor'] = $post->auditor;
                $nestedData['verification_result'] = $post->verification_result == '' ? '<span class="badge badge-sm font-weight-bold badge-primary badge-inline">Open</span>' : '<span class="badge badge-sm font-weight-bold badge-success badge-inline">Close</span>';
                $nestedData['priority'] = $post->priority;
                $nestedData['area_detail'] = $post->area_detail;
                $nestedData['corrective_action'] = $post->corrective_action;

                $execution_comment = "'" . $post->execution_comment . "'";
                $execution_path = "'" . $post->execution_path . "'";
                $nestedData['execution_path'] = '<button type="button" title="Take Photo" class="btn btn-light-primary btn-sm evidences" 
                            data-evidences=' . env('BASE_URL') . '/storage/app/public/' . $execution_path . ' data-comment =' . $execution_comment . ' data-sysid=' . $sys_id . ' data-verfication="' . $action_plan_text . '"
                            id="btn_worksheet_doc_' . $no . '"  style="text-align: center; width: 40px; height: 35px;">
                                <span id="svg_worksheet_doc_' . $no . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path>
                                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path>
                                    <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path>
                                </svg>
                                </span>
                                <span id="spinner_worksheet_doc_' . $no . '" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
                            </button>';

                $nestedData['status'] = '<span class="badge badge-sm font-weight-bold ' . $action_plan_badge . ' badge-inline">' . $action_plan_text . '</span>';
                $nestedData['action'] = $button;

                // >>> Tambahan sesuai request lu
                $nestedData['action_plan'] = ($post->corrective_action == null || $post->corrective_action == 0) ? 'Waiting' : 'Complate';
                $nestedData['evidence'] = ($post->evidence == null || $post->evidence == 0) ? 'Waiting' : 'Complate';
                $nestedData['verified'] = ($post->verification_result == null || $post->verification_result == 0) ? 'Waiting' : 'Complate';
                // <<<

                $data[] = $nestedData;
            }
        } else {
            $nestedData['no'] = '';
            $nestedData['findings'] = '';
            $nestedData['asign_to_dept'] = '';
            $nestedData['asign_to'] = '';
            $nestedData['Path'] = '';
            $nestedData['due_date'] = '';
            $nestedData['complete_date'] = '';
            $nestedData['auditor'] = '';
            $nestedData['verification_result'] = '';
            $nestedData['priority'] = '';
            $nestedData['area_detail'] = '';
            $nestedData['corrective_action'] = '';
            $nestedData['evidence'] = '';
            $nestedData['execution_path'] = '';
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
    public function do_verified(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $sysID = $str[0];
        } else {
            $sysID = $str_req[0];
        }

        $data = DB::table('GenbaProcAuditDtl')
            ->where('genba_id', $sysID)
            ->get();

        $count = DB::table('GenbaProcAuditDtl')
            ->where('genba_id', $sysID)
            ->count();

        if ($count > 0) {
            $update = DB::table('GenbaProcAuditDtl')
                ->where('genba_id', $sysID)
                ->where('evidence', 1)
                ->where('corrective_action', 1)
                ->update([
                    'verification_result' => 1,
                    'status' => 1,
                    'updated_at' => Carbon::now()
                ]);
        }

        if (!empty($update)) {
            return response()->json([
                'code' => 200,
                'message' => 'Data berhasil disimpan.',
            ]);
        } else {
            return response()->json([
                'code' => 500,
                'message' => 'Data gagal disimpan. Pastikan evidence & corrective_action = 1.',
            ]);
        }
    }
    public function get_worksheet(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $sysID = $str[0];
        } else {
            $sysID = $str_req[0];
        }
        $data = GenbaManagement::get_genba_worksheet($sysID)->get();
    }
    public function post_after_genba(Request $request)
    {
        $str_req = explode("_", $request->SysID);
        $status_id = $request->status_id;
        $execution_comment = $request->execution_comment;
        if ($str_req[0] != "0") {
            $str = explode("_", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
            $SysID = $str[0];
        } else {
            $SysID = $str_req[0];
        }


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
            DB::table('GenbaMngProcAuditDtl')
                ->where('SysID', $SysID)
                ->update([
                    'execution_path'       => $photoPathsString,
                    'execution_comment' => $execution_comment,
                    'updated_at' => Carbon::now() // Update timestamp
                ]);
        } else if ($status_id == 'SPV') {
            DB::table('GenbaSpvProcAuditDtl')
                ->where('SysID', $SysID)
                ->update([
                    'execution_path'       => $photoPathsString,
                    'execution_comment' => $execution_comment,
                    'updated_at' => Carbon::now() // Update timestamp
                ]);
        }
        return response()->json([
            'message' => 'Foto berhasil disimpan.',
            'photos'  => $photoPaths // Mengembalikan daftar path foto yang disimpan
        ]);
    }
}
