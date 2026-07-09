<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use App\Models\AppModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;

class TimeEntryController extends Controller
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
        return view('time_entry/time_entry_index', $data);
    }
    
    public function submit_header(Request $request)
    {
        $InptActualClockinDate = $request->InptActualClockinDate;
        $InptEmployeeID = $request->InptEmployeeID;
        $InptShiftID = $request->InptShiftID;
        if ($request->InptShiftID < 1) {
            $data['code'] = 500 ;
            $data['status'] = "Shift Harus diisi!";
        } else {
            if (!empty($request->laborHedSeq)) {
                $laborHedSeq = Crypt::decryptString(str_replace("-", "=", $request->laborHedSeq));
            }
            if (empty($request->laborHedSeq)) {
                    $GetLaborHedSeq = TimeEntry::get_labor_hed_seq($InptActualClockinDate, $InptShiftID, $InptEmployeeID);
                        if ($GetLaborHedSeq > 0) {
                            $SetShift = self::set_shift($GetLaborHedSeq, $InptShiftID);
                            if ($SetShift['code'] == 200) {
                                $data['laborHedSeq'] = Crypt::encryptString(str_replace("=", "-", $GetLaborHedSeq));
                                $data['code'] = $SetShift['code'];
                                $data['status'] = "Data berhasil Update !";
                                $data['payHour'] = $SetShift['payHour']; 
                                $data['clockInTime'] = $SetShift['clockInTime'];
                                $data['clockOutTime'] = $SetShift['clockOutTime'];
                            } else {
                                $data['code'] = $SetShift['code'];
                                $data['status'] = $SetShift['status'];
                                $data['payHour'] = '';
                                $data['clockInTime'] ='';
                                $data['clockOutTime'] = '';
                            }
                        } else {  
                            $GetNewHeader = self::get_new_header($InptActualClockinDate, $InptEmployeeID);
                            if ($GetNewHeader['code'] == 200) {
                                $data['laborHedSeq'] = Crypt::encryptString(str_replace("=", "-", $GetNewHeader['laborHedSeq']));
                                $SetShift = self::set_shift($GetNewHeader['laborHedSeq'], $InptShiftID);
                                if ($SetShift['code'] == 200) {
                                    $data['code'] = $SetShift['code'];
                                    $data['status'] = "Data berhasil Ditambah !";
                                    $data['payHour'] = $SetShift['payHour']; 
                                    $data['clockInTime'] = $SetShift['clockInTime'];
                                    $data['clockOutTime'] = $SetShift['clockOutTime'];
                                } else {
                                    $data['code'] = $SetShift['code'];
                                    $data['status'] = $SetShift['status'];
                                    $data['payHour'] = '';
                                    $data['clockInTime'] ='';
                                    $data['clockOutTime'] = '';
                                }
                            }  
                        }
            } else {
                $SetShift = self::set_shift($laborHedSeq, $InptShiftID);
                if ($SetShift['code'] == 200) {
                    $data['laborHedSeq'] = Crypt::encryptString(str_replace("=", "-", $laborHedSeq));
                    $data['code'] = $SetShift['code'];
                    $data['status'] = "Data berhasil Update !";
                    $data['payHour'] = $SetShift['payHour']; 
                    $data['clockInTime'] = $SetShift['clockInTime'];
                    $data['clockOutTime'] = $SetShift['clockOutTime'];
                } else {
                    $data['code'] = $SetShift['code'];
                    $data['status'] = $SetShift['status'];
                    $data['payHour'] = '';
                    $data['clockInTime'] ='';
                    $data['clockOutTime'] = '';
                }
            }
        }
        echo json_encode($data);
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

    public function submit_form(Request $request)
    {
        $laborHedSeq = 0;
        $laborDtlSeq = 0;
        $data['buttonSave']  = '' ;
        $data['buttonSubmit']  = '' ;
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
            $response = $client->request('POST', $host_api . 'Labor/Submit', [
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

        $strJobNum = explode("~", $request->InptJobNum) ; 
        $JoNum = str_replace("_SPACE_", " ", $strJobNum[0]) ;  
        $OprSeq = $strJobNum[1] ; 

        if ($data['code'] == 200) {
            $push_jonum = TimeEntry::push_jo_number_to_labor_dtl($laborHedSeq, $laborDtlSeq, $JoNum, $OprSeq) ;
            $data['status'] .= "$push_jonum" ;
        }
        return $data;
    }

    public function recall_form(Request $request)
    {
        $laborHedSeq = 0;
        $laborDtlSeq = 0;
        $data['buttonSave']  = '' ;
        $data['buttonSubmit']  = '' ;
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
            $response = $client->request('POST', $host_api . 'Labor/Recall', [
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

    public function check_document_status(Request $request)
    {
        $laborHedSeq = 0;
        $laborDtlSeq = 0;
        $data['buttonSave']  = '' ;
        $data['buttonSubmit']  = '' ;
        $data['buttonDelete']  = '' ;
        if (!empty($request->laborHedSeq)) {
            $laborHedSeq = Crypt::decryptString(str_replace("-", "=", $request->laborHedSeq));
        }
        if (!empty($request->laborDtlSeq)) {
            $laborDtlSeq = Crypt::decryptString(str_replace("-", "=", $request->laborDtlSeq));
        }
        $check_document = TimeEntry::check_document_status($laborHedSeq,  $laborDtlSeq) ; 
        $data['TimeStatus'] = $check_document ;
            if ($check_document == 0) {  
                $data['buttonSave'] = '
                    <button type="button" class="btn btn-primary btn-sm" id="btn_save_header" onclick="save_time_entry()">
                            <span id="svg_save_header" class="svg-icon svg-icon-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                    <g stroke="none" fill="none">
                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                        <path d="M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z" fill="#000000"/>
                                        <rect fill="#000000" opacity="0.3" x="12" y="4" width="3" height="5" rx="0.5"/>
                                    </g>
                                </svg>
                            </span> 
                            <span id="spinner_save_header" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>       
                            <span id="btn_text_save_header">Save</span>
                        </button>
                ' ;
                $data['buttonSubmit'] = '
                    <button type="button" class="btn btn-success btn-sm" id="btn_submit_form" onclick="submit_form()">
                        <span id="svg_submit_form" class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                <defs/>
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <polygon fill="#000000" opacity="0.3" points="6 3 18 3 20 6.5 4 6.5"/>
                                    <path d="M6,5 L18,5 C19.1045695,5 20,5.8954305 20,7 L20,19 C20,20.1045695 19.1045695,21 18,21 L6,21 C4.8954305,21 4,20.1045695 4,19 L4,7 C4,5.8954305 4.8954305,5 6,5 Z M9,9 C8.44771525,9 8,9.44771525 8,10 C8,10.5522847 8.44771525,11 9,11 L15,11 C15.5522847,11 16,10.5522847 16,10 C16,9.44771525 15.5522847,9 15,9 L9,9 Z" fill="#000000"/>
                                </g>
                            </svg>
                        </span> 
                        <span id="spinner_submit_form" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>       
                        <span id="btn_text_submit_form">Submit<span>
                    </button>  
                ' ;
                $data['buttonDelete'] = '
                    <button type="button" class="btn btn-danger btn-sm" id="btn_delete_header" onclick="btn_delete_header()">
                        <span id="svg_delete_header" class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                <defs/>
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"/>
                                    <path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                                    <path d="M10.5857864,13 L9.17157288,11.5857864 C8.78104858,11.1952621 8.78104858,10.5620972 9.17157288,10.1715729 C9.56209717,9.78104858 10.1952621,9.78104858 10.5857864,10.1715729 L12,11.5857864 L13.4142136,10.1715729 C13.8047379,9.78104858 14.4379028,9.78104858 14.8284271,10.1715729 C15.2189514,10.5620972 15.2189514,11.1952621 14.8284271,11.5857864 L13.4142136,13 L14.8284271,14.4142136 C15.2189514,14.8047379 15.2189514,15.4379028 14.8284271,15.8284271 C14.4379028,16.2189514 13.8047379,16.2189514 13.4142136,15.8284271 L12,14.4142136 L10.5857864,15.8284271 C10.1952621,16.2189514 9.56209717,16.2189514 9.17157288,15.8284271 C8.78104858,15.4379028 8.78104858,14.8047379 9.17157288,14.4142136 L10.5857864,13 Z" fill="#000000"/>
                                </g>
                            </svg>
                        </span> 
                        <span id="spinner_delete_header" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>       
                        <span id="btn_text_delete_header">Delete<span>
                    </button>
                ' ;
            } else {
                $data['buttonSubmit'] = '
                    <button type="button" class="btn btn-dark btn-sm" id="btn_recall_form" onclick="recall_form()">
                        <span id="svg_recall_form" class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                <defs/>
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <path d="M12,8 L8,8 C5.790861,8 4,9.790861 4,12 L4,13 C4,14.6568542 5.34314575,16 7,16 L7,18 C4.23857625,18 2,15.7614237 2,13 L2,12 C2,8.6862915 4.6862915,6 8,6 L12,6 L12,4.72799742 C12,4.62015048 12.0348702,4.51519416 12.0994077,4.42878885 C12.264656,4.2075478 12.5779675,4.16215674 12.7992086,4.32740507 L15.656242,6.46136716 C15.6951359,6.49041758 15.7295917,6.52497737 15.7585249,6.56395854 C15.9231063,6.78569617 15.876772,7.09886961 15.6550344,7.263451 L12.798001,9.3840407 C12.7118152,9.44801079 12.607332,9.48254921 12.5,9.48254921 C12.2238576,9.48254921 12,9.25869158 12,8.98254921 L12,8 Z" fill="#000000"/>
                                    <path d="M12.0583175,16 L16,16 C18.209139,16 20,14.209139 20,12 L20,11 C20,9.34314575 18.6568542,8 17,8 L17,6 C19.7614237,6 22,8.23857625 22,11 L22,12 C22,15.3137085 19.3137085,18 16,18 L12.0583175,18 L12.0583175,18.9825492 C12.0583175,19.2586916 11.8344599,19.4825492 11.5583175,19.4825492 C11.4509855,19.4825492 11.3465023,19.4480108 11.2603165,19.3840407 L8.40328311,17.263451 C8.18154548,17.0988696 8.13521119,16.7856962 8.29979258,16.5639585 C8.32872576,16.5249774 8.36318164,16.4904176 8.40207551,16.4613672 L11.2591089,14.3274051 C11.48035,14.1621567 11.7936615,14.2075478 11.9589099,14.4287888 C12.0234473,14.5151942 12.0583175,14.6201505 12.0583175,14.7279974 L12.0583175,16 Z" fill="#000000" opacity="0.3"/>
                                </g>
                            </svg>
                        </span> 
                        <span id="spinner_recall_form" class="spinner-border spinner-border-sm align-middle ms-2" style="display: none;"></span>       
                        <span id="btn_text_recall_form">Recall<span>
                    </button>  
                ' ;
            } 
        echo json_encode($data);
    }

    public function submit_detail_complete(Request $request)
    {
        $InptActualClockinDate = $request->InptActualClockinDate; 
        $InptClockIn = $request->InptClockIn; 
        $InptClockOut = $request->InptClockOut;
        $InptLaborHrs = $request->InptLaborHrs;
        $InptLaborQty = ($request->InptLaborQty == '' ? 0 : $request->InptLaborQty) ;
        $InptScrapQty = ($request->InptScrapQty == '' ? 0 : $request->InptScrapQty) ;
        $InptDiscrepQty = ($request->InptDiscrepQty == '' ? 0 : $request->InptDiscrepQty) ; 
        $ResourceGrpDescription = $request->InptResourceGrpDescription;
        $InptResourceGrpID = $request->InptResourceGrpID;
        $InptResourceID = $request->InptResourceID;  
        $isCopart = $request->isCopart ;
        $PartNum = $request->InptPartNum ;
        $InptDiscrpRsnCode = $request->InptDiscrpRsnCode ;
        $InptScrapReasonCode = $request->InptScrapReasonCode ; 
        $IndirectCode = $request->InptIndirectCode ; 
        $laborTypePseudo = $request->InptlaborTypePseudo ; 
        $InptLaborNote = str_replace(",", "__", $request->InptLaborNote) ; 

        $laborHedSeq = 0 ;
        $laborDtlSeq = 0 ;
        if (!empty($request->laborHedSeq)) { $laborHedSeq = Crypt::decryptString(str_replace("-", "=", $request->laborHedSeq)); }
        if (!empty($request->laborDtlSeq)) { $laborDtlSeq = Crypt::decryptString(str_replace("-", "=", $request->laborDtlSeq)); }   
        
        
        
        if ($laborTypePseudo == 'P') { 
            if ($isCopart == 1) {  
                $data = self::submit_copart($InptActualClockinDate, $laborHedSeq, $laborDtlSeq, $InptClockIn, $InptClockOut, $InptLaborHrs, $InptLaborQty, $InptScrapQty, $InptDiscrepQty, $PartNum, $InptResourceGrpID, $InptResourceID, $IndirectCode, $ResourceGrpDescription, $InptDiscrpRsnCode, $InptScrapReasonCode, $InptLaborNote) ;
            } else {
            $data = self::submit_un_copart($InptActualClockinDate, $laborHedSeq, $laborDtlSeq, $InptClockIn, $InptClockOut, $InptLaborHrs, $InptLaborQty, $InptScrapQty, $InptDiscrepQty, $InptResourceGrpID, $InptResourceID, $IndirectCode, $ResourceGrpDescription, $InptDiscrpRsnCode, $InptScrapReasonCode, $InptLaborNote) ;
            }  
        } else if ($laborTypePseudo == 'I') {
            $data = self::submit_un_copart($InptActualClockinDate, $laborHedSeq, $laborDtlSeq, $InptClockIn, $InptClockOut, $InptLaborHrs, 0, 0, 0, $InptResourceGrpID, $InptResourceID, $IndirectCode, $ResourceGrpDescription, null, null, $InptLaborNote) ;
        } 
        echo json_encode($data);
    }

    public function submit_un_copart($InptActualClockinDate, $laborHedSeq, $laborDtlSeq, $InptClockIn, $InptClockOut, $InptLaborHrs, $InptLaborQty, $InptScrapQty, $InptDiscrepQty, $InptResourceGrpID, $InptResourceID, $IndirectCode, $ResourceGrpDescription, $InptDiscrpRsnCode, $InptScrapReasonCode, $InptLaborNote) { 
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $data = []; 
        $host_api = self::get_host_api(); 
        $clockInDate = $InptActualClockinDate.'T'.$InptClockIn.':00.000Z' ; 
        $InptDiscrpRsnCode = ($InptDiscrpRsnCode === 'null' ? "" : $InptDiscrpRsnCode) ;
        $InptScrapReasonCode = ($InptScrapReasonCode === 'null' ? "" : $InptScrapReasonCode) ;  
        $InptLaborNote = (($InptLaborNote === 'null' || $InptLaborNote === '' || $InptLaborNote == null) ? "" : str_replace("__", ",", $InptLaborNote)) ;  
        // dd($InptActualClockinDate, $laborHedSeq, $laborDtlSeq, $InptClockIn, $InptClockOut, $InptLaborHrs, $InptLaborQty, $InptScrapQty, $InptDiscrepQty, $InptResourceGrpID, $InptResourceID, $IndirectCode, $ResourceGrpDescription, $InptDiscrpRsnCode, $InptScrapReasonCode, $InptLaborNote);
        try {
            $response = $client->request('POST', $host_api . 'Labor/UpdateDtl', [
                'json' => [
                    'laborHedSeq' => $laborHedSeq,
                    'laborDtlSeq' => $laborDtlSeq,
                    'date' => $clockInDate,
                    'clockInDate' => $clockInDate,
                    'clockinTime' => $InptClockIn,
                    'clockOutTime' => $InptClockOut,
                    'laborHrs' => $InptLaborHrs,
                    'burdenHrs' => $InptLaborHrs,
                    'laborQty' => $InptLaborQty,
                    'scrapQty' => $InptScrapQty,
                    'discrepQty' => $InptDiscrepQty,
                    'discrpRsnCode' => ($InptDiscrpRsnCode == null ? "" : $InptDiscrpRsnCode), 
                    'ScrapReasonCode' => ($InptScrapReasonCode == null ? "" : $InptScrapReasonCode), 
                    "resourceGrpID" => $InptResourceGrpID,
                    "resourceID" => $InptResourceID, 
                    "resourceGrpDescription" => "",
                    "indirectCode" => ($IndirectCode == 'null' ? "" : $IndirectCode),
                    "laborNote" => (($InptLaborNote === 'null' || $InptLaborNote === '' || $InptLaborNote == null) ? "" : $InptLaborNote),
                    'rowMod' => "U", 
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

    public function submit_copart($InptActualClockinDate, $laborHedSeq, $laborDtlSeq, $InptClockIn, $InptClockOut, $InptLaborHrs, $InptLaborQty, $InptScrapQty, $InptDiscrepQty, $PartNum, $InptResourceGrpID, $InptResourceID, $IndirectCode, $ResourceGrpDescription, $InptDiscrpRsnCode, $InptScrapReasonCode, $InptLaborNote) { 
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $InptDiscrpRsnCode = ($InptDiscrpRsnCode === 'null' ? "" : $InptDiscrpRsnCode) ;
        $InptScrapReasonCode = ($InptScrapReasonCode === 'null' ? "" : $InptScrapReasonCode) ;
        $client = new Client();
        $data = []; 
        $host_api = self::get_host_api();  
        $submit_detail = self::submit_un_copart($InptActualClockinDate, $laborHedSeq, $laborDtlSeq, $InptClockIn, $InptClockOut, $InptLaborHrs, 0, 0, 0, $InptResourceGrpID, $InptResourceID, $IndirectCode, $ResourceGrpDescription, $InptDiscrpRsnCode, $InptScrapReasonCode, $InptLaborNote) ;
        if ($submit_detail['code'] == 200) {  
            try {
                $response = $client->request('PATCH', $host_api . 'Labor/UpdateCoPart', [
                    'json' => [
                        'laborHedSeq' => $laborHedSeq,
                        'laborDtlSeq' => $laborDtlSeq,
                        'partNum' => $PartNum, 
                        'LaborQty' => $InptLaborQty,
                        'discrepQty' => $InptDiscrepQty,
                        'scrapQty' => $InptScrapQty,
                        'discrpRsnCode' => $InptDiscrpRsnCode, 
                        'ScrapReasonCode' => $InptScrapReasonCode, 
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
        } else {
            $data['code'] = 500;
            $data['status'] = "Reload and try again!"; 
        } 
        return $data;
    }

    public function delete_detail($laborHedSeq, $laborDtlSeq)
    { 
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

    public function submit_detail(Request $request)
    {
        $laborHedSeq = 0 ;
        $laborDtlSeq = 0 ;
        $date = $request->InptActualClockinDate ; 
        $jobNum = 0 ;
        $opSeq = 0 ;   
        $laborTypePseudo = $request->InptlaborTypePseudo ; 
        if (!empty($request->laborHedSeq)) { $laborHedSeq = Crypt::decryptString(str_replace("-", "=", $request->laborHedSeq)); }
        if (!empty($request->laborDtlSeq)) { $laborDtlSeq = Crypt::decryptString(str_replace("-", "=", $request->laborDtlSeq)); } 
        if (strpos($request->InptJobNum, '~') !== false) {
            $str = explode("~",$request->InptJobNum) ;
            $jobNum = str_replace("_SPACE_", " ", $str[0]) ;
            $opSeq = $str[1] ;  
        } 

        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $data = []; 
        $host_api = $this->get_host_api(); 
        
        if ($request->InptlaborTypePseudo == 'I') {
            $data = $this->submit_detail_indirect($laborHedSeq, $laborDtlSeq, $request->InptJobNum, $request->InptResourceGrpID, $request->InptResourceID, $request->InptIndirectCode, $request->InptlaborTypePseudo, $date);
        } else { 
            $clear_detail = $this->delete_detail($laborHedSeq, $laborDtlSeq);   
            if ($clear_detail) {
                $response = $client->request('POST', $host_api . 'Labor/GeNewtLaborDtl', [
                    'json' => [
                        'laborTypePseudo' => $laborTypePseudo,
                        'laborHedSeq' => $laborHedSeq,
                        'jobNum' => "$jobNum",
                        'opSeq' => $opSeq, 
                        'date' => "$date",
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
                $data['laborDtlSeq'] = Crypt::encryptString(str_replace("=", "-", $responseBody['data']['laborDtlSeq']));   
                $data['laborDtlSeqID'] = $responseBody['data']['laborDtlSeq'];   
                $data['isCoPart'] = ($responseBody['data']['isCopart'] == true ? 1 : 0) ; 
            } else {
                $data['code'] = 500;
                $data['status'] = "Gagal Simpan";
                $data['laborDtlSeq'] = ''; 
                $data['isCoPart'] = false ;  
            }  
        } 
        return $data;
    }

    public function submit_detail_indirect($laborHedSeq, $laborDtlSeq, $InptJobNum, $InptResourceGrpID, $InptResourceID, $InptIndirectCode, $InptlaborTypePseudo, $date)
    { 
        $jobNum = 0 ;
        $opSeq = 0 ;   
        if (strpos($InptJobNum, '~') !== false) {
            $str = explode("~",$InptJobNum) ;
            $jobNum = str_replace("_SPACE_", " ", $str[0]) ;
            $opSeq = $str[1] ;  
        } 
        $clear_detail = self::delete_detail($laborHedSeq, $laborDtlSeq); 
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $data = []; 
        $host_api = self::get_host_api(); 
        try {
            $response = $client->request('POST', $host_api . 'Labor/GeNewtLaborDtl', [
                'json' => [
                    'laborTypePseudo' => $InptlaborTypePseudo,
                    'laborHedSeq' => $laborHedSeq,
                    'jobNum' => "$jobNum",
                    'opSeq' => $opSeq,
                    'resourceGrpID' => $InptResourceGrpID,
                    'resourceID' => $InptResourceID,
                    'indirectCode' => $InptIndirectCode,
                    'date' => $date,
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
            $data['laborDtlSeq'] = Crypt::encryptString(str_replace("=", "-", $responseBody['data']['laborDtlSeq']));  
            $data['laborDtlSeqID'] = $responseBody['data']['laborDtlSeq'] ;  
            $data['isCoPart'] = ($responseBody['data']['isCopart'] == true ? 1 : 0) ;  
           
        } catch (RequestException $e) {
            // Log error details
            \Log::error('API request failed', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);

            $data['code'] = 500;
            $data['status'] = $e->getMessage();
            $data['laborDtlSeq'] = ''; 
            $data['isCoPart'] = false ; 
        } 
        return $data;
    }

    public function change_time(Request $request)
    { 
        $laborHedSeq = 0 ;
        $laborDtlSeq = 0 ;
        $InptShiftID = $request->InptShiftID ;
        $shiftDescription = $request->shiftDescription ;
        $clockinTime = $request->InptClockIn ;
        $clockOutTime = $request->InptClockOut ;
        if (!empty($request->laborHedSeq)) { $laborHedSeq = Crypt::decryptString(str_replace("-", "=", $request->laborHedSeq)); }
        if (!empty($request->laborDtlSeq)) { $laborDtlSeq = Crypt::decryptString(str_replace("-", "=", $request->laborDtlSeq)); } 
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $data = [];
        $host_api = self::get_host_api(); 
        // dd($laborHedSeq, $laborDtlSeq, $InptShiftID);
        try {
            $response = $client->request('POST', $host_api . 'Labor/ChangeLaborTime', [
                'json' => [
                    'laborHedSeq' => $laborHedSeq,
                    'laborDtlSeq' => $laborDtlSeq,  
                    'shift' => $InptShiftID,   
                    'clockinTime' => "$clockinTime",  
                    'clockOutTime' => "$clockOutTime",   
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
            $data['laborHrs'] = $responseBody['data']['laborHrs'];
        } catch (RequestException $e) {
            \Log::error('API request failed', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);

            $data['code'] = 500;
            $data['status'] = $e->getMessage();
            $data['laborHrs'] = '';
        }
        return $data;
    }

    public function set_shift($laborHedSeq, $InptShiftID)
    {
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $get_shift_attr = self::change_shift($laborHedSeq, $InptShiftID);
         
        if ($get_shift_attr['code'] == 200) {

            $employeeNum = $get_shift_attr['employeeNum'];
            $workDate = $get_shift_attr['workDate'];
            $payHour = $get_shift_attr['payHour'];
            $actualClockinDate = $get_shift_attr['actualClockinDate'];
            $clockInDate = $get_shift_attr['clockInDate'];

            $actualClockInTime = $get_shift_attr['actualClockInTime'];
            $hours = floor($actualClockInTime);
            $minutes = ($actualClockInTime - $hours) * 60;
            $minutes = str_pad(floor($minutes), 2, '0', STR_PAD_LEFT);
            $actualClockInTime = ($hours < 10 ? '0'.$hours : $hours) . ":" . $minutes;


            $clockInTime = $get_shift_attr['clockInTime'];
            $hours = floor($clockInTime);
            $minutes = ($clockInTime - $hours) * 60;
            $minutes = str_pad(floor($minutes), 2, '0', STR_PAD_LEFT);
            $clockInTime = ($hours < 10 ? '0'.$hours : $hours) . ":" . $minutes;

            $actualClockOutTime = $get_shift_attr['actualClockOutTime'];
            $hours = floor($actualClockOutTime);
            $minutes = ($actualClockOutTime - $hours) * 60;
            $minutes = str_pad(floor($minutes), 2, '0', STR_PAD_LEFT);
            $actualClockOutTime = $hours . ":" . $minutes;

            $clockOutTime = $get_shift_attr['clockOutTime'];
            $hours = floor($clockOutTime);
            $minutes = ($clockOutTime - $hours) * 60;
            $minutes = str_pad(floor($minutes), 2, '0', STR_PAD_LEFT);
            $clockOutTime = ($hours < 10 ? '0'.$hours : $hours) . ":" . $minutes;

            $actLunchOutTime = $get_shift_attr['actLunchOutTime'];
            $hours = floor($actLunchOutTime);
            $minutes = ($actLunchOutTime - $hours) * 60;
            $minutes = str_pad(floor($minutes), 2, '0', STR_PAD_LEFT);
            $actLunchOutTime = $hours . ":" . $minutes;

            $lunchOutTime = $get_shift_attr['lunchOutTime'];
            $hours = floor($lunchOutTime);
            $minutes = ($lunchOutTime - $hours) * 60;
            $minutes = str_pad(floor($minutes), 2, '0', STR_PAD_LEFT);
            $lunchOutTime = $hours . ":" . $minutes;

            $actLunchInTime = $get_shift_attr['actLunchInTime'];
            $hours = floor($actLunchInTime);
            $minutes = ($actLunchInTime - $hours) * 60;
            $minutes = str_pad(floor($minutes), 2, '0', STR_PAD_LEFT);
            $actLunchInTime = $hours . ":" . $minutes;

            $lunchInTime = $get_shift_attr['lunchInTime'];
            $hours = floor($lunchInTime);
            $minutes = ($lunchInTime - $hours) * 60;
            $minutes = str_pad(floor($minutes), 2, '0', STR_PAD_LEFT);
            $lunchInTime = $hours . ":" . $minutes;

            $client = new Client();
            $data = [];
            $host_api = self::get_host_api();
            try {
                $response = $client->request('POST', $host_api . 'Labor/UpdateHeader', [
                    'json' => [
                        'nik' => "$username",
                        'password' => "$password",
                        'employeeNum' => "$employeeNum",
                        'workDate' => "$workDate",
                        'laborHedSeq' => $laborHedSeq,
                        'shift' => $InptShiftID,
                        'payHours' => $payHour,
                        'actualClockinDate' => "$actualClockinDate",
                        'clockInDate' => "$clockInDate",
                        'actualClockInTime' => $actualClockInTime,
                        'clockInTime' => $clockInTime,
                        'actualClockOutTime' => $actualClockOutTime,
                        'clockOutTime' => $clockOutTime,
                        'actLunchOutTime' => $actLunchOutTime,
                        'lunchOutTime' => $lunchOutTime,
                        'actLunchInTime' => $actLunchInTime,
                        'lunchInTime' => $lunchInTime
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'verify' => false,
                ]);
                $responseBody = json_decode($response->getBody()->getContents(), true);
                $data['code'] = $responseBody['code'];
                $data['status'] = $responseBody['status'];
                $data['payHour'] = $payHour;
                $data['clockInTime'] = $clockInTime;
                $data['clockOutTime'] = $clockOutTime;
            } catch (RequestException $e) {
                // Log error details
                \Log::error('API request failed', [
                    'message' => $e->getMessage(),
                    'request' => $e->getRequest(),
                    'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
                ]);

                $data['code'] = 500;
                $data['status'] = $e->getMessage();
                $data['payHour'] = '';
                $data['clockInTime'] = '';
                $data['clockOutTime'] = '';
            }
        }
        return $data;
    }

    public function change_shift($laborHedSeq, $InptShiftID)
    {
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $data = [];
        $host_api = self::get_host_api();

        try {
            $response = $client->request('POST', $host_api . 'Labor/ChangeShift', [
                'json' => ['nik' => "$username", 'password' => "$password", 'laborHedSeq' => $laborHedSeq, 'shift' => $InptShiftID],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
            ]);
            $responseBody = json_decode($response->getBody()->getContents(), true);
            $data['code'] = $responseBody['code'];
            $data['status'] = $responseBody['status'];
            $data['employeeNum'] = $responseBody['data']['employeeNum'];
            $data['workDate'] = $responseBody['data']['workDate'];
            $data['payHour'] = $responseBody['data']['payHour'];
            $data['actualClockinDate'] = $responseBody['data']['actualClockinDate'];
            $data['clockInDate'] = $responseBody['data']['clockInDate'];
            $data['actualClockInTime'] = $responseBody['data']['actualClockInTime'];
            $data['clockInTime'] = $responseBody['data']['clockInTime'];
            $data['actualClockOutTime'] = $responseBody['data']['actualClockOutTime'];
            $data['clockOutTime'] = $responseBody['data']['clockOutTime'];
            $data['actLunchOutTime'] = $responseBody['data']['actLunchOutTime'];
            $data['lunchOutTime'] = $responseBody['data']['lunchOutTime'];
            $data['actLunchInTime'] = $responseBody['data']['actLunchInTime'];
            $data['lunchInTime'] = $responseBody['data']['lunchInTime'];
        } catch (RequestException $e) {
            // Log error details
            \Log::error('API request failed', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);

            $data['code'] = 500;
            $data['status'] = $e->getMessage();
            $data['employeeNum'] = '';
            $data['workDate'] = '';
            $data['payHour'] = '';
            $data['actualClockinDate'] = '';
            $data['clockInDate'] = '';
            $data['actualClockInTime'] = '';
            $data['clockInTime'] = '';
            $data['actualClockOutTime'] = '';
            $data['clockOutTime'] = '';
            $data['actLunchOutTime'] = '';
            $data['lunchOutTime'] = '';
            $data['actLunchInTime'] = '';
            $data['lunchInTime'] = '';
        }

        return $data;
    }

    public function get_new_header($InptActualClockinDate, $InptEmployeeID)
    {
        $username = Auth::user()->username;
        $password = Crypt::decryptString(Auth::user()->epicor_password);
        $client = new Client();
        $data = [];
        $host_api = self::get_host_api();
        try {
            $response = $client->request('POST', $host_api . 'Labor/CreateNew', [
                'json' => [
                    'employeeNum' => "$InptEmployeeID",
                    'startDate' => "$InptActualClockinDate",
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
            $data['laborHedSeq'] = $responseBody['data']['laborHedSeq'];
        } catch (RequestException $e) {
            \Log::error('API request failed', [
                'message' => $e->getMessage(),
                'request' => $e->getRequest(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);

            $data['code'] = 500;
            $data['status'] = $e->getMessage();
            $data['laborHedSeq'] = '';
        }
        return $data;
    }

    public function get_resource(Request $request)
    {
        $search = $request->search;
        $line = $request->line;
        $page = $request->post('page', 1);
        $pageSize = 10;

        $query = TimeEntry::get_resource($line);
        if ($search) {
            $query->where('a.ResourceGrpID', 'like', '%' . $search . '%');
        }
        $resource = $query->paginate($pageSize, ['*'], 'page', $page);

        // Prepare JSON response
        return response()->json([
            'items' => $resource->map(function ($resource) {
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

    public function add_document(Request $request)
    {
        $data['test'] = '';
        return view('time_entry/form', $data);
    }

    public function get_resource_group(Request $request)
    {
        $search = $request->search;
        $category_id = $request->category_id; // Get category_id from request
        $page = $request->post('page', 1); // default to page 1
        $pageSize = 10; // Number of items per page

        $query = TimeEntry::get_resource_group($category_id);
        if ($search) {
            $query->where('a.ResourceGrpID', 'like', '%' . $search . '%');
        }
        $resourceGroups = $query->paginate($pageSize, ['*'], 'page', $page);

        // Prepare JSON response
        return response()->json([
            'items' => $resourceGroups->map(function ($resourceGroup) {
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

    public function get_job_list(Request $request)
    {
        $search = $request->search;
        $page = $request->post('page', 1);
        $pageSize = 10;
        $JobDate = $request->JobDate;

        $query = TimeEntry::get_job_list($JobDate);
        if ($search) {
            $query->where('jo_num', 'like', '%' . $search . '%'); 
        } 
        $resourceGroups = $query->paginate($pageSize, ['*'], 'page', $page);

        return response()->json([
            'items' => $resourceGroups->map(function ($resourceGroup) {
                return [
                    'id' => $resourceGroup->jo_num . '~' . $resourceGroup->process_detail_id . '~' . $resourceGroup->home_line . '~' . $resourceGroup->home_line_detail_id . '~' . 
                    ((strpos($resourceGroup->dept, 'ASSY') !== false) ? 'ASSY' : (
                        (strpos($resourceGroup->dept, 'STP') !== false) ? 'STP' : (
                            (strpos($resourceGroup->dept, 'PPIC') !== false) ? 'PPIC' : ''
                        )
                    )
                ). '~' . $resourceGroup->part_num,
                    'name' => $resourceGroup->jo_num .' - OP' .$resourceGroup->process_detail_id
                ];
            }),
            'pagination' => [
                'more' => $resourceGroups->hasMorePages(),
            ]
        ]);
    }

    public function get_employee_list(Request $request)
    {
        $search = $request->search;
        $page = $request->post('page', 1);
        $pageSize = 10;
        $query = TimeEntry::get_employee_list();
        if ($search) {
            $query->where('a.EmpID', 'like', '%' . $search . '%');
            $query->orWhere('a.Name', 'like', '%' . $search . '%');
        }
        $employeeDB = $query->paginate($pageSize, ['*'], 'page', $page);
        return response()->json([
            'items' => $employeeDB->map(function ($employeeDB) {
                return [
                    'id' => $employeeDB->EmpID,
                    'name' => $employeeDB->EmpID . '-' . $employeeDB->Name
                ];
            }),
            'pagination' => [
                'more' => $employeeDB->hasMorePages(), // Tells Select2 if more pages are available
            ]
        ]);
    }

    public function get_shift_list(Request $request)
    {
        $search = $request->search;
        $page = $request->post('page', 1);
        $pageSize = 10;
        $query = TimeEntry::get_shift_list();
        if ($search) {
            $query->orWhere('a.Description', 'like', '%' . $search . '%');
        }
        $shiftDB = $query->paginate($pageSize, ['*'], 'page', $page);
        return response()->json([
            'items' => $shiftDB->map(function ($shiftDB) {
                return [
                    'id' => $shiftDB->Shift,
                    'name' => $shiftDB->Description
                ];
            }),
            'pagination' => [
                'more' => $shiftDB->hasMorePages(), // Tells Select2 if more pages are available
            ]
        ]);
    }

    public function get_reason_code_scrap_list(Request $request)
    {
        $search = $request->search;
        $page = $request->post('page', 1);
        if ($request->qty > 0) {
            $pageSize = 10;
        } else {
            $pageSize = 0;
        } 
        $query = TimeEntry::get_reason_code_scrap_list();
        if ($search) {
            $query->orWhere('a.Description', 'like', '%' . $search . '%');
        }
        $shiftDB = $query->paginate($pageSize, ['*'], 'page', $page);
        return response()->json([
            'items' => $shiftDB->map(function ($shiftDB) {
                return [
                    'id' => $shiftDB->ReasonCode,
                    'name' => $shiftDB->Description
                ];
            }),
            'pagination' => [
                'more' => $shiftDB->hasMorePages(), // Tells Select2 if more pages are available
            ]
        ]);
    }

    public function get_indirect_code_list(Request $request)
    {
        $search = $request->search;
        $page = $request->post('page', 1);
        if ($request->InptlaborTypePseudo == 'I') {
            $pageSize = 10;
        } else {
            $pageSize = 0;
        } 
        $query = TimeEntry::get_indirect_code_list();
        if ($search) {
            $query->orWhere('a.Description', 'like', '%' . $search . '%');
        }
        $shiftDB = $query->paginate($pageSize, ['*'], 'page', $page);
        return response()->json([
            'items' => $shiftDB->map(function ($shiftDB) {
                return [
                    'id' => $shiftDB->IndirectCode,
                    'name' => $shiftDB->Description
                ];
            }),
            'pagination' => [
                'more' => $shiftDB->hasMorePages(), // Tells Select2 if more pages are available
            ]
        ]);
    }

    public function get_part_num_list(Request $request)
    {
        $search = $request->search;
        $page = $request->post('page', 1);
        $strJobNum = explode("~", $request->InptJobNum) ; 
        $jobNum = str_replace("_SPACE_", " ", $strJobNum[0]) ;   
        $opSeq = $strJobNum[1] ; 
        $laborHedSeq = 0 ;
        $laborDtlSeq = 0 ;
        if (!empty($request->laborHedSeq)) { $laborHedSeq = Crypt::decryptString(str_replace("-", "=", $request->laborHedSeq)); }
        if (!empty($request->laborDtlSeq)) { $laborDtlSeq = Crypt::decryptString(str_replace("-", "=", $request->laborDtlSeq)); }   
        if ($request->isCopart ==  0) {
            $pageSize = 1;
        } else {
            $pageSize = 10;
        }  
        // dd($jobNum, $opSeq, $laborHedSeq, $laborDtlSeq);
        $query = TimeEntry::part_num_list($jobNum); 
        if ($search) {
            $query->orWhere('PartNum', 'like', '%' . $search . '%');
        }
        $coPartDB = $query->paginate($pageSize, ['*'], 'page', $page);
        return response()->json([
            'items' => $coPartDB->map(function ($coPartDB) {
                return [
                    'id' => $coPartDB->PartNum,
                    'name' => $coPartDB->PartNum
                ];
            }),
            'pagination' => [
                'more' => $coPartDB->hasMorePages(), // Tells Select2 if more pages are available
            ]
        ]);
    }

    public function get_partnum_attr(Request $request)
    {
        $InptPartNum = $request->InptPartNum; 
        $strJobNum = explode("~", $request->InptJobNum) ;  
        $jobNum = str_replace("_SPACE_", " ", $strJobNum[0]) ;   
        $opSeq = $strJobNum[1] ; 
        $laborHedSeq = 0 ;
        $laborDtlSeq = 0 ;
        if (!empty($request->laborHedSeq)) { $laborHedSeq = Crypt::decryptString(str_replace("-", "=", $request->laborHedSeq)); }
        if (!empty($request->laborDtlSeq)) { $laborDtlSeq = Crypt::decryptString(str_replace("-", "=", $request->laborDtlSeq)); } 
        $query = TimeEntry::get_part_num_list($jobNum, $opSeq, $laborHedSeq, $laborDtlSeq, $InptPartNum) ; 
        // dd($query->get());
        if ($query->count() > 0) {
            foreach ($query->get() AS $row) {
                $data['code'] = 200 ;
                $data['home_line'] = $row->home_line ;
                $data['home_line_detail_id'] = $row->home_line_detail_id ;
                $data['item_name'] = $row->item_name ;
                $data['LaborNote'] = $row->LaborNote ;
                $data['qty_plan'] = (int) $row->qty_plan ;
                $data['model_name'] = $row->model_name ;
                $data['QtyCompleted'] = (int) ($request->isCopart == 1 ? $row->QtyCompleted : $row->QtyCompleted) ;
                $data['DiscrepQty'] =  (int) ($request->isCopart == 1 ? $row->CoPartDiscrepQty : $row->DiscrepQty) ;
                $data['ScrapQty'] =  (int) ($request->isCopart == 1 ? $row->CoPartScrapQty : $row->ScrapQty) ;
                $data['DiscrpRsnCode'] =  ($request->isCopart == 1 ? $row->CoPartDiscrpRsnCode : $row->DiscrpRsnCode) ;
                $data['ScrapReasonCode'] =  ($request->isCopart == 1 ? $row->CoPartScrapReasonCode : $row->ScrapReasonCode) ;
                $data['DiscrpRsnCodeDesc'] = ($data['DiscrpRsnCode'] == '' ? '' :TimeEntry::get_descr_reason_code($data['DiscrpRsnCode']));
                $data['ScrapReasonCodeDesc'] = ($data['ScrapReasonCode'] == '' ? '' :TimeEntry::get_descr_reason_code($data['ScrapReasonCode']));
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

    public function get_jobnum_attr(Request $request)
        { 
            $strJobNum = explode("~", $request->InptJobNum) ; 
            $jobNum = str_replace("_SPACE_", " ", $strJobNum[0]) ; 
            $opSeq = $strJobNum[1] ;  
            // dd($jobNum, $opSeq);
            $query = TimeEntry::get_job_properties($jobNum, $opSeq); 
            if ($query->count() > 0) {
                foreach ($query->get() AS $row) {
                    $data['code'] = 200 ;
                    $data['ResourceGrpID'] = $row->ResourceGrpID ;
                    $data['ResourceGrpDesc'] = $row->ResourceGrpDesc ;
                    $data['ResourceID'] = $row->ResourceID ;
                    $data['ResourceDesc'] = $row->ResourceDesc ;
                    $data['PartNum'] = $row->PartNum ; 
                }
            } else { 
                $data['ResourceGrpID'] = '' ;
                $data['ResourceGrpDesc'] = '' ;
                $data['ResourceID'] = '' ;
                $data['ResourceDesc'] = '' ;
                $data['PartNum'] = '' ;
                $data['code'] = 500 ;
                $data['status'] = "Data Part tidak ditemukan!" ;
            }
            echo json_encode($data);
        }

    public function front_table(Request $request)
    {
        $JoDate = $request->JoDate;
        $ResourceGroupID =  $request->ResourceGroupID;
        $ResourceID =  $request->ResourceID;
        $ShiftID =  $request->ShiftID;
        $EmployeeID =  $request->EmployeeID;
        $search =  $request->front_table_search;
        $status_id =  $request->status_id ;
        $columns = array(
            0 => 'JobNum',
            1 => 'JobNum',
            2 => 'JobNum',
            3 => 'PartNum',
            4 => 'ResourceID',
            5 => 'OprSeq',
            6 => 'PlanQty',
            7 => 'LaborQty',
            8 => 'ReceivedQty'
        );
        // dd($JoDate, $ResourceGroupID, $ResourceID, $ShiftID, $EmployeeID);
        $totalData = TimeEntry::get_detail_list($JoDate, $ResourceGroupID, $ResourceID, $ShiftID, $EmployeeID);
        $totalData = $totalData->get()->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = ($request->input('order.0.column') == 0 ? $columns[1] : $columns[$request->input('order.0.column')]);
        $dir = ($request->input('order.0.column') == 0 ? 'desc' : $request->input('order.0.dir'));
        if (empty($search)) { 
            $posts = TimeEntry::get_detail_list($JoDate, $ResourceGroupID, $ResourceID, $ShiftID, $EmployeeID)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
                // dd($posts);
        } else { 
            $posts = TimeEntry::get_detail_list($JoDate, $ResourceGroupID, $ResourceID, $ShiftID, $EmployeeID)
                ->where(function ($query) use ($search) {
                    $query->where('PartNum', 'LIKE', "%$search%");
                    $query->orWhere('JobNum', 'LIKE', "%$search%");
                    $query->orWhere('EmployeeName', 'LIKE', "%$search%");
                })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = TimeEntry::get_detail_list($JoDate, $ResourceGroupID, $ResourceID, $ShiftID, $EmployeeID)
                ->where(function ($query) use ($search) {
                    $query->where('PartNum', 'LIKE', "%$search%");
                    $query->orWhere('JobNum', 'LIKE', "%$search%");
                    $query->orWhere('EmployeeName', 'LIKE', "%$search%");
                })->get()->count();
        }
        $data = array();
        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                // if (($post->ProcessMode == 'C' ? $post->CoLaborQty : $post->LaborQty) > 0) {
                    $no++;
                    $LaborHedSeq = "'".str_replace("=", "-", Crypt::encryptString($post->LaborHedSeq))."'";
                    $LaborDtlSeq = "'".str_replace("=", "-", Crypt::encryptString($post->LaborDtlSeq))."'";
                    $PartNum = "'".$post->PartNum."'" ;  
                    $ClockInDate = "'".$post->ClockInDate."'" ;  
                    $ShiftID = "'".$post->ShiftID."'" ;  
                    $Shift = "'".$post->Shift."'" ;  
                    $EmployeeNum = "'".$post->EmployeeNum."'" ;  
                    $EmployeeName = "'".$post->EmployeeName."'" ;  
                    $JobNum = "'".$post->JobNum.'~'.$post->OprSeq."'" ;  
                    $JobNumDesc = "'".$post->JobNum.' - OP'.$post->OprSeq."'" ;  
                    $LaborType = "'".$post->LaborType."'" ;  
                    $LaborHrs = "'".$post->LaborHrs."'" ;  
                    $PayHours = "'".$post->PayHours."'" ;  

                    $actualClockInTime = $post->ClockinTime ;
                    $hours = floor($actualClockInTime);
                    $minutes = ($actualClockInTime - $hours) * 60;
                    $minutes = str_pad(floor($minutes), 2, '0', STR_PAD_LEFT);  
                    $ClockInTime = "'".($hours < 10 ? '0'.$hours : $hours) . ":" . $minutes ."'" ; 

                    $actualClockOutTime = $post->ClockOutTime ;
                    $hours = floor($actualClockOutTime);
                    $minutes = ($actualClockOutTime - $hours) * 60;
                    $minutes = str_pad(floor($minutes), 2, '0', STR_PAD_LEFT);  
                    $ClockOutTime = "'".($hours < 10 ? '0'.$hours : $hours) . ":" . $minutes ."'" ; 
                    $isCoPart = "'".($post->ProcessMode == 'C' ? 1 : 0)."'" ; 
                    $IndirectCode =  "'".($post->IndirectCode == '' ? null : $post->IndirectCode)."'" ; 
                    $IndirectCodeDesc =  "'".($post->IndirectCode == 'Select Option' ? null : $post->IndirectDescription)."'" ; 

                    $LaborTypeDesc = "'".($post->LaborType == 'P' ? 'Production' : 'Indirect')."'" ;  
                    if ($post->TimeStatus == 'A') {
                        $status = '(Confirm)';
                    } else {
                        $status = '(Draft)';
                    }
                    $button = '<button type="button" title="Generate Label" class="btn btn-light-primary btn-sm" id="btn_form_view_doc_' . $no . '" onclick="open_document(' . $LaborHedSeq . ',' . $LaborDtlSeq . ',' . $PartNum . ',' . $ShiftID . ',' . $Shift. ',' . $ClockInDate . ',' . $EmployeeNum . ',' . $EmployeeName . ',' . $JobNum . ',' . $JobNumDesc . ',' . $LaborType . ',' . $LaborTypeDesc . ',' . $LaborHrs . ',' . $ClockInTime . ',' . $ClockOutTime . ',' . $isCoPart . ',' . $IndirectCode . ',' . $IndirectCodeDesc . ',' . $PayHours . ',' . $no . ')" style="text-align: center; width: 40px; height: 35px;">
                                <span id="svg_form_view_doc_' . $no . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black"></path>
                                    <path opacity="0.3" d="M10.3 15.3L11 14.6L8.70002 12.3C8.30002 11.9 7.7 11.9 7.3 12.3C6.9 12.7 6.9 13.3 7.3 13.7L10.3 16.7C9.9 16.3 9.9 15.7 10.3 15.3Z" fill="black"></path><path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM11.7 16.7L16.7 11.7C17.1 11.3 17.1 10.7 16.7 10.3C16.3 9.89999 15.7 9.89999 15.3 10.3L11 14.6L8.70001 12.3C8.30001 11.9 7.69999 11.9 7.29999 12.3C6.89999 12.7 6.89999 13.3 7.29999 13.7L10.3 16.7C10.5 16.9 10.8 17 11 17C11.2 17 11.5 16.9 11.7 16.7Z" fill="black"></path>
                                </svg>
                                </span>
                                <span id="spinner_form_view_doc_' . $no . '" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
                            </button>'; 

                    $nestedData['no'] = $no;
                    $nestedData['JobNum'] =  $post->JobNum . ' <br> ' . $post->EmployeeName . ' <br> ' . $post->Shift;
                    $nestedData['PartNum'] = $post->PartNum . ' <br> ' . $post->PartDescription . ' <br> ' . $status;
                    $nestedData['OprSeq'] = $post->OprSeq;
                    $nestedData['PlanQty'] = number_format($post->PlanQty, 0);
                    $nestedData['QtyCompleted'] = number_format(($post->ProcessMode == 'C' ? $post->CoLaborQty : $post->LaborQty), 0);
                    $nestedData['ReceivedQty'] = number_format($post->ReceivedQty, 0);
                    $nestedData['ResourceID'] = $post->ResourceID . ' <br> OP : ' . $post->OprSeq;
                    $nestedData['action'] = $button;
                    $data[] = $nestedData;
                // }
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

    public function get_count_document(Request $request)
    {
        $JoDate = $request->JoDate;
        $ResourceGroupID =  $request->ResourceGroupID;
        $ResourceID =  $request->ResourceID;
        $ShiftID =  $request->ShiftID;
        $EmployeeID =  $request->EmployeeID;

        $data['total_draft'] = TimeEntry::get_count_document_draft($JoDate, $ResourceGroupID, $ResourceID, $ShiftID, $EmployeeID);
        $data['total_document'] = TimeEntry::get_count_document($JoDate, $ResourceGroupID, $ResourceID, $ShiftID, $EmployeeID);
        echo json_encode($data);
    }

     


    public function detail_table(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        $str_id = explode("~", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
        $job_num = $str_id[0];
        $process_detail_id = $str_id[1];
        $part_num = $str_id[2];
        $columns = array(
            0 => 'a.line_search_id',
            1 => 'a.item_no',
            2 => 'a.qty_1',
            3 => 'a.created_by'
        );

        $totalData = TimeEntry::get_detail_tag_label($job_num, $process_detail_id, $part_num)->get()->count();
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[0];
        $dir = ($request->input('order.0.column') == 0 ? 'desc' : $request->input('order.0.dir'));
        if (empty($request->input('search.value'))) {
            $posts = TimeEntry::get_detail_tag_label($job_num, $process_detail_id, $part_num)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $posts =  TimeEntry::get_detail_tag_label($job_num, $process_detail_id, $part_num)->where('b.[item_no]', 'LIKE', "%$search%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = TimeEntry::get_detail_tag_label($job_num, $process_detail_id, $part_num)->where('b.item_no', 'LIKE', "%$search%")->get()->count();
        }
        $data = array();
        if (!empty($posts)) {
            $no = $start;
            foreach ($posts as $post) {
                $no++;
                $trc_id = str_replace("=", "-", Crypt::encryptString($post->job_num . '~' . $post->process_detail_id . '~' . $post->item_no . '~' . $post->line_search_id));
                $sys_id = "'" . $trc_id . "'";

                $button = '<button type="button" title="print" class="btn btn-light-primary btn-sm" id="print_tag_label_' . $no . '" onclick="print_label(' . $sys_id . ',' . $no . ')" style="text-align: center; width: 40px; height: 35px;">
                            <span id="svg_print_tag_label_' . $no . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                    <defs/>
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M16,17 L16,21 C16,21.5522847 15.5522847,22 15,22 L9,22 C8.44771525,22 8,21.5522847 8,21 L8,17 L5,17 C3.8954305,17 3,16.1045695 3,15 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,15 C21,16.1045695 20.1045695,17 19,17 L16,17 Z M17.5,11 C18.3284271,11 19,10.3284271 19,9.5 C19,8.67157288 18.3284271,8 17.5,8 C16.6715729,8 16,8.67157288 16,9.5 C16,10.3284271 16.6715729,11 17.5,11 Z M10,14 L10,20 L14,20 L14,14 L10,14 Z" fill="#000000"/>
                                        <rect fill="#000000" opacity="0.3" x="8" y="2" width="8" height="2" rx="1"/>
                                    </g>
                                </svg>
                            </span>
                            <span id="spinner_print_tag_label_' . $no . '" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
                        </button>';

                $button_delete = '<button type="button" title="Delete" class="btn btn-light-danger btn-sm" id="delete_tag_label_' . $no . '" onclick="delete_tag_label(' . $sys_id . ',' . $no . ')" style="text-align: center; width: 40px; height: 35px;">
                            <span id="svg_delete_tag_label_' . $no . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                    <defs/>
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M6,8 L18,8 L17.106535,19.6150447 C17.04642,20.3965405 16.3947578,21 15.6109533,21 L8.38904671,21 C7.60524225,21 6.95358004,20.3965405 6.89346498,19.6150447 L6,8 Z M8,10 L8.45438229,14.0894406 L15.5517885,14.0339036 L16,10 L8,10 Z" fill="#000000" fill-rule="nonzero"/>
                                        <path d="M14,4.5 L14,3.5 C14,3.22385763 13.7761424,3 13.5,3 L10.5,3 C10.2238576,3 10,3.22385763 10,3.5 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3"/>
                                    </g>
                                </svg>
                            </span>
                            <span id="spinner_delete_tag_label_' . $no . '" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
                        </button>';
                $button_save = '<button type="button" title="Delete" class="btn btn-light-success btn-sm" id="save_tag_label_' . $no . '" onclick="save_tag_label(' . $sys_id . ',' . $no . ')" style="text-align: center; width: 40px; height: 35px;">
                            <span id="svg_save_tag_label_' . $no . '" class="svg-icon svg-icon-2" style="margin-left: -7px;">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1"> 
                                    <defs/>
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24"/>
                                        <path d="M17,4 L6,4 C4.79111111,4 4,4.7 4,6 L4,18 C4,19.3 4.79111111,20 6,20 L18,20 C19.2,20 20,19.3 20,18 L20,7.20710678 C20,7.07449854 19.9473216,6.94732158 19.8535534,6.85355339 L17,4 Z M17,11 L7,11 L7,4 L17,4 L17,11 Z" fill="#000000" fill-rule="nonzero"/>
                                        <rect fill="#000000" opacity="0.3" x="12" y="4" width="3" height="5" rx="0.5"/>
                                    </g>
                                </svg>
                            </span>
                            <span id="spinner_save_tag_label_' . $no . '" class="spinner-border spinner-border-sm svg-icon svg-icon-1x" style="display: none;"></span>  
                        </button>';

                $nestedData['no'] = "
            <input class='form-control form-control-sm text-xs' readonly='' style='text-align: center; width: 40px; background: transparent;' type='text' value='" . $no . "' />
            <input type='hidden' value='" . $post->model_name . "' id='model_name" . $trc_id . "' />
            ";
                $nestedData['item_no'] =
                    "<input class='form-control form-control-sm text-xs' readonly='' style='width: 220px; background: transparent;' type='text' value='" . $post->item_no . "' />
            ";
                $nestedData['plan'] = '
            <input type="number" class="form-control form-control-sm text-xs" style="text-align: center; width: 80px; background: transparent;" id="qty_pack_' . $trc_id . '" type="text" value="' . number_format($post->qty_1, 0) . '" />
            ';

                $nestedData['created_by'] = ' 
            <input class="form-control form-control-sm text-xs" style="text-align: left; width: 100px; background: transparent;" id="qty_pack_' . $trc_id . '" type="text" value="' . $post->created_by . '" readonly/>
            ';

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

    public function generate_tag_label(Request $request)
    {
        $str_req = explode("_", $request->trc_unix_id);
        $str_id = explode("~", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
        $job_num = $str_id[0];
        $process_detail_id = $str_id[1];
        $my_name = auth()->user()->username;

        $qty_pack = $request->std_pack;
        $home_line_detail_id = $request->home_line;
        $operator_name = $request->operator_name;
        $quality_name = $request->quality_operator_name;
        $model_name = $request->model_name;
        $qty_plan = $request->qty_plan;
        $part_num = str_replace(",", "__", $request->part_num);
        $part_name = $request->part_name;
        $cust_name = $request->partner_code;
        $production_date = $request->production_date;

        $delete = DB::table('t510_production_tag')
            ->where('job_num', '=', "$job_num")
            ->where('process_detail_id', '=', $process_detail_id)
            ->where('item_no', '=', "$part_num")
            ->where('created_by', '=', $my_name);
        if ($delete->count() > 0) {
            $exe_delete =  $delete->delete();
            if ($exe_delete) {
                $generate = TimeEntry::generate_tag_label($job_num, $process_detail_id, $qty_plan, $qty_pack, $home_line_detail_id, $operator_name, $quality_name, $model_name, $part_num, $part_name, $cust_name, $production_date);
                if ($generate) {
                    $data['process_status'] = 1;
                    $data['msg_process'] = 'Data updated successfully';
                } else {
                    $data['process_status'] = 0;
                    $data['msg_process'] = 'Data updated fail !';
                }
            } else {
                $data['process_status'] = 0;
                $data['msg_process'] = 'Login sebagai admin data!';
            }
        } else {
            $generate = TimeEntry::generate_tag_label($job_num, $process_detail_id, $qty_plan, $qty_pack, $home_line_detail_id, $operator_name, $quality_name, $model_name, $part_num, $part_name, $cust_name, $production_date);
            if ($generate) {
                $data['process_status'] = 1;
                $data['msg_process'] = 'Data updated successfully';
            } else {
                $data['process_status'] = 0;
                $data['msg_process'] = 'Data updated fail !';
            }
        }
        return json_encode($data);
    }

    public function clear_tag_label(Request $request)
    {
        $my_name = auth()->user()->username;
        $str_req = explode("_", $request->trc_unix_id);
        $str_id = explode("~", Crypt::decryptString(str_replace("-", "=", $str_req[0])));
        $job_num = $str_id[0];
        $process_detail_id = $str_id[1];
        $part_num = $str_id[2];
        $delete = DB::table('t510_production_tag')
            ->where('job_num', '=', "$job_num")
            ->where('process_detail_id', '=', $process_detail_id)
            ->where('item_no', '=', "$part_num")
            ->where('created_by', '=', $my_name)
            ->delete();
        if ($delete) {
            $data['process_status'] = 1;
            $data['msg_process'] = 'Delete successfully';
        } else {
            $data['process_status'] = 0;
            $data['msg_process'] = 'Login sebagai admin data!';
        }

        return json_encode($data);
    }


    public function print_label_reguler(Request $request)
    {

        date_default_timezone_set('Asia/Jakarta');
        $print_time = date("d M Y H:i ");
        $str_req = explode("_", $request->ref_doc);
        $str_id = explode("~", Crypt::decryptString(str_replace("-", "=", $str_req[0])));

        $job_num = $str_id[0];
        $process_detail_id = $str_id[1];
        $part_num = $str_id[2];

        $dtCount = TimeEntry::get_detail_tag_label($job_num, $process_detail_id, $part_num)->get()->count();

        if (count($str_id) > 3) {
            $line_search_id = $str_id[3];
            $db_data = TimeEntry::get_detail_tag_label_id($job_num, $process_detail_id, $line_search_id, $part_num)->get();
        } else {
            $db_data = TimeEntry::get_detail_tag_label($job_num, $process_detail_id, $part_num)->get();
        }
        if ($db_data->count() > 0) {
            foreach ($db_data  as $db) {
                $barcode = $db->item_no . '~' . $db->qty_1 . '~05-10-01~GENERAL~' . $db->job_num . '~A';
                $so_date = AppModel::local_date_formate_name($db->production_date);
                $log_date = AppModel::local_date_formate_name($db->log_date);
                $created_by = $db->created_by;
                $item_no = $db->item_no;
                $item_name = $db->item_name;
                $partner_code = $db->cust_name;
                $qty_1 = $db->qty_1;
                $qc_status = 'OK';
                // if ($db->special_mark == 1) {
                // $special_mark = '<br> ++S' ;
                // } else {
                // $special_mark = '' ; 
                // }
                $special_mark = '';
                echo  "   
        <style>
    .container-label {
      display: flex;
      flex-wrap: wrap;
      font-size: 7px;
      text-align:center;
    }

    .box-label {
      width: 18px;
      height: 18px; 
      margin: 2px;
      text-align: center;
      line-height: 18px;
      border: 2px solid black; /* Menambahkan border hitam */
    }
  </style>
        <table class='tg' style='undefined;table-layout: fixed; width: 340px'> 
        <colgroup>
        <col style='width: 140px'>
        <col style='width: 70px'>
        <col style='width: 30px'>
        <col style='width: 50px'>
        <col style='width: 50px'>
        </colgroup>
        <tr> 
            <th class='tg-baqhh' colspan='5' style='font-family:'Courier New', Courier, monospace !important; font-size:14px; text-align: left; border-left: none; border-right: none; border-top: none;'>FO-44-02   Rev. 01</th> 
            </tr> 
            <tr>
            <td rowspan='2'><div align='center'><span class='style91' style='font-weight: bold; font-size: xx-large;'>" . $qc_status . "</span> 
            </td>
            <td colspan='4'>
            <div align='center' class='style9'>
                <h4 class='style10' style='margin:0; padding:0;'>PRODUCT IDENTIFICATION 
                
                </h4>
            </div>
            <div align='center' class='style9'>
            <h5 style='font-weight: bold;" . ($so_date == '' ? '' : 'align:center') . " align:center; margin:0; padding:0;'>Prod. Date : <em> <u>" . $so_date . "</u></em> </h5>
            <h5 style='font-weight: bold; align:center; margin:0; padding:0;'>
               <em></em>
            </h5>
            </div>
            </td> 
            </tr>
            <tr>
            <td colspan='4'><div align='center'><span style='font-weight: bold; '>
            Line : " . $db->home_line_detail_id . " </span></div></td>
            </tr>
            <td><div align='center'><span class='style11'><strong>" . $db->job_num . "</strong></div></td>
            <td colspan='2'>
            <span class='style9'> Pallet No.</span>
          </td> 
          <td colspan='2'>
          <span class='style9'> QC CHECK</span>
          </td>
          </tr>
            <tr>
            <td><div align='Center' class='style9'><span class='style11''>QTY : <strong>" . $qty_1 . "</strong></span> </div></td>
            <td colspan='2'>
                <div align='center'><span class='style2'> <strong> " . $db->line_search_id . '/' . $dtCount . " 
                </strong></span>
               
                </div>
            </td>
            <td colspan='2'><div align='center'></br><span class='style2'>" . $db->quality_name . "</span></br></br></div></td>
            </tr>
            
            <tr>
            <td rowspan='4'>
            <div align='center'> 
            " . QrCode::size(90)->generate($barcode) . "</div>
            </div>
        </td>
            <td colspan='4' rowspan='2' >
            <div style='margin-left:30px; text-align: center;'>
                <div class='container-label' >
                <span class='box-label'>PD</span>
                <span class='box-label'>QC</span>
                <span class='box-label'>WH1</span>
                <span class='box-label'>WH2</span>
                <span class='box-label'>WH3</span>
                </div>

                <div class='container-label' >
                <span class='box-label'></span>
                <span class='box-label'></span>
                <span class='box-label'></span>
                <span class='box-label'></span>
                <span class='box-label'></span>
                </div>
                </div>

            </td>
            </tr>
            <tr>
            <td colspan='4'><div align='center'><span class='style10'>OP : " . $db->operator_name . "</span></div></td>
            </tr>
            <tr>
                <td colspan='4'>
                <div align='center'><span class='style2'> <strong>Model : " . $db->model_name . "</strong></span></div>
                </td>
            </tr>
            <tr>
                <td colspan='4' >
                <div align='center'><span class='style10'> <strong> " . $item_no . "</strong></span></div>
                </td>
            </tr>
            <tr>
            <td colspan='5'><div align='center'><span class='style11'><strong>" . $item_name . "</strong> </span></div></td>
            </tr>
            <tr>
            <td colspan='5'><div align='center'><span class='style10'>Created :  " . $created_by . " / " . $log_date . "</span></div></td>
            </tr> 
            <tr>  
            <td colspan='5'><div align='center'><span class='style10'>Printed : " . auth()->user()->username . " / " . $print_time . "</span></div></td>
            </tr> 
        </table>
       <div class='pagebreak'></div> 
        ";
            }
        } else {
            echo  'Failed Url !!!';
        }
        return view('time_entry.print_all');
    }

    public function print_label_export(Request $request)
    {

        date_default_timezone_set('Asia/Jakarta');
        $print_time = date("d M Y H:i ");
        $str_req = explode("_", $request->ref_doc);
        $str_id = explode("~", Crypt::decryptString(str_replace("-", "=", $str_req[0])));

        $job_num = $str_id[0];
        $process_detail_id = $str_id[1];
        $part_num = $str_id[2];
        $dtCount = TimeEntry::get_detail_tag_label($job_num, $process_detail_id, $part_num)->get()->count();
        if (count($str_id) > 3) {
            $line_search_id = $str_id[3];
            $db_data = TimeEntry::get_detail_tag_label_id($job_num, $process_detail_id, $line_search_id, $part_num)->get();
        } else {
            $db_data = TimeEntry::get_detail_tag_label($job_num, $process_detail_id, $part_num)->get();
        }
        // dd($db_data);
        if ($db_data->count() > 0) {
            foreach ($db_data  as $db) {
                $barcode = $db->item_no . '~' . $db->qty_1 . '~05-10-01~GENERAL~' . $db->job_num . '~A';
                $so_date = AppModel::local_date_formate_name($db->production_date);
                $log_date = AppModel::local_date_formate_name($db->log_date);
                $created_by = $db->created_by;
                $item_no = $db->item_no;
                $item_name = $db->item_name;
                $partner_code = $db->cust_name;
                $qty_1 = $db->qty_1;
                $qc_status = 'OK';
                // if ($db->special_mark == 1) {
                // $special_mark = '<br> ++S' ;
                // } else {
                // $special_mark = '' ; 
                // }
                $special_mark = '';
                echo
                "   
        <style>
    .container-label {
      display: flex;
      flex-wrap: wrap;
      font-size: 7px;
      text-align:center;
    }

    .box-label {
      width: 18px;
      height: 18px; 
      margin: 2px;
      text-align: center;
      line-height: 18px;
      border: 2px solid black; /* Menambahkan border hitam */
    }
  </style>
        <table class='tg' style='undefined;table-layout: fixed; width: 340px; border: solid black 3px;'> 
        <colgroup>
        <col style='width: 140px'>
        <col style='width: 70px'>
        <col style='width: 30px'>
        <col style='width: 50px'>
        <col style='width: 50px'>
        </colgroup>
        <tr> 
            <th class='tg-baqhh' colspan='5' style='font-family:'Courier New', Courier, monospace !important; font-size:14px; text-align: left; border-left: none; border-right: none; border-top: none;'>FO-44-02   Rev. 01</th> 
            </tr> 
            <tr>
            <th colspan='5' style='border: solid black 3px; font-weight: bold; font-size: large; background-color: black; color:white;'>EXPORT PART</th> 
            </tr>
            <tr>
            <td rowspan='2'><div align='center'><span class='style91' style='font-weight: bold; font-size: xx-large;'>" . $qc_status . "</span>
            <br><span style='vertical-align:bottom; font-weight: bold;'></span>
            </td>
            <td colspan='4'>
            <div align='center' class='style9'>
                <h4 class='style10' style='margin:0; padding:0;'>PRODUCT IDENTIFICATION
                </h4>
            </div>
            <div align='center' class='style9'>
            <h5 style='font-weight: bold;" . ($so_date == '' ? '' : 'align:center') . " align:center; margin:0; padding:0;'>Prod. Date : <em> <u>" . $so_date . "</u></em> </h5>
            <h5 style='font-weight: bold; align:center; margin:0; padding:0;'>
               <em></em>
            </h5>
            </div>
            </td> 
            </tr>
            <tr>
            <td colspan='4'><div align='center'><span style='font-weight: bold; '>
            Line : " . $db->home_line_detail_id . " </span></div></td>
            </tr>
            <td><div align='center'><span class='style11'><strong>" . $db->job_num . "</strong></div></td>
            <td colspan='2'>
            <span class='style9'> Pallet No.</span>
          </td> 
          <td colspan='2'>
          <span class='style9'> QC CHECK</span>
          </td>
          </tr>
            <tr>
            <td><div align='Center' class='style9'><span class='style11''>QTY : <strong>" . $qty_1 . "</strong></span> </div></td>
            <td colspan='2'>
                <div align='center'><span class='style2'> <strong> " . $db->line_search_id . '/' . $dtCount . " 
                </strong></span>
               
                </div>
            </td>
            <td colspan='2'><div align='center'></br><span class='style2'>" . $db->quality_name . "</span></br></br></div></td>
            </tr>
            
            <tr>
            <td rowspan='4'>
            <div align='center'> 
            " . QrCode::size(90)->generate($barcode) . "</div>
            </div>
        </td>
            <td colspan='4' rowspan='2' >
            <div style='margin-left:30px; text-align: center;'>
                <div class='container-label' >
                <span class='box-label'>PD</span>
                <span class='box-label'>QC</span>
                <span class='box-label'>WH1</span>
                <span class='box-label'>WH2</span>
                <span class='box-label'>WH3</span>
                </div>

                <div class='container-label' >
                <span class='box-label'></span>
                <span class='box-label'></span>
                <span class='box-label'></span>
                <span class='box-label'></span>
                <span class='box-label'></span>
                </div>
                </div>

            </td>
            </tr>
            <tr>
            <td colspan='4'><div align='center'><span class='style10'>OP : " . $db->operator_name . "</span></div></td>
            </tr>
            <tr>
                <td colspan='4'>
                <div align='center'><span class='style2'> <strong>Model : " . $db->model_name . "</strong></span></div>
                </td>
            </tr>
            <tr>
                <td colspan='4' >
                <div align='center'><span class='style10'> <strong> " . $item_no . "</strong></span></div>
                </td>
            </tr>
            <tr>
            <td colspan='5'><div align='center'><span class='style11'><strong>" . $item_name . "</strong> </span></div></td>
            </tr>
            <tr>
            <td colspan='5'><div align='center'><span class='style10'>Created :  " . $created_by . " / " . $log_date . "</span></div></td>
            </tr> 
            <tr>  
            <td colspan='5'><div align='center'><span class='style10'>Printed : " . auth()->user()->username . " / " . $print_time . "</span></div></td>
            </tr> 
        </table>
       <div class='pagebreak'></div> 
        ";
            }
        } else {
            echo  'Failed Url !!!';
        }
        return view('time_entry.print_all');
    } 
    public function export_production_sch(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $print_time = date("m-d-Y H:i");
        $date = $request->date_sch;
        $category =  $request->category;
        $line =  $request->line;
        $shift =  $request->shift;
        $docnum =  $request->docnum;
        $db_data = TimeEntry::get_detail_list($date, $line, $shift, $docnum)->get();
        $d['doc_date'] = AppModel::local_date_formate_name($date);
        $d['category'] = $category;
        $d['line'] = $request->line;
        $d['docnum'] = $docnum;
        $d['shift'] = $shift;
        $d['db_data'] = $db_data;
        $d['num'] = $db_data->count();
        return view('time_entry.export_data', $d);
    }
}
