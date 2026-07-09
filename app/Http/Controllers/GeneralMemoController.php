<?php

namespace App\Http\Controllers;

use App\Models\ProductionSchedule;
use App\Models\GeneralMemo;
use App\Models\AppModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt ;
use Illuminate\Support\Facades\DB; 
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PDF;  
use Illuminate\Support\Facades\Validator;

class GeneralMemoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $my_id = Auth::user()->id ; 
        $uri = explode("/", url()->current());  
        $SEGMENT_NUM = env('SEGMENT_NUM');  
        if (count($uri) <= $SEGMENT_NUM) {
            $menu = $this->menu($my_id, 'home') ;   
        } else {
            $menu = $this->menu($my_id, $uri[$SEGMENT_NUM]) ;  
        }   
        $data['head_title'] = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'] ;
        $data['menu_level_2'] = $menu['menu_level_2'] ;
        $data['menu_level_3'] = $menu['menu_level_3'] ;
        $data['menu_level_4'] = $menu['menu_level_4'] ;  

        return view('general_memo/memo_issue_index', $data); 
    }
 
    public function get_part_number(Request $request){   
        $search = $request->search ;
        $category_id = $request->category_id ; // Get category_id from request
        $page = $request->post('page', 1); // default to page 1
        $pageSize = 10; // Number of items per page
    
        $query = GeneralMemo::get_part($category_id) ;
        if ($search) {
            $query->where('a.PartNum', 'like', '%' . $search . '%');
        } 
        $parts = $query->paginate($pageSize, ['*'], 'page', $page);

        // Prepare JSON response
        return response()->json([
            'items' => $parts->map(function($part) {
                $name=$part->PartNum.' '.$part->PartDescription;
                return [
                    'id' => $part->PartNum,  // Map the 'ResourceGrpID' to 'id'
                    'name' => $name          // Map the 'name' column to 'name'
                ];
            }),
            'pagination' => [
                'more' => $parts->hasMorePages(), // Tells Select2 if more pages are available
            ]
        ]);
    }

    public function get_warehouse(Request $request){   
        $search = $request->search;
        $line = $request->line;
        $qty = $request->qty;
        $page = $request->post('page', 1);
        $pageSize = 10;

        // Query khusus untuk warehouse unik
        $query = DB::connection('sqlsrv4')->table('Erp.PartBin AS a')
            ->leftjoin('Erp.Warehse as b', 'b.WarehouseCode', '=', 'a.WarehouseCode')
            ->where('a.PartNum', $line)
            ->where('a.OnhandQty', '>=', $qty)
            ->select('a.WarehouseCode', 'b.Description') // Pastikan kolom sesuai
            ->distinct();
        
        if ($search) {
            $query->where('a.PartNum', 'like', '%' . $search . '%');
        }
        
        $warehouses = $query->paginate($pageSize, ['*'], 'page', $page);

        return response()->json([
            'items' => $warehouses->map(function($warehouse) {
                return [
                    'id' => $warehouse->WarehouseCode,
                    'name' => $warehouse->Description 
                ];
            }),
            'pagination' => [
                'more' => $warehouses->hasMorePages(),
            ]
        ]);
    }
    public function get_bin(Request $request){   
        $search = $request->search;
        $line = $request->line;
        $warehouse = $request->warehouse;
        $qty = $request->qty;
        $page = $request->post('page', 1);
        $pageSize = 10;

        // Query khusus untuk warehouse unik
        $query = DB::connection('sqlsrv4')->table('Erp.PartBin AS a')
            ->leftjoin('Erp.Warehse as b', 'b.WarehouseCode', '=', 'a.WarehouseCode')
            ->where('a.PartNum', $line)
            ->where('a.WarehouseCode', $warehouse)
            ->where('a.OnhandQty', '>=', $qty)
            ->select('a.BinNum') // Pastikan kolom sesuai
            ->distinct();
        
        if ($search) {
            $query->where('a.PartNum', 'like', '%' . $search . '%');
        }
        
        $warehouses = $query->paginate($pageSize, ['*'], 'page', $page);

        return response()->json([
            'items' => $warehouses->map(function($warehouse) {
                return [
                    'id' => $warehouse->BinNum,
                    'name' => $warehouse->BinNum 
                ];
            }),
            'pagination' => [
                'more' => $warehouses->hasMorePages(),
            ]
        ]);
    }
    public function get_part_bin(Request $request){   
        $search = $request->search;
        $line = $request->line;
        $warehouse = $request->warehouse;
        $bin = $request->bin;
        $qty = $request->qty;
        $page = $request->post('page', 1);
        $pageSize = 10;

        // Query khusus untuk warehouse unik
        $query = DB::connection('sqlsrv4')->table('Erp.PartBin AS a')
            ->leftjoin('Erp.Warehse as b', 'b.WarehouseCode', '=', 'a.WarehouseCode')
            ->where('a.PartNum', $line)
            ->where('a.WarehouseCode', $warehouse)
            ->where('a.BinNum', $bin)
            ->where('a.OnhandQty', '>=', $qty)
            ->select('a.LotNum') // Pastikan kolom sesuai
            ->distinct();
        
        if ($search) {
            $query->where('a.PartNum', 'like', '%' . $search . '%');
        }
        
        $warehouses = $query->paginate($pageSize, ['*'], 'page', $page);

        return response()->json([
            'items' => $warehouses->map(function($warehouse) {
                return [
                    'id' => $warehouse->LotNum,
                    'name' => $warehouse->LotNum 
                ];
            }),
            'pagination' => [
                'more' => $warehouses->hasMorePages(),
            ]
        ]);
    }
    public function get_approval(Request $request){
        $search = $request->search;
        $page = $request->post('page', 1);
        $pageSize = 10;

        $query = DB::connection('sqlsrv4')->table('Erp.UserFile AS a')
            ->select('a.DcdUserID','a.Name')
            ->distinct();
        
        if ($search) {
            $query->where('a.Name', 'like', '%' . $search . '%');
        }
        
        $apprals = $query->paginate($pageSize, ['*'], 'page', $page);

        return response()->json([
            'items' => $apprals->map(function($approval) {
                return [
                    'id' => $approval->DcdUserID,
                    'name' => $approval->Name 
                ];
            }),
            'pagination' => [
                'more' => $apprals->hasMorePages(),
            ]
        ]);
    }
    public function get_approval_selected(Request $request){
        $approver=DB::table('tb_memo_part_approval')->where('id_memo',$request->id_memo)->where('sequance',$request->sequance)->value('nik');
        $search = $request->search;
        $page = $request->post('page', 1);
        $pageSize = 10;

        $query = DB::connection('sqlsrv4')->table('Erp.UserFile AS a')
            ->select('a.DcdUserID','a.Name')
            ->where('a.DcdUserID',$approver)
            ->distinct();
        
        if ($search) {
            $query->where('a.Name', 'like', '%' . $search . '%');
        }
        
        $apprals = $query->paginate($pageSize, ['*'], 'page', $page);

        return response()->json([
            'items' => $apprals->map(function($approval) {
                return [
                    'id' => $approval->DcdUserID,
                    'name' => $approval->Name 
                ];
            }),
            'pagination' => [
                'more' => $apprals->hasMorePages(),
            ]
        ]);
    }
    public function save_memo(Request $request){
        $part_name = GeneralMemo::get_partname($request->part_num);
        $warehouse_description = GeneralMemo::get_warehousename($request->warehouse_code);
        $my_id = Auth::user()->id ;
        $user=DB::table('users')->where('id',$my_id)->value('full_name');
        $now=date('Y-m-d h:i:s');
        if($request->memo_date=='')$memo_date=date('Y-m-d');
        else $memo_date=$request->memo_date;

        try {
            $cek=DB::table('tb_memo_part')->where('memo_date',$request->memo_date)->where('part_num',$request->part_num)->where('is_deleted','0')->count();
            if($cek==0){
                // Log data untuk debugging
                \Log::info('Save Memo Request:', $request->all());
                $now=date('Y-m-d H:i:s');
                $add=DB::table('tb_memo_part')->insert([
                    'memo_date'=>$memo_date,
                    'part_class'=>$request->part_class,
                    'part_num'=>$request->part_num,
                    'part_name'=>$part_name,
                    'qty_request'=>$request->qty_request,
                    'remark'=>$request->keterangan,
                    'warehouse_code'=>$request->warehouse_code,
                    'warehouse_description'=>$warehouse_description,
                    'bin_num'=>$request->bin_num,
                    'lot_num'=>$request->lot_num,
                    'is_deleted'=>'0',
                    'created_by'=>$user,
                    'created_at'=>$now,
                    'updated_at'=>$now
                ]);
            }else{
                $cek=DB::table('tb_memo_part')->where('memo_date',$request->memo_date)->where('part_num',$request->part_num)->update([
                    'qty_request'=>$request->qty_request,
                    'remark'=>$request->keterangan,
                    'warehouse_code'=>$request->warehouse_code,
                    'warehouse_description'=>$warehouse_description,
                    'bin_num'=>$request->bin_num,
                    'lot_num'=>$request->lot_num,
                    'is_deleted'=>'0',
                    'created_by'=>$user,
                    'updated_at'=>$now
                ]);
            }
            // if($add){
                $id_memo=DB::table('tb_memo_part')->where('memo_date',$request->memo_date)->where('part_num',$request->part_num)->value('id');
                $last_approval=0;
                for($i=1;$i<=5;$i++){
                    $kolom='approval'.$i;
                    $kolom_status='approval'.$i.'status';
                    // if($request->$kolom_status=='on')$status=1;
                    // else $status=0;
                    $status=$request->$kolom_status;
                    if($status==1)$last_approval=$i;
                    $tb[$i]=DB::table('tb_memo_part_approval')->where('id_memo',$id_memo)->where('sequance',$i)->count();
                    $name[$i]=DB::table('users')->where('username',$request->$kolom)->value('full_name');
                    if($tb[$i]>0){
                        $update=DB::table('tb_memo_part_approval')->where('id_memo',$id_memo)->where('sequance',$i)->update([
                            'id_memo'=>$id_memo,
                            'sequance'=>$i,
                            'id_user'=>$my_id,
                            'nik'=>$request->$kolom,
                            'full_name'=>$name[$i],
                            'status'=>$status,
                            'updated_at'=>$now
                        ]);
                    }else{
                        $add2=DB::table('tb_memo_part_approval')->insert([
                            'id_memo'=>$id_memo,
                            'sequance'=>$i,
                            'id_user'=>$my_id,
                            'nik'=>$request->$kolom,
                            'full_name'=>$name[$i],
                            'status'=>'0',
                            'created_at'=>$now,
                            'updated_at'=>$now
                        ]);
                    }
                }
                $update_last=DB::table('tb_memo_part')->where('id',$id_memo)->update([
                    'approval_seq'=>$last_approval,
                    'updated_at'=>$now
                ]);

            // }
                
            // Return response sederhana dulu untuk test
            return response()->json([
                'process_status' => 200,
                'msg_process' => 'Test berhasil! Data diterima.',
                'debug_data' => [
                    'id_memo' => $request->id_memo,
                    'part_num' => $request->part_num,
                    'qty_request' => $request->qty_request
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error save_memo: ' . $e->getMessage());
            
            return response()->json([
                'process_status' => 500,
                'msg_process' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    public function reset_memo(Request $request){
        $my_id = Auth::user()->id ;
        $user=DB::table('users')->where('id',$my_id)->value('full_name');
        $now=date('Y-m-d h:i:s');

        try {
            $update=DB::table('tb_memo_part_approval')->where('id_memo',$request->id_memo)->update([
                'status'=>'0',
                'updated_at'=>$now
            ]);
                
            // Return response sederhana dulu untuk test
            return response()->json([
                'process_status' => 200,
                'msg_process' => 'Test berhasil! Data diterima.',
                'debug_data' => [
                    'id_memo' => $request->id_memo
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error save_memo: ' . $e->getMessage());
            
            return response()->json([
                'process_status' => 500,
                'msg_process' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    public function delete_memo(Request $request){
        $my_id = Auth::user()->id ;
        $user=DB::table('users')->where('id',$my_id)->value('full_name');
        $now=date('Y-m-d h:i:s');

        try {
            $update=DB::table('tb_memo_part')->where('id',$request->id_memo)->update([
                'is_deleted'=>'1',
                'updated_at'=>$now
            ]);
                
            // Return response sederhana dulu untuk test
            return response()->json([
                'process_status' => 200,
                'msg_process' => 'Test berhasil! Data diterima.',
                'debug_data' => [
                    'id_memo' => $request->id_memo
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error save_memo: ' . $e->getMessage());
            
            return response()->json([
                'process_status' => 500,
                'msg_process' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    public function get_approval_by_memo(Request $request){
        $idMemo = $request->id_memo;
        $my_id = Auth::user()->id ;
        $nik=DB::table('users')->where('id',$my_id)->value('username');
        
        try {
            // Ganti 'nama_tabel_approval' dengan nama tabel yang benar
            $approvals = DB::table('tb_memo_part_approval')
                ->select('sequance', 'nik', 'full_name','status','updated_at')
                ->where('id_memo', $idMemo)
                ->orderBy('sequance')
                ->get();

            $approvalData = [];
            
            // Format data berdasarkan sequance
            $next_approval=1;
            foreach ($approvals as $approval) {
                $sequance = $approval->sequance;
                $approvalData["Approval{$sequance}_id"] = $approval->nik;
                $approvalData["Approval{$sequance}_name"] = $approval->full_name;
                $approvalData["Approval{$sequance}_status"] = $approval->status;
                $approvalData["Approval{$sequance}_updated_at"] = $approval->updated_at;
                if($approval->status==1)$next_approval++;
            }
            $approvalData["nik"]=$nik;
            $approvalData["next_approval"]=$next_approval;

            if (count($approvalData) > 0) {
                return response()->json([
                    'success' => true,
                    'data' => $approvalData
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data approval tidak ditemukan untuk memo ini'
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // Helper function to format approval information for display
    private function formatApprovalInfo($approvals){
        if (empty($approvals)) {
            return 'No Approval Data';
        }
        
        $formatted = [];
        foreach ($approvals as $sequence => $approval) {
            $statusBadge = $approval['status'] == 'approved' ? 
                '<span class="badge badge-success">Approved</span>' : 
                '<span class="badge badge-warning">Pending</span>';
                
            $formatted[] = "Seq {$sequence}: {$approval['full_name']} {$statusBadge}";
        }
        
        return implode('<br>', $formatted);
    }
    public function get_nik(){
        $my_id = Auth::user()->id ;
        $nik=DB::table('users')->where('id',$my_id)->value('nik');

    }


    public function front_table(Request $request){ 
        $search =  $request->front_table_search ;  
        $columns = array(  
            0 =>'id',
            1 =>'memo_date',
            2 =>'part_class',
            3 =>'part_num', 
            4 =>'part_name',
            5 =>'qty_request',
            6 =>'warehouse_code',
            7 =>'warehouse_description',
            8 =>'bin_num',
            9 =>'lot_num',
            10 =>'approval_seq',
            11 =>'remark',
            12 =>'created_by',   
        );   
        // $totalData = ProductionSchedule::get_detail_list($date,$line,$shift,$machine) ;
        $totalData=DB::table('tb_memo_part')->count();
        // $totalData = $totalData->get()->count(); 
        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column')==0 ? $columns[1] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column')==0 ? 'desc' : $request->input('order.0.dir')) ; 
        if(empty($search))
        {            
            //$posts = ProductionSchedule::get_detail_list($date,$line,$shift,$machine)
            $posts=DB::table('tb_memo_part')
            ->where('is_deleted','0')
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();
        } else { 
            // $posts =  ProductionSchedule::get_detail_list($date,$line,$shift,$machine)
            $posts=DB::table('tb_memo_part')
            ->where('is_deleted','0')
            ->where(function($query) use($search) {
                $query->where('part_num','LIKE',"%$search%");
                $query->orWhere('part_name','LIKE',"%$search%"); 
                $query->orWhere('memo_date', 'LIKE', "%$search%"); 
            })
            ->offset($start)
            ->limit($limit) 
            ->orderBy($order,$dir)
            ->get();  
            // $totalFiltered = ProductionSchedule::get_detail_list($date,$line,$shift,$machine)
            $totalFiltered = DB::table('tb_memo_part')
            ->where(function($query) use($search) {
                $query->where('part_num','LIKE',"%$search%");
                $query->orWhere('part_name','LIKE',"%$search%"); 
                $query->orWhere('memo_date', 'LIKE', "%$search%"); 
            })->get()->count();
        } 
        $data = array();
        if(!empty($posts))
        { 
        $no = $start ;
        foreach ($posts as $post)
        {  
            $no++; 
            // $trc_id = str_replace("=","-", Crypt::encryptString($post->jo_num.'~'.$post->process_detail_id.'~'.$post->item_no)) ;   
            $trc_id = '1';
            $sys_id = "'".$trc_id."'" ;     
            $button = '<button type="button" title="Detail Memo" class="btn btn-light-primary btn-sm" onclick="detail(this)" 
            data-id_memo="'.$post->id.'" 
            data-memo_date="'.$post->memo_date.'" 
            data-part_class="'.$post->part_class.'" 
            data-part_num="'.$post->part_num.'" 
            data-part_name="'.htmlspecialchars($post->part_name).'" 
            data-qty_request="'.$post->qty_request.'" 
            data-keterangan="'.htmlspecialchars($post->remark).'" 
            data-warehouse_code="'.$post->warehouse_code.'" 
            data-warehouse_description="'.htmlspecialchars($post->warehouse_description).'" 
            data-bin_num="'.$post->bin_num.'" 
            data-lot_num="'.$post->lot_num.'" 
            style="text-align: center; width: 40px; height: 35px;">
                <span id="svg_generate_tag_label_'.$no.'" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path>
                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path>
                    </svg>
                </span>
                <span id="spinner_generate_tag_label_'.$no.'" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
            </button>' ;  

            $nestedData['no'] = $no ; 
            $nestedData['id'] =  $post->id ;   
            $nestedData['memo_date'] = $post->memo_date ;  
            $nestedData['part_class'] = $post->part_class ; 
            $nestedData['part_num'] = $post->part_num; 
            $nestedData['part_name'] = $post->part_name ; 
            $nestedData['qty_request'] = number_format($post->qty_request,0) ;
            $nestedData['warehouse_code'] = $post->warehouse_code;
            $nestedData['warehouse_description'] = $post->warehouse_description;
            $nestedData['bin_num'] = $post->bin_num;
            $nestedData['lot_num'] = $post->lot_num;
            $nestedData['remark'] = $post->remark ; 
            $nestedData['approval_seq'] = $post->approval_seq;
            $nestedData['action'] = $button ;  
            $data[] = $nestedData; 
        }
        } 
        $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $data   
        ); 
        echo json_encode($json_data);  
    }

}
