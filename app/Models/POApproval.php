<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class POApproval extends Model
{
    use HasFactory;
    public static function get_transaction_list($search, $status_id, $section_id, $start_date,$end_date)
    {
        date_default_timezone_set('Asia/Jakarta');
        if (empty($start_date) && empty($end_date)) {
            // $start_date = date('Y-m-d', strtotime('-60 days'));
            // $end_date = date('Y-m-d', strtotime('+5 day'));
            $start_date = date('Y-m-d', strtotime('-120 days'));
            $end_date = date('Y-m-d', strtotime('+5 day'));
        }
        
        $result = DB::connection('sqlsrv5')->table('f_po_approval_status2()');

        if (!empty($section_id)) {
            $result = $result->where(function ($query) use ($section_id) {
                $query->where('buyer_id', "$section_id");
            });
        }
        if (!empty($search)) {
            $result = $result->where(function ($query) use ($search) {
                $query->where('docnum', 'LIKE', "%$search%")
                    ->orWhere('po_num', 'LIKE', "%$search%");
            });
        }else{
            $result = $result->whereBetween('orderdate', [$start_date, $end_date]);
        }

        if ($status_id == 1) {
            $result = $result->where(function ($query) {
                $query->where('status_checker', '=', 'PENDING');
            });
        } else if ($status_id == 2) {
            $result = $result->where('status_checker', '<>', 'PENDING')->where('status_approver', '=', 'PENDING');
        } else if ($status_id == 3) {
            $result = $result->where('status_approver', '=', 'APPROVED')
                ->where('status_legalizer', '=', 'Pending');
        } else {
            // $result = $result->whereBetween('orderdate', [$start_date, $end_date]);
        }
        return $result;
    }

    public static function get_email_by_username($user_id)
    { 
        $results = env('MAIL_USERNAME')."~System";
        $db_user = DB::connection('sqlsrv4')->table('Erp.UserFile as a') 
        ->where('a.DcdUserID', "$user_id")  
        ->get();
        if ($db_user->count() > 0) {
            foreach ($db_user as $row) {
                $results = ($row->EMailAddress != '' ? $row->EMailAddress : env('MAIL_USERNAME'))."~".$row->Name ;
            }
        } 
        return $results ;
    }
    public static function get_phone_number_by_username($user_id)
    { 
        $results = '';
        $db_user = DB::connection('sqlsrv4')->table('Erp.UserFile as a') 
        ->where('a.DcdUserID', "$user_id")  
        ->get();
        if ($db_user->count() > 0) {
            foreach ($db_user as $row) {
                $results = $row->Phone ;
            }
        } 
        return $results ;
    }
    
    public static function check_access_buyer($po_num, $my_username)
    {

        $db_approval = DB::connection('sqlsrv5')->table('f_po_approval_status2()')->where('po_num', $po_num)->get(); 
        $status_checker = 0;
        $status_approver = 0;
        $status_legalizer = 0;
        if ($db_approval->count() > 0) {
            foreach ($db_approval as $row) {
                $status_checker = ($row->status_checker == 'Pending' ? 0 : 1);
                $status_approver = ($row->status_approver == 'Pending' ? 0 : 1);
                $status_legalizer = ($row->status_legalizer == 'Pending' ? 0 : 1);
                $buyer_id2 = $row->buyer_id2;
                $buyer_id3 = $row->buyer_id3;
                $buyer_id4 = $row->buyer_id4;
            }
        }

        if ($status_checker == 0) {
            $buyer_id = $buyer_id2;
        } else if ($status_checker == 1 && $status_approver == 0) {
            $buyer_id = $buyer_id3;
        } else if ($status_checker == 1 && $status_approver == 1 && $status_legalizer == 0) {
            $buyer_id = $buyer_id4;
        } else {
            $buyer_id = '';
        }

        $result = DB::connection('sqlsrv4')->table('Erp.PurAuth AS a')
            ->where('a.DcdUserID', "$my_username")
            ->where('a.BuyerID', "$buyer_id")
            ->count();

        return $result;
    }

    public static function get_attachment_list($po_num)
    {
        $result = DB::connection('sqlsrv4')->table('Ice.XFileAttch as A')
            ->select('A.RelatedToFile', 'A.Key1', 'B.*')
            ->leftJoin('Ice.XFileRef as B', 'B.XFileRefNum', '=', 'A.XFileRefNum')
            ->where('A.Key1', '=', $po_num)
            ->where('A.RelatedToFile', 'LIKE', '%PO%')
            ->get();
        return $result;
    }

    public static function get_comment_list($po_num)
    {
        $result = DB::table('t500_comment')
            ->where('docnum', '=', $po_num)
            ->where('key_comment', '=', 'POHeader')
            ->orderBy('id', 'DESC')
            ->get();
        return $result;
    }

    public static function get_buyer_name($buyer_id)
    {
        $db_buyer = DB::connection('sqlsrv4')->table('Erp.PurAgent')
            ->where('BuyerID', '=', "$buyer_id")
            ->get();
        if ($db_buyer->count() > 0) {
            foreach ($db_buyer as $row) {
                $result = $row->Name;
            }
        } else {
            $result = '';
        }
        return $result;
    }

    public static function sent_comment($po_num, $my_username, $my_fullname, $comment)
    {
        $result = DB::table('t500_comment')
            ->insert([
                'docnum' => $po_num,
                'username' => $my_username,
                'fullname' => $my_fullname,
                'comment' => $comment,
                'key_comment' => 'POHeader',
            ]);
        return $result;
    }

    public static function get_count_document_check($section_id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $start_date = date('Y-m-d', strtotime('-60 days'));
        $end_date = date('Y-m-d', strtotime('+5 day'));

        $result = DB::connection('sqlsrv5')->table('f_po_approval_status2()')
            ->where(function ($query) {
                $query->where('status_checker', '=', 'PENDING');
                $query->where('approval_status','=','P');
                
            });
            if (!empty($section_id)) {
                $result = $result->where(function ($query) use ($section_id) {
                    $query->where('buyer_id', "$section_id");
                });
            }
            // ->whereBetween('orderdate', [$start_date, $end_date]);
        return $result->count();
    }

    public static function get_count_document_approve($section_id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $start_date = date('Y-m-d', strtotime('-60 days'));
        $end_date = date('Y-m-d', strtotime('+5 day'));
        $result = DB::connection('sqlsrv5')->table('f_po_approval_status2()')
            ->where(function ($query) {
                $query->where('status_checker', '=', 'APPROVED');
                $query->where('approval_status','=','P');

            })
            ->where(function ($query) {
                $query->where('status_approver', '=', 'PENDING')->orwhere('status_approver', '=', '');
            });
        if (!empty($section_id)) {
            $result = $result->where(function ($query) use ($section_id) {
                $query->where('buyer_id', "$section_id");
            });
        }
        return $result->count();
    }

    public static function get_count_document_legal($section_id)
    {
        $start_date = date('Y-m-d', strtotime('-60 days'));
        $end_date = date('Y-m-d', strtotime('+5 day'));
        date_default_timezone_set('Asia/Jakarta');
        $result = DB::connection('sqlsrv5')->table('f_po_approval_status2()')
            ->where('status_approver', '=', 'APPROVED')
            ->where('status_legalizer', '=', 'PENDING')
            ->where('approval_status','=','P')
            ->whereBetween('orderdate', [$start_date, $end_date]);
        if (!empty($section_id)) {
            $result = $result->where(function ($query) use ($section_id) {
                $query->where('buyer_id', "$section_id");
            });
        }

        return $result->count();
    }

    public static function get_count_document($section_id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $start_date = date('Y-m-d', strtotime('-60 days'));
        $end_date = date('Y-m-d', strtotime('+5 day'));

        $result = DB::connection('sqlsrv5')->table('f_po_approval_status2()')
            ->where('status_legalizer', '=', 'PENDING')
            ->whereBetween('orderdate', [$start_date, $end_date]);
        if (!empty($section_id)) {
            $result = $result->where(function ($query) use ($section_id) {
                $query->where('buyer_id', "$section_id");
            });
        }
        return $result->count();
    }

    public static function sum_detail_doc($po_num)
    {
        $t = DB::connection('sqlsrv4')
            ->table('Erp.PODetail AS a')
            ->where('a.PONum', '=', $po_num)
            ->select(DB::raw("SUM(a.DocExtCost + a.DocTotalTax) as result_sum"))
            ->get();
        if ($t->count() > 0) {
            foreach ($t as $h) {
                $result = $h->result_sum;
            }
        } else {
            $result = 0;
        }
        return $result;
    }

    public static function sum_detail_doc_qty($po_num)
    {
        $t = DB::connection('sqlsrv4')
            ->table('Erp.PODetail AS a')
            ->where('a.PONum', '=', $po_num)
            ->select(DB::raw("SUM(a.XOrderQty) as result_sum"))
            ->get();
        if ($t->count() > 0) {
            foreach ($t as $h) {
                $result = $h->result_sum;
            }
        } else {
            $result = 0;
        }
        return $result;
    }

    public static function find_trc_name($trc_type_id, $flow_id)
    {
        $db = DB::table('t100_transaction_type')
            ->where('trc_type_id', '=', $trc_type_id)
            ->where('flow_id', '=', $flow_id)
            ->select('trc_code')
            ->get();
        $r = $db->count();
        if ($r > 0) {
            foreach ($db as $h) {
                $result = $h->trc_code;
            }
        } else {
            $result = '';
        }
        return $result;
    }

    public static function get_sequence_approval($po_num)
    {
        $result = DB::connection('sqlsrv5')->table('f_po_approval_status2()')
            ->where('po_num', $po_num)
            ->get();
        return $result;
    }

    public static function get_section_list()
    {
        $result = DB::connection('sqlsrv4')->table('Ice.UD01')
            ->select('Key1 as id', 'Character02 as desc')
            ->where('Key5', 'Section')
            ->where('CheckBox01', 1)
            ->get();
        return $result;
    }


    public static function data_detail($po_num)
    {
        $result = DB::connection('sqlsrv4')->table('Erp.POHeader AS a')
            ->join('Erp.POHeader_UD AS b', 'a.SysRowID', '=', 'b.ForeignSysRowID')
            ->join('Erp.Vendor AS c', 'a.VendorNum', '=', 'c.VendorNum')
            ->select('b.DocNum_C', 'a.*')
            ->where('a.PONum', $po_num)->get();

        return $result;
    }


    public static function detail_list_data($po_num)
    {

        $t = DB::connection('sqlsrv4')->table('Erp.PODetail AS a')
            ->join('Erp.PORel AS b', function ($join) {
                $join->on('a.PONum', '=', 'b.PONum');
                $join->on('a.POLine', '=', 'b.POLine');
            })
            ->leftJoin('Erp.ReqHead AS d', 'b.ReqNum', '=', 'd.ReqNum')
            ->leftJoin('Erp.ReqHead_UD AS e', 'd.SysRowID', '=', 'e.ForeignSysRowID')
            ->where('a.PONum', '=', $po_num)
            ->select(
                'a.POLine',
                'b.PORelNum',
                'a.PartNum',
                'a.LineDesc',
                'a.DueDate',
                'a.OrderQty',
                'a.XOrderQty',
                'a.DocUnitCost',
                'a.DocExtCost',
                'a.PUM',
                'a.IUM',
                'e.ReqCategory_c',
                'b.ReqNum',
                'b.BaseQty',
                'b.RelQty'
            )
            ->groupBy(
                'a.POLine',
                'b.PORelNum',
                'a.PartNum',
                'a.LineDesc',
                'a.DueDate',
                'a.OrderQty',
                'a.XOrderQty',
                'a.DocUnitCost',
                'a.DocExtCost',
                'a.PUM',
                'a.IUM',
                'e.ReqCategory_c',
                'b.ReqNum',
                'b.BaseQty',
                'b.RelQty'
            )
            ->orderBy('a.POLine', 'ASC')
            ->get();

        return $t;
    }


    public static function load_data_print_page($po_num, $Status, $print_option, $offset, $limit)
    {
        $query = DB::connection('sqlsrv4')->table('PODetail AS a')
            ->join('Erp.PORel AS b', function ($join) {
                $join->on('a.PONum', '=', 'b.PONum');
                $join->on('a.POLine', '=', 'b.POLine');
            })
            ->leftJoin('Erp.ReqHead AS d', 'b.ReqNum', '=', 'd.ReqNum')
            ->leftJoin('Erp.ReqHead_UD AS e', 'd.SysRowID', '=', 'e.ForeignSysRowID')
            ->where('a.PONum', '=', $po_num)
            ->select(
                'a.POLine',
                'a.Discount_c',
                'b.PORelNum',
                'a.PartNum',
                'a.LineDesc',
                'a.DueDate',
                'a.OrderQty',
                'a.XOrderQty',
                'a.DocUnitCost',
                'a.DocExtCost',
                'a.BefDocPrice_c',
                'a.BefUnitPrice_c',
                'a.PUM',
                'a.IUM',
                'e.ReqCategory_c',
                'b.ReqNum',
                'b.BaseQty',
                'b.RelQty',
                'b.XRelQty'
            )
            ->groupBy(
                'a.POLine',
                'a.Discount_c',
                'b.PORelNum',
                'a.PartNum',
                'a.LineDesc',
                'a.DueDate',
                'a.OrderQty',
                'a.XOrderQty',
                'a.DocUnitCost',
                'a.DocExtCost',
                'a.BefDocPrice_c',
                'a.BefUnitPrice_c',
                'a.PUM',
                'a.IUM',
                'e.ReqCategory_c',
                'b.ReqNum',
                'b.BaseQty',
                'b.RelQty',
                'b.XRelQty'
            )
            ->offset($offset)
            ->limit($limit)
            ->orderBy('a.POLine', 'ASC')
            ->get();

        $data = array();
        $no = $offset + 1;

        foreach ($query as $l) {
            $dtDelivery = AppModel::local_date_formate_name(substr($l->DueDate, 0, 10));
            $ItemName = $l->LineDesc;
            $ItemNum_Req = $l->PartNum;
            if ($Status == 1) {
                $StatusX = '- Draft';
            } else {
                $StatusX = '';
            }
            if ($print_option != 0) {
                $Product = $ItemNum_Req . ' - ' . $ItemName;
            } else {
                $Product = $ItemName;
            }


            $line_x = $no . '^' . $ItemNum_Req . '^' . $dtDelivery . '^' . number_format(round($l->RelQty)) . '^' . $l->IUM . '^' .
                number_format(round($l->DocUnitCost, 2), 2) . '^' . number_format(round($l->DocUnitCost * $l->RelQty, 2), 2) . '^' .
                number_format(round($l->DocUnitCost * $l->RelQty, 2), 2) . '^' . number_format(round($l->XRelQty)) . '^' . $l->PUM . '^@^' . $ItemName
                . '^' . $l->ReqCategory_c . '^' . $l->ReqNum. '^' . number_format(round($l->BefUnitPrice_c, 2), 2) . '^' .  number_format(round($l->BefUnitPrice_c * $l->RelQty, 2), 2)
                . '^' . $l->Discount_c ;

            $no++;
            $data[] = explode('^', chop($line_x));
        }
        return $data;
    }
}
