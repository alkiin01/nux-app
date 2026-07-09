<?php

namespace App\Http\Controllers;

use App\Models\InventoryMoveIn;
use App\Models\AppModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt; 
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;

class InventoryMoveInController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $my_id = Auth::user()->id;
        $segment_number = env('SEGMENT_NUM');
        $uri = explode("/", url()->current());
        if (count($uri) <= $segment_number) {
            $menu = $this->menu($my_id, 'home');
        } else {
            $menu = $this->menu($my_id, $uri[$segment_number]);
        }
        $data['head_title'] = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'];
        $data['menu_level_2'] = $menu['menu_level_2'];
        $data['menu_level_3'] = $menu['menu_level_3'];
        $data['menu_level_4'] = $menu['menu_level_4'];
        $data['wh_list'] = InventoryMoveIn::get_wh_list() ;
        return view('inventory_move_in/inventory_move_in_index', $data);
    }
     

    public function delete_header(Request $request)
    {
        $laborHedSeq = 0;
        $laborDtlSeq = 0;
        if (!empty($request->laborHedSeq)) {
            $laborHedSeq = Crypt::decryptString(str_replace("-", "=", $request->laborHedSeq));
        }
        if (!empty($request->laborDtlSeq)) {
            $laborDtlSeq = Crypt::decryptString(str_replace("-", "=", $request->laborDtlSeq));
        }
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $data = [];
        $host_api = self::get_host_api();
        try {
            $response = $client->request('DELETE', $host_api . 'Labor/DeleteDtl', [
                'json' => [
                    'laborHedSeq' => $laborHedSeq,
                    'laborDtlSeq' => $laborDtlSeq,
                    'nik' => "$username",
                    'password' => "$password"
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);
            $data['code'] = $responseBody['code'];
            $data['status'] = $responseBody['status'];
        } catch (RequestException $e) {
            \Log::error('API request failed', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(), 
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);

            $data['code'] = 500;
            $data['status'] = $e->getMessage();
        }
        return $data;
    }

    // public function submit_form_packlist(Request $request){

    // }

    public function submit_form_mit(Request $request)
    { 

        $mit = $request->DocNumReference;
        $PartNum = $request->InptPartNum;
        $Qty = (int) $request->InptQty;
        $line = $request->DocNumReferenceLine;
        $LineRel = $request->DocNumReferenceLineRel ;
        $LotNum = $request->LotNum;
        $ToBinID = $request->ToBinID;
        $DocNum = explode("~", $request->InptDocNum) ; 
        $TranTypeID = $DocNum[0];
        $MonthID = $DocNum[1];
        $TranSeqID = $DocNum[2]; 

        $ToWarehouseID = $request->ToWarehouseID; 
        $ToWarehouseIDFromTag = $request->ToWarehouseIDFromTag; 
        
         
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $data = [];
        $host_api = self::get_host_api(); 
        
        if ($ToWarehouseID != $ToWarehouseIDFromTag) {
            $data['code'] = 500 ;
            $data['status'] = 'Warehouse tidak sesuai!' ;
            return $data;
        } else { 
            $check_barcode = InventoryMoveIn::check_barcode($mit, $line, $LineRel, $PartNum);
            if ($check_barcode > 0) {
                $data['code'] = 500 ;
                $data['status'] = 'Barcode sudah discan!' ;
            } else {

                try {
                    $response = $client->request('POST', $host_api . 'Mit/ScanProcess', [
                        'json' => [
                            'MIT' => "$mit",
                            'partNumber' => "$PartNum",
                            'qty' => $Qty,
                            'line' => "$line", 
                            'isTransferred' => true,
                            'nik' => "$username",
                            'password' => "$password"
                        ],
                        'headers' => [
                            'Content-Type' => 'application/json',
                        ],
                        'verify' => false,
                    ]);
        
                    $responseBody = json_decode($response->getBody()->getContents(), true);
                    $data['code'] = $responseBody['code'];
                    $data['status'] = $responseBody['status'];
                } catch (RequestException $e) {
                    \Log::error('API request failed', [
                        'message' => $e->getMessage(),
                        'request' => $e->getRequest(),
                        'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
                    ]); 
                    $data['code'] = 500;
                    $data['status'] = $e->getMessage();
                }     
                if ($data['code'] == 200) {
                    $push_jonum = InventoryMoveIn::push_inventory_dtl($mit, $PartNum, $Qty, $line, $LotNum, $ToBinID, $TranTypeID, $MonthID, $TranSeqID, $LineRel) ; 
                } 
            } 
            return $data;
        }
    }
    public function submit_form_packlist(Request $request)
    { 
        $PoNum = $request->DocNumReference;
          $PartNum = $request->InptPartNum;
        $Qty = (int) $request->InptQty;
        $opSeq = $request->DocNumReferenceLine;
        $LineRel = $request->DocNumReferenceLineRel ;
        $LotNum = $request->LotNum;
        $ToBinID = $request->ToBinID;
        $ToWarehouseID = $request->ToWarehouseID; 
        $ToWarehouseIDFromTag = $request->ToWarehouseIDFromTag; 
        $ToWarehouseDesc = InventoryMoveIn::get_name_warehouse($request->ToWarehouseID) ; 
        $DocNum = explode("~", $request->InptDocNum) ; 
        $TranTypeID = $DocNum[0];
        $MonthID = $DocNum[1];
        $TranSeqID = $DocNum[2];  
        $WarehseFrm = $request->WarehouseFrom ;
         $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $data = [] ;
        $host_api = self::get_host_api();  

        if ($ToWarehouseID != $ToWarehouseIDFromTag) {
            $data['code'] = 500 ;
            $data['status'] = 'Warehouse tidak sesuai!, Label ini hanya untuk tujuan Warehouse Finish Good' ;
            return $data;
        } else{
            $check_barcode = InventoryMoveIn::check_barcode($PoNum, $opSeq, $LineRel, $PartNum);   
             if ($check_barcode > 0) {
                $data['code'] = 500 ;
                $data['status'] = 'Barcode sudah discan!' ;
            } else{
            try {
                    $response = $client->request('POST', $host_api . 'Mit/ScanOnly', [
                        'json' => [
                            'qty' => $Qty,
                            'lotNumFrm' => "$LotNum", 
                            'warehseTo' => "$ToWarehouseID",  
                            'warehseFrm' => "$WarehseFrm",  
                            'partNumber' => "$PartNum",
                            'binFrm' => "$ToBinID",  
                            'nik' => "$username",
                            'password' => "$password"
                        ],
                        'headers' => [
                            'Content-Type' => 'application/json',
                        ],
                        'verify' => false,
                    ]);
                    $responseBody = json_decode($response->getBody()->getContents(), true); 
                    $data['code'] = $responseBody['code'];
                    $data['status'] = $responseBody['status'];
                    if ($responseBody['code'] == 200) {
                        $FromWarehouseID = $responseBody['data']['warehouseFrom'] ;
                        $FromWarehouseDesc = InventoryMoveIn::get_name_warehouse($responseBody['data']['warehouseFrom']) ;
                        $FromBinID = $responseBody['data']['binFrom'] ; 
                    } 
                } catch (RequestException $e) {
                    \Log::error('API request failed', [
                        'message' => $e->getMessage(),
                        'request' => $e->getRequest(),
                        'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
                    ]); 
                    $data['code'] = 500;
                    $data['status'] = $e->getMessage();
                }
            }
        }
    }
    public function submit_form_job(Request $request)
    {  
        $jobnum = $request->DocNumReference;
        $PartNum = $request->InptPartNum;
        $Qty = (int) $request->InptQty;
        $opSeq = $request->DocNumReferenceLine;
        $LineRel = $request->DocNumReferenceLineRel ;
        $LotNum = $request->LotNum;
        $ToBinID = $request->ToBinID;
        $ToWarehouseID = $request->ToWarehouseID; 
        $ToWarehouseIDFromTag = $request->ToWarehouseIDFromTag; 
        $ToWarehouseDesc = InventoryMoveIn::get_name_warehouse($request->ToWarehouseID) ; 
        $DocNum = explode("~", $request->InptDocNum) ; 
        $TranTypeID = $DocNum[0];
        $MonthID = $DocNum[1];
        $TranSeqID = $DocNum[2];  
         
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $data = [] ;
        $host_api = self::get_host_api();  

        if ($ToWarehouseID != $ToWarehouseIDFromTag) {
            $data['code'] = 500 ;
            $data['status'] = 'Warehouse tidak sesuai!' ;
            return $data;
        } else { 
            $check_barcode = InventoryMoveIn::check_barcode($jobnum, $opSeq, $LineRel, $PartNum);   
            if ($check_barcode > 0) {
                $data['code'] = 500 ;
                $data['status'] = 'Barcode sudah discan!' ;
            } else { 
                try {
                    $response = $client->request('POST', $host_api . 'JobRec/BypassUpdate', [
                        'json' => [
                            'jobNum' => "$jobnum",
                            'qty' => $Qty,
                            'lotNum' => "$LotNum", 
                            'wareHouseCode' => "$ToWarehouseID",  
                            'partNum' => "$PartNum",
                            'binNum' => "$ToBinID",  
                            'nik' => "$username",
                            'password' => "$password"
                        ],
                        'headers' => [
                            'Content-Type' => 'application/json',
                        ],
                        'verify' => false,
                    ]);
        
                    $responseBody = json_decode($response->getBody()->getContents(), true); 
                    $data['code'] = $responseBody['code'];
                    $data['status'] = $responseBody['status'];
                    if ($responseBody['code'] == 200) {
                        $FromWarehouseID = $responseBody['data']['warehouseFrom'] ;
                        $FromWarehouseDesc = InventoryMoveIn::get_name_warehouse($responseBody['data']['warehouseFrom']) ;
                        $FromBinID = $responseBody['data']['binFrom'] ; 
                    } 
                } catch (RequestException $e) {
                    \Log::error('API request failed', [
                        'message' => $e->getMessage(),
                        'request' => $e->getRequest(),
                        'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
                    ]); 
                    $data['code'] = 500;
                    $data['status'] = $e->getMessage();
                }   
                if ($data['code'] == 200) { 
                    $push_jonum = InventoryMoveIn::push_inventory_dtl_from_job($jobnum, $PartNum, $Qty, $opSeq, $LotNum, $ToBinID, $TranTypeID, $MonthID, $TranSeqID, $LineRel, $FromWarehouseID, $FromWarehouseDesc, $FromBinID, $ToWarehouseID, $ToWarehouseDesc) ; 
                } 
            } 
            return $data;
        }
    }
    
  
    public function add_document(Request $request)
    {
        $data['wh_list'] = InventoryMoveIn::get_wh_list() ; 
        return view('inventory_move_in/form', $data);
    }

    public function get_new_docnum(Request $request)
    { 
        $data['DocNum'] = InventoryMoveIn::get_new_docnum($request->DocDate) ; 
        echo json_encode($data);  
    }
 

    public function front_table(Request $request)
    {
        $CreatedBy = $request->CreatedAt;
        $ToWarehouseID =  $request->ToWarehouseID; 
        $search =  str_replace("-", "~", $request->front_table_search) ; 
        $columns = array(
            0 => 'DocNum',
            1 => 'DocNum',
            2 => 'DocNum',
            3 => 'DocDate',
            4 => 'ToWarehouseDesc',
            5 => 'CreatedBy',
            6 => 'TotalLine'
        );
        $totalData = InventoryMoveIn::get_detail_list($CreatedBy, $ToWarehouseID);
        $totalData = $totalData->get()->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column') == 0 ? $columns[1] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column') == 0 ? 'desc' : $request->input('order.0.dir'));
        if (empty($search)) {
            $posts = InventoryMoveIn::get_detail_list($CreatedBy, $ToWarehouseID)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $posts =  InventoryMoveIn::get_detail_list($CreatedBy, $ToWarehouseID)
                ->where(function ($query) use ($search) {
                    $query->where('ToWarehouseDesc', 'LIKE', "%$search%");
                    $query->orWhere('UpdatedBy', 'LIKE', "%$search%");
                    $query->orWhere('DocNum', 'LIKE', "%$search%");
                })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = InventoryMoveIn::get_detail_list($CreatedBy, $ToWarehouseID)
                ->where(function ($query) use ($search) {
                    $query->where('ToWarehouseDesc', 'LIKE', "%$search%");
                    $query->orWhere('UpdatedBy', 'LIKE', "%$search%");
                    $query->orWhere('DocNum', 'LIKE', "%$search%");
                })->get()->count();
        }
        $data = array();
        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $DocNum = "'".$post->DocNum."'"; 
                $button = '<button type="button" title="Generate Label" class="btn btn-light-primary btn-sm" id="btn_form_view_doc_' . $no . '" onclick="open_document(' . $DocNum . ',' . $no . ')" style="text-align: center; width: 40px; height: 35px;">
                            <span id="svg_form_view_doc_' . $no . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path>
                                <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path>
                              </svg>
                            </span>
                            <span id="spinner_form_view_doc_' . $no . '" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
                        </button>'; 

                $nestedData['no'] = $no;
                $nestedData['DocNum'] =  $post->DocNum ;
                $nestedData['DocDate'] = $post->DocDate ;
                $nestedData['CreatedAt'] = $post->CreatedAt ;
                $nestedData['CreatedBy'] = $post->CreatedBy ;
                $nestedData['ToWarehouseID'] = $post->ToWarehouseID ;
                $nestedData['ToWarehouseDesc'] = $post->ToWarehouseDesc ;
                $nestedData['UpdatedBy'] = $post->UpdatedBy ;
                $nestedData['LastUpdated'] = $post->LastUpdated ;
                $nestedData['TotalLine'] = $post->TotalLine ; 
                $nestedData['action'] = $button;
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
    
 
    public function get_preview_doc(Request $request)
    {
        $laborHedSeq = 0 ;
        $laborDtlSeq = 0 ;
        if (!empty($request->laborHedSeq)) { $laborHedSeq = Crypt::decryptString(str_replace("-", "=", $request->laborHedSeq)); }
        if (!empty($request->laborDtlSeq)) { $laborDtlSeq = Crypt::decryptString(str_replace("-", "=", $request->laborDtlSeq)); } 
        $query = InventoryMoveIn::get_part_num_list($jobNum, $opSeq, $laborHedSeq, $laborDtlSeq)->where('item_no', "$InptPartNum"); 
        if ($query->count() > 0) {
            foreach ($query->get() AS $row) {
                $data['code'] = 200 ;
                $data['home_line'] = $row->home_line ;
                $data['home_line_detail_id'] = $row->home_line_detail_id ;
                $data['item_name'] = $row->item_name ;
                $data['qty_plan'] = (int) $row->qty_plan ;
                $data['model_name'] = $row->model_name ;
                $data['QtyCompleted'] = (int) ($request->isCopart == 1 ? $row->CoPartQtyCompleted : $row->QtyCompleted) ;
                $data['DiscrepQty'] =  (int) ($request->isCopart == 1 ? $row->CoPartDiscrepQty : $row->DiscrepQty) ;
                $data['ScrapQty'] =  (int) ($request->isCopart == 1 ? $row->CoPartScrapQty : $row->ScrapQty) ;
                $data['DiscrpRsnCode'] =  ($request->isCopart == 1 ? $row->CoPartDiscrpRsnCode : $row->DiscrpRsnCode) ;
                $data['ScrapReasonCode'] =  ($request->isCopart == 1 ? $row->CoPartScrapReasonCode : $row->ScrapReasonCode) ;
                $data['DiscrpRsnCodeDesc'] = ($data['DiscrpRsnCode'] == '' ? '' :InventoryMoveIn::get_descr_reason_code($data['DiscrpRsnCode']));
                $data['ScrapReasonCodeDesc'] = ($data['ScrapReasonCode'] == '' ? '' :InventoryMoveIn::get_descr_reason_code($data['ScrapReasonCode']));
                $data['category'] =  ( (strpos($row->dept, 'ASSY') !== false) ? 'ASSY' : (
                                (strpos($row->dept, 'STP') !== false) ? 'STP' : (
                                    (strpos($row->dept, 'PPIC') !== false) ? 'PPIC' : ''
                                )
                            )
                        ) ;
            }
        } else {
            $data['home_line'] = '' ;
            $data['home_line_detail_id'] = '' ;
            $data['item_name'] = '' ;
            $data['qty_plan'] = '' ;
            $data['model_name'] = '' ;
            $data['QtyCompleted'] = '' ;
            $data['DiscrepQty'] =  '' ;
            $data['ScrapQty'] =  '' ;
            $data['DiscrpRsnCode'] =  '' ;
            $data['ScrapReasonCode'] =  '' ;
            $data['category'] =  '' ;
            $data['code'] = 200 ;
            $data['status'] = "Data Part tidak ditemukan!" ;
        }
        echo json_encode($data);
    }

    public function submit_delete_item(Request $request) {  
        $trc_id = explode("~", Crypt::decryptString(str_replace("-", "=", $request->trc_id))) ;   
        $TranTypeID = $trc_id[0];
        $MonthID = $trc_id[1];
        $TranSeqID = $trc_id[2];
        $LineID = $trc_id[3];
        
        $db_detail = InventoryMoveIn::get_line_detail($TranTypeID, $MonthID, $TranSeqID, $LineID);
        if($db_detail->count() > 0) {
            foreach ($db_detail AS $row) {
                $DocNumReference = $row->DocNumReference ;
                $PartNum = $row->PartNum ;
                $Qty = $row->QtyMove ;
                $DocNumReferenceLine = $row->DocNumReferenceLine ; 
                $ToWarehouseID = $row->ToWarehouseID ;
                $ToBinID = $row->ToBinID ; 
                $LotNum = $row->LotNum ; 
            }
        } else {
            $DocNumReference = '' ;
            $PartNum = '' ;
            $Qty = '' ;
            $DocNumReferenceLine = '' ; 
            $ToWarehouseID = '' ; 
            $ToBinID = '' ;
            $LotNum = 'A' ; 
        }
       
        if (str_contains($DocNumReference, 'MIT')) {
           $data = self::delete_receipt_mit($TranTypeID, $MonthID, $TranSeqID, $LineID, $DocNumReference, $PartNum, $Qty, $DocNumReferenceLine) ;
        } else {
            $data = self::delete_receipt_job($TranTypeID, $MonthID, $TranSeqID, $LineID, $DocNumReference, $PartNum, $Qty, $LotNum, $ToBinID, $ToWarehouseID) ;
        }
        echo json_encode($data);
    }

    public function delete_receipt_mit($TranTypeID, $MonthID, $TranSeqID, $LineID, $DocNumReference, $PartNum, $Qty, $DocNumReferenceLine)
    {   
        $Qty = (int) ($Qty * -1);
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $data = [];
        $host_api = self::get_host_api();  
        // dd($TranTypeID, $MonthID, $TranSeqID, $LineID, $DocNumReference, $PartNum, $Qty, $DocNumReferenceLine);
        try {
            $response = $client->request('POST', $host_api . 'Mit/ScanProcess', [
                'json' => [
                    'MIT' => "$DocNumReference",
                    'partNumber' => "$PartNum",
                    'qty' => $Qty,
                    'line' => "$DocNumReferenceLine", 
                    'isTransferred' => true,
                    'nik' => "$username",
                    'password' => "$password"
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true); 
            // dd($responseBody);
            $data['code'] = $responseBody['code'];
            $data['status'] = $responseBody['status'];
        } catch (RequestException $e) {
            \Log::error('API request failed', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]); 
            $data['code'] = 500;
            $data['status'] = $e->getMessage();
        }     
        // dd($TranTypeID, $MonthID, $TranSeqID, $LineID);
        if ($data['code'] == 200) {
            $delete_history = InventoryMoveIn::delete_inventory_dtl($TranTypeID, $MonthID, $TranSeqID, $LineID) ; 
        } 
     
        return $data;
    }

    public function delete_receipt_job($TranTypeID, $MonthID, $TranSeqID, $LineID, $DocNumReference, $PartNum, $Qty, $LotNum, $ToBinID, $ToWarehouseID)
    {  
        $jobnum = $DocNumReference ; 
        $Qty = (int) ($Qty * -1); 
        $LotNum = $LotNum;
        $ToBinID = $ToBinID;
        $ToWarehouseID = $ToWarehouseID;    
         
        // dd($TranTypeID, $MonthID, $TranSeqID, $LineID, $DocNumReference, $PartNum, $Qty, $LotNum, $ToBinID, $ToWarehouseID);
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $data = [] ;
        $host_api = self::get_host_api();   
        try {
            $response = $client->request('POST', $host_api . 'JobRec/BypassUpdate', [
                'json' => [
                    'jobNum' => "$jobnum",
                    'qty' => $Qty,
                    'lotNum' => "$LotNum", 
                    'wareHouseCode' => "$ToWarehouseID",  
                    'partNum' => "$PartNum",
                    'binNum' => "$ToBinID",  
                    'nik' => "$username",
                    'password' => "$password"
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true); 
            $data['code'] = $responseBody['code'];
            $data['status'] = $responseBody['status']; 
        } catch (RequestException $e) {
            \Log::error('API request failed', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]); 
            $data['code'] = 500;
            $data['status'] = $e->getMessage();
        }   
        if ($data['code'] == 200) {
            $delete_history = InventoryMoveIn::delete_inventory_dtl($TranTypeID, $MonthID, $TranSeqID, $LineID) ; 
        }  
        return $data;
    }


    public function detail_table(Request $request)
    { 
        $str_id = explode("~", $request->DocNum) ;
        $TranTypeID = $str_id[0];
        $MonthID = $str_id[1];
        $TranSeqID = $str_id[2];
        $search = $request->input('search');

        // dd($search == null ? 1 : 0);
        $columns = array(
            0 => 'a.LineID',
            1 => 'a.PartNum',
            2 => 'a.QtyMove',
            3 => 'a.FromWarehouseID',
            4 => 'a.ToWarehouseID',
        );

        $totalData = InventoryMoveIn::get_detail_table($TranTypeID, $MonthID, $TranSeqID)->get()->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[0];
        $dir = ($request->input('order.0.column') == 0 ? 'desc' : $request->input('order.0.dir'));
        if ($search == null) {
            $posts = InventoryMoveIn::get_detail_table($TranTypeID, $MonthID, $TranSeqID)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else { 
            $posts =  InventoryMoveIn::get_detail_table($TranTypeID, $MonthID, $TranSeqID)
                ->where('a.PartNum', 'LIKE', "%$search%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = InventoryMoveIn::get_detail_table($TranTypeID, $MonthID, $TranSeqID)
                    ->where('a.PartNum', 'LIKE', "%$search%")
                    ->get()->count(); 
        }
        
        $data = array();
        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                
                $no++;
                $trc_id = "'" .str_replace("=", "-", Crypt::encryptString($post->TranTypeID . '~' . $post->MonthID . '~' . $post->TranSeqID . '~' . $post->LineID)). "'" ;   
                $DocRef = "'" .$post->DocNumReference.' - '.$post->DocNumReferenceLine.'/'.$post->DocNumReferenceLineRel. "'" ;
                
                $button = '<button type="button" title="Delete Label" class="btn btn-light-primary btn-sm" id="btn_delete_item_' . $no . '" onclick="delete_item('. $trc_id .',' . $no . ',' .$DocRef.')" style="text-align: center; width: 40px; height: 35px;">
                    <span id="svg_delete_item_' . $no . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                <defs/>
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                    <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                </g>
                            </svg>
                        </span>
                    <span id="spinner_delete_item_' . $no . '" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>
                </button>' ;
                $nestedData['no'] = $no ;
                $nestedData['PartNum'] = $post->PartNum .' ('.$post->DocNumReference.'/'.$post->DocNumReferenceLine.'/'.$post->DocNumReferenceLineRel.')' ;
                $nestedData['Qty'] =  number_format($post->QtyMove,0) ;
                $nestedData['FromWarehouseDesc'] = $post->FromWarehouseDesc ;
                $nestedData['ToWarehouseDesc'] = $post->ToWarehouseDesc ;
                $nestedData['action'] = $button;
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
    public function showBin(Request $request){
        $WarehouseCode = $request->warehouseCode;
        $result = InventoryMoveIn::showBin($WarehouseCode);
        return response()->json($result);
    }
   
}
