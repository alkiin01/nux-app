<?php

namespace App\Http\Controllers;

use App\Models\ProductionSchedule;
use App\Models\AppModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt ;
use Illuminate\Support\Facades\DB; 
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PDF;  

class ProductionScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
        return view('production_schedule/production_schedule_index', $data); 
    }
 
    public function get_resource_group(Request $request)
    {   
        $search = $request->search ;
        $category_id = $request->category_id ; // Get category_id from request
        $page = $request->post('page', 1); // default to page 1
        $pageSize = 10; // Number of items per page
    
        $query = ProductionSchedule::get_resource_group($category_id) ;
        if ($search) {
            $query->where('a.ResourceGrpID', 'like', '%' . $search . '%');
        } 
        $resourceGroups = $query->paginate($pageSize, ['*'], 'page', $page);

        // Prepare JSON response
        return response()->json([
            'items' => $resourceGroups->map(function($resourceGroup) {
                return [
                    'id' => $resourceGroup->ResourceGrpID,  // Map the 'ResourceGrpID' to 'id'
                    'name' => $resourceGroup->ResourceGrpID          // Map the 'name' column to 'name'
                ];
            }),
            'pagination' => [
                'more' => $resourceGroups->hasMorePages(), // Tells Select2 if more pages are available
            ]
        ]);
    }

    public function get_resource(Request $request)
    {   
        $search = $request->search ;
        $line = $request->line ; // Get category_id from request
        $page = $request->post('page', 1); // default to page 1
        $pageSize = 10; // Number of items per page
    
        $query = ProductionSchedule::get_resource($line) ;
        if ($search) {
            $query->where('a.ResourceID', 'like', '%' . $search . '%');
        } 
        $resource = $query->paginate($pageSize, ['*'], 'page', $page);

        // Prepare JSON response
        return response()->json([
            'items' => $resource->map(function($resource) {
                return [
                    'id' => $resource->ResourceID,  // Map the 'ResourceGrpID' to 'id'
                    'name' => $resource->Description          // Map the 'name' column to 'name'
                ];
            }),
            'pagination' => [
                'more' => $resource->hasMorePages(), // Tells Select2 if more pages are available
            ]
        ]);
    }

    public function get_resource_form(Request $request)
    {   
        $search = $request->search ;
        $category_id = $request->category_id ;  // Get category_id from request
        $page = $request->post('page', 1); // default to page 1
        $pageSize = 10; // Number of items per page
    
        $query = ProductionSchedule::get_resource_form($category_id) ;
        if ($search) {
            $query->where('b.ResourceID', 'like', '%' . $search . '%');
        } 
        $resource = $query->paginate($pageSize, ['*'], 'page', $page);

        // Prepare JSON response
        return response()->json([
            'items' => $resource->map(function($resource) {
                return [
                    'id' => $resource->ResourceID,  // Map the 'ResourceGrpID' to 'id'
                    'name' => $resource->Description          // Map the 'name' column to 'name'
                ];
            }),
            'pagination' => [
                'more' => $resource->hasMorePages(), // Tells Select2 if more pages are available
            ]
        ]);
    }

    public function get_warehouse_id(Request $request)
    {   
        $search = $request->search ; 
        $page = $request->post('page', 1); // default to page 1
        $pageSize = 10; // Number of items per page
    
        $query = ProductionSchedule::get_warehouse_id() ;
        if ($search) {
            $query->where('Description', 'like', '%' . $search . '%');
        } 
        $resource = $query->paginate($pageSize, ['*'], 'page', $page);
 
        return response()->json([
            'items' => $resource->map(function($resource) {
                return [
                    'id' => $resource->WarehouseCode, 
                    'name' => $resource->Description   
                ];
            }),
            'pagination' => [
                'more' => $resource->hasMorePages(), // Tells Select2 if more pages are available
            ]
        ]);
    }

    public function front_table(Request $request)
    { 
        $date = $request->JoDate ; 
        $category =  $request->LineCategory ;
        $line =  $request->ResourceGroupID ;
        $machine =  $request->ResourceID ;
        $shift =  $request->ShiftID ;
        $search =  $request->front_table_search ;  
        $columns = array(  
            0 =>'jo_num',
            1 =>'jo_num',
            2 =>'achievment',
            3 =>'jo_num', 
            4 =>'item_no',
            5 =>'qty_packing',
            6 =>'home_line_detail_id',
            7 =>'process_detail_id',
            8 =>'qty_plan',
            9 =>'qty_1',
            10 =>'qty_receive',
            11 =>'remark_d',   
        );   
        $totalData = ProductionSchedule::get_detail_list($date,$line,$shift,$machine) ;
        $totalData = $totalData->get()->count(); 
        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column')==0 ? $columns[1] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column')==0 ? 'desc' : $request->input('order.0.dir')) ; 
        if(empty($search))
        {            
        $posts = ProductionSchedule::get_detail_list($date,$line,$shift,$machine)
        ->offset($start)
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();
        } else { 
        $posts =  ProductionSchedule::get_detail_list($date,$line,$shift,$machine)
        ->where(function($query) use($search) {
            $query->where('item_no','LIKE',"%$search%");
            $query->orWhere('item_name','LIKE',"%$search%"); 
            $query->orWhere('jo_num', 'LIKE', "%$search%"); 
        })
        ->offset($start)
        ->limit($limit) 
        ->orderBy($order,$dir)
        ->get();  
        $totalFiltered = ProductionSchedule::get_detail_list($date,$line,$shift,$machine)
        ->where(function($query) use($search) {
            $query->where('item_no','LIKE',"%$search%");
            $query->orWhere('item_name','LIKE',"%$search%"); 
            $query->orWhere('jo_num', 'LIKE', "%$search%");
        })->get()->count();
        } 
        $data = array();
        if(!empty($posts))
        { 
        $no = $start ;
        foreach ($posts as $post)
        {  
            $no++; 
            $trc_id = str_replace("=","-", Crypt::encryptString($post->jo_num.'~'.$post->process_detail_id.'~'.$post->item_no)) ;   
            $sys_id = "'".$trc_id."'" ;     
            $button = '<button type="button" title="Generate Label" class="btn btn-light-primary btn-sm" id="btn_generate_tag_label_'.$no.'" onclick="form_generate_tag_label('.$sys_id.','.$no.')" style="text-align: center; width: 40px; height: 35px;">
                <span id="svg_generate_tag_label_'.$no.'" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path>
                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path>
                    </svg>
                </span>
                <span id="spinner_generate_tag_label_'.$no.'" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
            </button>' ;  

            $nestedData['no'] = $no ; 
            $nestedData['doc_num'] =  $post->jo_num ;   
            $nestedData['item_no'] = $post->item_no .' <br> '.$post->item_name ;  
            $nestedData['qty_packing'] = number_format($post->qty_packing,0)  ; 
            $nestedData['plan'] = number_format($post->qty_plan,0) ; 
            $nestedData['line'] = $post->home_line_detail_id ; 
            $nestedData['process'] = $post->process_detail_id ;
            $nestedData['act'] = number_format($post->qty_1,0) ; 
            $nestedData['ach'] = number_format($post->achievment,0).'%' ;
            $nestedData['receive'] = number_format($post->qty_receive,0) ;
            $nestedData['remark'] = $post->remark_d ; 
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

    public function get_preview_doc(Request $request){  
        $str_req = explode("_",$request->trc_unix_id); 
        $str = explode("~",Crypt::decryptString(str_replace("-","=", $str_req[0])));  
        $sys_id = "'".$request->trc_unix_id."'" ;    
        $jo_num = $str[0] ;     
        $process_detail_id = $str[1] ;     
        $item_no = $str[2] ;      
        $data_detail = ProductionSchedule::data_detail("$jo_num", "$process_detail_id", "$item_no") ;  
        // dd($data_detail);
        if($data_detail->count() > 0)  {
            foreach ($data_detail as $db) { 
                $data['trc_unix_id'] =  $sys_id ;   
                $data['req_due_date'] = $db->req_due_date ;
                $data['process_detail_id'] = $db->process_detail_id ;
                $data['home_line'] = $db->home_line ; 
                $data['home_line_detail_id'] = $db->home_line_detail_id ; 
                $data['qty_packing'] = (int) $db->qty_packing ; 
                $data['qty_plan'] = (int) $db->qty_plan ; 
                $data['qty_1'] = (int) $db->qty_1 ; 
                $data['qty_receive'] = (int) $db->qty_receive ; 
                $data['remark_d'] = $db->remark_d ; 
                $data['shift'] = $db->shift ; 
                $data['item_no'] = $db->item_no ; 
                $data['item_name'] = str_replace(",", "__", $db->item_name) ; 
                $data['partner_code'] = $db->partner_code ; 
                $data['model_name'] = $db->model_name ; 
                $data['achievment'] = $db->achievment ; 
                $data['jo_num'] = $db->jo_num ; 
                $data['WarehouseID'] = $db->WarehouseCode ; 
                $data['WarehouseDesc'] = $db->WarehouseDesc ; 
                $data['ref_tab'] = 1 ;  
            }  
        } else {
                $data['PackNum'] = '' ;
                $data['req_due_date'] ='' ;
                $data['process_detail_id'] = '' ;
                $data['home_line'] = '' ;
                $data['home_line_detail_id'] = '' ;
                $data['qty_packing'] = '' ;
                $data['qty_plan'] = '' ;
                $data['qty_1'] = '' ;
                $data['qty_receive'] = '' ;
                $data['remark_d'] = '' ;
                $data['shift'] = '' ;
                $data['item_no'] = '' ;
                $data['item_name'] = '' ;
                $data['partner_code'] = '' ;
                $data['model_name'] = '' ;
                $data['achievment'] = '' ;
                $data['jo_num'] = '' ; 
                $data['WarehouseID'] = '' ; 
                $data['WarehouseDesc'] = '' ; 
                $data['ref_tab'] = 0 ;  
        } 
        echo json_encode($data); 
    }


    public function detail_table(Request $request) {    
        $str_req = explode("_",$request->trc_unix_id); 
        $str_id = explode("~",Crypt::decryptString(str_replace("-","=", $str_req[0]))); 
        $job_num = $str_id[0];
        $process_detail_id = $str_id[1]; 
        $part_num = $str_id[2];   
        $columns = array(  
            0 =>'a.line_search_id',
            1 =>'a.item_no',
            2 =>'a.qty_1',
            3 =>'a.created_by' 
        );   

        $totalData = ProductionSchedule::get_detail_table($job_num,$process_detail_id,$part_num)->get()->count(); 
        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[0];
        $dir = ($request->input('order.0.column')==0 ? 'desc' : $request->input('order.0.dir')) ; 
        if(empty($request->input('search.value')))
        {            
        $posts = ProductionSchedule::get_detail_table($job_num,$process_detail_id,$part_num)
        ->offset($start)
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();
        } else {
        $search = $request->input('search.value');  
        $posts =  ProductionSchedule::get_detail_table($job_num,$process_detail_id,$part_num)->where('b.[item_no]','LIKE',"%$search%")
        ->offset($start)
        ->limit($limit) 
        ->orderBy($order,$dir)
        ->get();  
        $totalFiltered = ProductionSchedule::get_detail_table($job_num,$process_detail_id,$part_num)->where('b.item_no','LIKE',"%$search%")->get()->count();
        } 
        $data = array();
        if(!empty($posts))
        { 
        $no = $start ;
        foreach ($posts as $post)
        { 
            $no++; 
            $trc_id = str_replace("=","-", Crypt::encryptString($post->job_num.'~'.$post->process_detail_id.'~'.$post->item_no.'~'.$post->line_search_id)) ;   
            $sys_id = "'".$trc_id."'" ;     

            $button = '<button type="button" title="print" class="btn btn-light-primary btn-sm" id="print_tag_label_'.$no.'" onclick="tag_label_preview('.$sys_id.')" style="text-align: center; width: 40px; height: 35px;">
                            <span id="svg_print_tag_label_'.$no.'" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                    <defs/>
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M16,17 L16,21 C16,21.5522847 15.5522847,22 15,22 L9,22 C8.44771525,22 8,21.5522847 8,21 L8,17 L5,17 C3.8954305,17 3,16.1045695 3,15 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,15 C21,16.1045695 20.1045695,17 19,17 L16,17 Z M17.5,11 C18.3284271,11 19,10.3284271 19,9.5 C19,8.67157288 18.3284271,8 17.5,8 C16.6715729,8 16,8.67157288 16,9.5 C16,10.3284271 16.6715729,11 17.5,11 Z M10,14 L10,20 L14,20 L14,14 L10,14 Z" fill="#000000"/>
                                        <rect fill="#000000" opacity="0.3" x="8" y="2" width="8" height="2" rx="1"/>
                                    </g>
                                </svg>
                            </span>
                            <span id="spinner_print_tag_label_'.$no.'" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
                        </button>' ;

            $button_delete = '<button type="button" title="Delete" class="btn btn-light-danger btn-sm" id="btn_delete_tag_label_'.$post->line_search_id.'" onclick="btn_delete_label('.$post->line_search_id.', '.$post->qty_1.')" style="text-align: center; width: 40px; height: 35px;">
                            <span id="svg_delete_tag_label_'.$post->line_search_id.'" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                    <defs/>
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                        <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                    </g>
                                </svg>
                            </span>
                            <span id="spinner_delete_tag_label_'.$post->line_search_id.'" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
                        </button>' ;

            $button_save = '<button type="button" title="Edit" class="btn btn-light-success btn-sm" id="save_tag_label_'.$no.'" onclick="getLabelForm('.$post->line_search_id.', '.$post->qty_1.')" style="text-align: center; width: 40px; height: 35px;">
                            <span id="svg_save_tag_label_'.$no.'" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                    <defs/>
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M8,17.9148182 L8,5.96685884 C8,5.56391781 8.16211443,5.17792052 8.44982609,4.89581508 L10.965708,2.42895648 C11.5426798,1.86322723 12.4640974,1.85620921 13.0496196,2.41308426 L15.5337377,4.77566479 C15.8314604,5.0588212 16,5.45170806 16,5.86258077 L16,17.9148182 C16,18.7432453 15.3284271,19.4148182 14.5,19.4148182 L9.5,19.4148182 C8.67157288,19.4148182 8,18.7432453 8,17.9148182 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000000, 10.707409) rotate(-135.000000) translate(-12.000000, -10.707409) "/>
                                        <rect fill="#000000" opacity="0.3" x="5" y="20" width="15" height="2" rx="1"/>
                                    </g>
                                </svg>
                            <span id="spinner_save_tag_label_'.$no.'" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
                        </button>' ;

            $label_done = '<button title="Edit" class="btn btn-sm" style="text-align: center; height: 35px;" disabled><span>Sudah discan</span></button>' ;
  
            $nestedData['no'] =  $no ;    
            $nestedData['item_no'] = $post->item_no ;  
            $nestedData['plan'] = number_format($post->qty_1,0) ;   
            $nestedData['created_by'] =  $post->created_by ;    
            $nestedData['action'] = ($post->TotalScan == 0 ? $button.' '.$button_delete.' '.$button_save : $label_done) ;  
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

    public function generate_tag_label(Request $request) 
            {
                $str_req = explode("_",$request->trc_unix_id); 
                $str_id = explode("~",Crypt::decryptString(str_replace("-","=", $str_req[0]))); 
                $job_num = $str_id[0];
                $process_detail_id = $str_id[1]; 
                $my_name = auth()->user()->username ;

                $qty_pack = $request->std_pack ;  
                $home_line_detail_id = $request->home_line ;
                $operator_name = $request->operator_name ;
                $quality_name = $request->quality_operator_name ; 
                $model_name = $request->model_name ;  
                $qty_plan = $request->qty_plan ;  
                $part_num = $request->part_num ; 
                $part_name = str_replace(",", "__", $request->part_name) ;  
                $cust_name = $request->partner_code ;    
                $production_date = $request->production_date ;   
                $warehouse_code = $request->ToWarehouseID ;   
                $warehouse_desc = $request->ToWarehouseDesc ;   

                $delete = DB::table('t510_production_tag AS a')
                ->leftJoin('t510_InventoryMove AS b', function($join) {
                    $join->on('a.job_num', '=', 'b.DocNumReference')
                        ->on('a.process_detail_id', '=', 'b.DocNumReferenceLine')
                        ->on('a.line_search_id', '=', 'b.DocNumReferenceLineRel')
                        ->on('a.item_no', '=', 'b.PartNum');
                })
                ->where('a.job_num', '=', $job_num)
                ->where('a.process_detail_id', '=', $process_detail_id)
                ->where('a.item_no', '=', $part_num)
                ->whereNull('b.DocNumReference') ;

                if ($delete->count() > 0)
                {
                    $exe_delete =  $delete->delete(); 
                    if ($exe_delete) {
                        $generate = ProductionSchedule::generate_tag_label($job_num,$process_detail_id,$qty_plan,$qty_pack,$home_line_detail_id,$operator_name,$quality_name,$model_name,$part_num,$part_name,$cust_name,$production_date, $warehouse_code, $warehouse_desc);
                        if ($generate) {   
                            $data['process_status'] = 200 ; 
                            $data['msg_process'] = 'Data updated successfully' ; 
                        } else {
                            $data['process_status'] = 500 ;
                            $data['msg_process'] = 'Data updated fail !' ; 
                        } 
                    } else {
                        $data['process_status'] = 500 ;
                        $data['msg_process'] = 'Login sebagai admin data!' ;  
                    }
                } else {  
                    $generate = ProductionSchedule::generate_tag_label($job_num,$process_detail_id,$qty_plan,$qty_pack,$home_line_detail_id,$operator_name,$quality_name,$model_name,$part_num,$part_name,$cust_name,$production_date, $warehouse_code, $warehouse_desc);
                    if ($generate) {   
                        $data['process_status'] = 200 ; 
                        $data['msg_process'] = 'Data updated successfully' ; 
                    } else {
                        $data['process_status'] = 500 ;
                        $data['msg_process'] = 'Data updated fail !' ; 
                    } 
                }  
                return json_encode($data);
            }

        public function save_tag_label(Request $request) 
            {
                $str_req = explode("_",$request->trc_unix_id); 
                $str_id = explode("~",Crypt::decryptString(str_replace("-","=", $str_req[0]))); 
                $job_num = $str_id[0];
                $process_detail_id = $str_id[1];  

                if ($request->detail_trc_unix_id != '') {
                    $detail_trc_unix_id = $request->detail_trc_unix_id ;  
                } else {
                    $detail_trc_unix_id = 0 ;  
                } 

                $qty_pack = $request->std_pack ;  
                $home_line_detail_id = $request->home_line ;
                $operator_name = $request->operator_name ;
                $quality_name = $request->quality_operator_name ; 
                $model_name = $request->model_name ;  
                $qty_plan = $request->qty_plan ;  
                $original_plan = $request->original_plan ;  
                $part_num = $request->part_num ; 
                $part_name = str_replace(",", "__", $request->part_name) ;  
                $cust_name = $request->partner_code ;    
                $production_date = $request->production_date ;  
 
                $qty_before = ProductionSchedule::check_total_qty_before($job_num,$process_detail_id,$part_num,$detail_trc_unix_id);
                $validasi_total = ProductionSchedule::check_total_qty($job_num,$process_detail_id,$part_num);
          
                if (((($validasi_total - $qty_before) + $qty_pack)  - $original_plan) <= 0)
                {
                    $check_db = ProductionSchedule::check_line($job_num,$process_detail_id,$part_num,$detail_trc_unix_id) ; 
                    if ($check_db > 0) {
                        $generate = ProductionSchedule::update_tag_label($job_num,$process_detail_id,$qty_pack,$qty_pack,$home_line_detail_id,$operator_name,$quality_name,$model_name,$part_num,$part_name,$cust_name,$production_date,$detail_trc_unix_id);
                        if ($generate) {   
                            $data['process_status'] = 200 ; 
                            $data['msg_process'] = 'Data updated successfully' ; 
                        } else {
                            $data['process_status'] = 500 ; 
                            $data['msg_process'] = 'Fail data update!' ; 
                        }
                    } else { 
                        
                        $generate = ProductionSchedule::add_tag_label($job_num,$process_detail_id,$qty_pack,$qty_pack,$home_line_detail_id,$operator_name,$quality_name,$model_name,$part_num,$part_name,$cust_name,$production_date); 
                        if ($generate) {   
                            $data['process_status'] = 200 ; 
                            $data['msg_process'] = 'Data updated successfully' ; 
                        } else {
                            $data['process_status'] = 500 ; 
                            $data['msg_process'] = 'Fail data update!' ; 
                        }
                    } 
                } else {
                    $data['process_status'] = 500 ; 
                    // $data['msg_process'] = 'Total Qty melebihi planning !'.; 
                    $data['msg_process'] = 'Total Qty melebihi planning ! '.($validasi_total - $qty_before) + $qty_pack .' '.$original_plan.' '.((($validasi_total - $qty_before) + $qty_pack)  - $original_plan);
                }
                return json_encode($data);
            }

    public function clear_tag_label(Request $request){  
        $my_name = auth()->user()->username ;
        $str_req = explode("_",$request->trc_unix_id); 
        $str_id = explode("~",Crypt::decryptString(str_replace("-","=", $str_req[0]))); 
        $job_num = $str_id[0];
        $process_detail_id = $str_id[1]; 
        $part_num = $str_id[2]; 
        $delete = DB::table('t510_production_tag AS a')
        ->leftJoin('t510_InventoryMove AS b', function($join) {
            $join->on('a.job_num', '=', 'b.DocNumReference')
                ->on('a.process_detail_id', '=', 'b.DocNumReferenceLine')
                ->on('a.line_search_id', '=', 'b.DocNumReferenceLineRel')
                ->on('a.item_no', '=', 'b.PartNum');
        })
        ->where('a.job_num', '=', $job_num)
        ->where('a.process_detail_id', '=', $process_detail_id)
        ->where('a.item_no', '=', $part_num)
        ->whereNull('b.DocNumReference') 
        ->delete(); 
        if ($delete) {
            $data['process_status'] = 1 ;
            $data['msg_process'] = 'Delete successfully' ;  
        } else {
            $data['process_status'] = 0 ;
            $data['msg_process'] = 'Login sebagai admin data!' ;  
        }
        
        return json_encode($data);  
    }

    public function delete_tag_label(Request $request){  
        $my_name = auth()->user()->username ;
        $str_req = explode("_",$request->trc_unix_id); 
        $str_id = explode("~",Crypt::decryptString(str_replace("-","=", $str_req[0]))); 
        $job_num = $str_id[0];
        $process_detail_id = $str_id[1]; 
        $part_num = $str_id[2]; 
        $detail_trc_unix_id = $request->detail_trc_unix_id ; 
        $delete = DB::table('t510_production_tag AS a')
        ->leftJoin('t510_InventoryMove AS b', function($join) {
            $join->on('a.job_num', '=', 'b.DocNumReference')
                ->on('a.process_detail_id', '=', 'b.DocNumReferenceLine')
                ->on('a.line_search_id', '=', 'b.DocNumReferenceLineRel')
                ->on('a.item_no', '=', 'b.PartNum');
        })
        ->where('a.job_num', '=', $job_num)
        ->where('a.process_detail_id', '=', $process_detail_id)
        ->where('a.item_no', '=', $part_num)
        ->where('a.line_search_id', '=', $detail_trc_unix_id)
        ->whereNull('b.DocNumReference')
        ->delete();  
        if ($delete) {
            $data['process_status'] = 200 ;
            $data['msg_process'] = 'Delete successfully' ;  
        } else {
            $data['process_status'] = 500 ;
            $data['msg_process'] = 'Login sebagai admin data!' ;  
        }
        
        return json_encode($data);  
    } 

    public function export_production_sch(Request $request) {  
        date_default_timezone_set('Asia/Jakarta'); 
        $print_time = date ("m-d-Y H:i") ;  
        $date = $request->date_sch ; 
        $category =  $request->category ;
        $line =  $request->line ;
        $shift =  $request->shift ;
        $docnum =  $request->docnum ;     
        $db_data = ProductionSchedule::get_detail_list($date,$line,$shift,$docnum)->get(); 
          $d['doc_date'] = AppModel::local_date_formate_name($date)  ;
          $d['category'] = $category ;
          $d['line'] = $request->line  ;
          $d['docnum'] = $docnum ;
          $d['shift'] = $shift ;
          $d['db_data'] = $db_data ;
          $d['num'] = $db_data->count() ;
        return view('production_schedule.export_data',$d); 
    }


    function tag_print_view(Request $request) { 
        date_default_timezone_set('Asia/Jakarta'); 
        $print_time = date ("m-d-Y H:i") ;  
        $str_req = explode("_",$request->trc_unix_id);  
        $str_id = explode("~",Crypt::decryptString(str_replace("-","=", $str_req[0])));   
        $job_num = $str_id[0];
        $process_detail_id = $str_id[1];  
        $part_num = $str_id[2];     
        if (count($str_id) > 3) { 
            $line_search_id = $str_id[3];   
            $db_data = ProductionSchedule::get_detail_tag_label_id($job_num,$process_detail_id,$line_search_id,$part_num)->get();    
        } else {
            $db_data = ProductionSchedule::get_detail_tag_label($job_num,$process_detail_id,$part_num)->get(); 
        }   
        if($db_data->count() > 0){ 
            $data['ref_doc'] = $request->trc_unix_id ;    
            $data['layout'] = $request->layout ;    
            return view('production_schedule.tag_label_direct_print', $data);
        } else {
            $html = '
                <div class="col-md-12 col-lg-12 col-xl-12"> 
                    <div class="card h-100 flex-center bg-light-primary border-primary border border-dashed p-8"> 
                        <img src="' . env('APP_ASSETS') . 'assets/media/svg/files/upload.svg" class="alt="" /> <br/>
                            <a class="text-hover-primary fs-5 fw-bolder mb-2">No Data Entry</a> 
                        <div class="fs-7 fw-bold text-gray-400">Please entry detail of receipt !</div> 
                    </div> 
                </div>'; 
            return response($html, 200)->header('Content-Type', 'text/html');
        } 
    } 

    public function tag_label_print(Request $request) { 
        date_default_timezone_set('Asia/Jakarta'); 
        $print_time = AppModel::local_date_formate_name(date ("Y-m-d"))." ".date ("H:i") ;  
        $str_req = explode("_",$request->ref_doc);  
        $str_id = explode("~",Crypt::decryptString(str_replace("-","=", $str_req[0])));  
        $job_num = $str_id[0];
        $process_detail_id = $str_id[1];  
        $part_num = $str_id[2];   
        $total_pallet = ProductionSchedule::get_detail_tag_label_table($job_num,$process_detail_id,$part_num)->get()->count();   
        $last_pallet_num = ProductionSchedule::get_line_max($job_num, $process_detail_id, $part_num) ;   
        if (count($str_id) > 3) { 
            $line_search_id = $str_id[3];   
            $db_data = ProductionSchedule::get_detail_tag_label_id($job_num,$process_detail_id,$line_search_id,$part_num)->get();    
        } else {
            $db_data = ProductionSchedule::get_detail_tag_label($job_num,$process_detail_id,$part_num)->get(); 
        } 
        $w_top = array(95, 35, 60, 2, 30, 55);  
        $style = array(
            'border' => false,
            'vpadding' => 2,
            'hpadding' => 2,
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
            
        foreach ( $db_data as $db ) 
        {  
            $warehouse_code = ($db->ToWarehouseID == '' ? '05-10-01' : $db->ToWarehouseID) ; 
            $warehouse_name = strtoupper($db->ToWarehouseDesc) ;   
            $barcode = $db->item_no.'~'.$db->qty_1.'~'.$warehouse_code.'~'.$warehouse_name.'~'.$db->job_num.'~'.$db->job_num.'~'.$process_detail_id.'~'.$db->line_search_id ; 
            $so_date = AppModel::local_date_formate_name($db->production_date); 
            $log_date = AppModel::local_date_formate_name($db->log_date); 
            $created_by = $db->created_by ;
            $item_no = $db->item_no ;
            $item_name = $db->item_name ;  
            $partner_code = $db->cust_name ;    
              
            $qty_1 = $db->qty_1 ;   
            $pallet_no = $db->line_search_id - ($last_pallet_num - $total_pallet) ; 
            $qc_status = 'OK' ; 
            if ($db->EngineeringAlert != '') {
                $special_mark = $db->EngineeringAlert ;
            } else {
                $special_mark = '' ; 
            } 
           
            if ($request->layout == 2) {
                $add_height = 15 ;
            } else {
                $add_height = 0 ;
            }
            
            PDF::SetTitle("ProductinLabel_".$db->job_num);
            PDF::SetAuthor('Aji');
            PDF::setPrintHeader(false);
            PDF::SetTopMargin(5); 
            PDF::SetMargins(5, 5, 7, 7);
            PDF::SetAutoPageBreak(TRUE, 0); 
            PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);
            PDF::setPrintFooter(false);
            PDF::AddPage('P', 'A6');
            PDF::SetFillColor(255,255,255); 
            PDF::SetTextColor(0); 
            PDF::SetFont('dejavusans', '', 8); 

            PDF::Cell($w_top[0], 5, 'FO-34-1', 'B', 0, 'L', 0); 
            PDF::Ln();

            if ($request->layout == 2) {
                PDF::SetFont('dejavusans', 'B', 20);
                PDF::SetTextColor(255, 255, 255); // Set text color to white
                PDF::SetFillColor(0, 0, 0); // Set background color to black
                PDF::Cell(95, 15, 'EXPORT PART', 1, 0, 'C', 1); // Draw the cell with text and background 
                PDF::Ln();
            } 
  
            PDF::SetFillColor(255,255,255); 
            PDF::SetTextColor(0); 
            // Product Identification Title
            PDF::SetFont('dejavusans', 'B', 10);
            PDF::Cell(35, 7, '', 'TRL', 0, 'C', 0);
            PDF::SetFont('dejavusans', 'B', 10);
            PDF::Cell(60, 7, '', 'TRL', 0, 'C', 0);
            PDF::Ln();

            if ($db->EngineeringAlert != '') {
                PDF::SetFont('dejavusans', 'B', 20);
            } else {
                PDF::SetFont('dejavusans', 'B', 24);
            }
            PDF::Cell(35, 4, 'OK', 'RL', 0, 'C', 0); 
            PDF::SetFont('dejavusans', 'B', 10); 
            PDF::Cell(60, 4, 'PRODUCT IDENTIFICATION', 'RL', 0, 'C', 0);
            PDF::Ln();

            // Production Details
            if ($db->EngineeringAlert != '') {
                PDF::SetFont('dejavusans', 'B', 20);
            } else {
                PDF::SetFont('dejavusans', 'B', 24);
            }
            PDF::Cell(35, 7, $special_mark, 'RL', 0, 'C', 0);  
            PDF::SetFont('dejavusans', '', 9); 
            PDF::Cell(60, 7, 'Prod. Date: '.$so_date, 'BRL', 0, 'C', 0);
            PDF::Ln();
            

            PDF::SetFont('dejavusans', 'B', 10);
            PDF::Cell(35, 6, '', 'BRL', 0, 'L', 0);
            PDF::Cell(60, 6, 'Line : '.$db->home_line_detail_id, 'BRL', 0, 'C', 0);
            PDF::Ln();
            

            // Pallet Number Section
            PDF::SetFont('dejavusans', '', 9);
            PDF::Cell(35, 7, 'Lot : ' . $db->job_num, 1, 0, 'C', 0);
            PDF::Cell(30, 7, 'Pallet No.', 1, 0, 'C', 0);
            PDF::Cell(30, 7, 'QC CHECK', 1, 1, 'C', 0);

            // Quantity and Location
            PDF::SetFont('dejavusans', 'B', 10);
            PDF::Cell(35, 15, "QTY : $qty_1", 1, 0, 'C', 0);
            PDF::Cell(30, 15, "$pallet_no / $total_pallet", 1, 0, 'C', 0);
            PDF::Cell(30, 15, $db->quality_name, 1, 1, 'C', 0);

            PDF::SetFont('dejavusans', '', 9);
            PDF::Cell(35, 1, '', 'TRL', 0, 'C', 0); 
            PDF::SetFont('dejavusans', '', 8);
            PDF::Cell(60, 1, '', 'TRL', 1, 'C', 0); 

            PDF::SetFont('dejavusans', '', 8); 
            PDF::Cell(35, 6, '', 'RL', 0, 'C', 0);  
            PDF::Cell(11, 6, "", 'L', 0, 'C', 0);

            PDF::SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0))); 
            PDF::SetFont('dejavusans', '', 5); 
            PDF::Cell(6, 6, "PD", 1, 0, 'C', 0);
            PDF::Cell(2, 6, "", 'RL', 0, 'C', 0);
            PDF::Cell(6, 6, "QC", 1, 0, 'C', 0);
            PDF::Cell(2, 6, "", 'RL', 0, 'C', 0);
            PDF::Cell(6, 6, "WH1", 1, 0, 'C', 0);
            PDF::Cell(2, 6, "", 'RL', 0, 'C', 0);
            PDF::Cell(6, 6, "WH2", 1, 0, 'C', 0);
            PDF::Cell(2, 6, "", 'RL', 0, 'C', 0);
            PDF::Cell(6, 6, "WH3", 1, 0, 'C', 0);
            PDF::SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0))); 
            PDF::Cell(11, 6, "", 'R', 0, 'C', 0);

            PDF::Ln(); 

            PDF::SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

            PDF::SetFont('dejavusans', '', 9);
            PDF::Cell(35, 1, '', 'RL', 0, 'C', 0); 
            PDF::SetFont('dejavusans', '', 8);
            PDF::Cell(60, 1, '', 'RL', 0, 'C', 0); 
            PDF::Ln(1); 


            PDF::SetFont('dejavusans', '', 8); 
            PDF::Cell(35, 6, '', 'RL', 0, 'C', 0);  
            PDF::Cell(11, 6, "", 'L', 0, 'C', 0);

            PDF::SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0))); 
            PDF::Cell(6, 6, "", 1, 0, 'C', 0);
            PDF::Cell(2, 6, "", 'RL', 0, 'C', 0);
            PDF::Cell(6, 6, "", 1, 0, 'C', 0);
            PDF::Cell(2, 6, "", 'RL', 0, 'C', 0);
            PDF::Cell(6, 6, "", 1, 0, 'C', 0);
            PDF::Cell(2, 6, "", 'RL', 0, 'C', 0);
            PDF::Cell(6, 6, "", 1, 0, 'C', 0);
            PDF::Cell(2, 6, "", 'RL', 0, 'C', 0);
            PDF::Cell(6, 6, "", 1, 0, 'C', 0);
            PDF::SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0))); 
            PDF::Cell(11, 6, "", 'R', 0, 'C', 0);
            PDF::Ln();

            PDF::SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
            PDF::SetFont('dejavusans', '', 9);
            PDF::Cell(35, 1, '', 'RL', 0, 'C', 0); 
            PDF::SetFont('dejavusans', '', 8);
            PDF::Cell(60, 1, '', 'BRL', 1, 'C', 0); 

            PDF::Cell(35, 7, '', 'RL', 0, 'C', 0); 
            PDF::SetFont('dejavusans', 'B', 9);
            PDF::Cell(60, 7, "Model : $db->model_name", 1, 0, 'C', 0);
            PDF::Ln();

            PDF::Cell(35, 7, '', 'BRL', 0, 'C', 0); 
            PDF::SetFont('dejavusans', 'B', 9);
            PDF::Cell(60, 7, "$item_no", 1, 0, 'C', 0);
            PDF::write2DBarcode($barcode, 'QRCODE,L', 7.5, (58 + $add_height), 30, 30, $style, 'N');     
            PDF::Ln(2.6); 
            PDF::Cell(95, 7, $item_name, 1, 1, 'C', 0); 
            PDF::Cell(95, 7, $warehouse_name, 1, 1, 'C', 0);
 
            // Footer Section
            PDF::SetFont('dejavusans', '', 9);
            PDF::Cell(95, 7, 'Created : '.$created_by.' / '.$log_date, 1, 1, 'C', 0);
            PDF::Cell(95, 7, 'Printed : '.auth()->user()->username.' / '.$print_time, 1, 1, 'C', 0);

            PDF::Ln(10);
  
                
            }  
            PDF::Output('ProductinLabel.pdf');
    }

}
