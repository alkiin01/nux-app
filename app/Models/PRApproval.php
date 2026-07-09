<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PRApproval extends Model
{
    use HasFactory;

    public static function get_transaction_list($search, $status_id, $section_id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $start_date = date('Y-m-d', strtotime('-60 days'));
        $end_date = date('Y-m-d', strtotime('+1 day'));
        $result = DB::connection('sqlsrv5')->table("f_pr_approval_status('$section_id')");
        if (!empty($search)) {
            $result = $result->where(function ($query) use ($search) {
                $query->where('docnum', 'LIKE', "%$search%")
                    ->orWhere('pr_num', 'LIKE', "%$search%");
            });
        } else {
            $result = $result->whereBetween('docdate', [$start_date, $end_date]);
        }
        if ($status_id == 1) {
            $result = $result->where(function ($query) {
                $query->where('status_checker', '=', 'P');
            });
        } else if ($status_id == 2) {
            $result = $result->where(function ($query) {
                $query->where('status_checker', '=', 'A');
                $query->where('status_approver', '=', 'P');
            })
                ->orWhere(function ($query) {
                    $query->where('status_approver', '=', 'P');
                    $query->where('status_checker', '=', null);
                });
        } else if ($status_id == 3) {
            $result = $result->where(function ($query) {
                $query->where('status_checker', '=', 'A');
            })
                ->where(function ($query) {
                    $query->where('status_approver', '=', 'A');
                })
                ->where(function ($query) {
                    $query->where('status_legalizer', '=', 'P')
                        ->orWhere('status_legalizer', '=', '');
                });
        }
        return $result;
    }

    public static function check_access_req_action($pr_num, $my_username, $section_id)
    {

        $db_approval = DB::connection('sqlsrv5')->table("f_pr_approval_status('$section_id')")->where('pr_num', $pr_num)->get();
        $status_checker = 0;
        $status_approver = 0;
        $status_legalizer = 0;
        if ($db_approval->count() > 0) {
            foreach ($db_approval as $row) {
                $status_checker = (($row->status_checker == 'P' || $row->status_approver == 'R') ? ($row->status_checker == '' ? 1 : 0) : 1);
                $status_approver = (($row->status_approver == 'P' || $row->status_approver == 'R') ? 0 : 1);
                $status_legalizer = (($row->status_legalizer == 'P' || $row->status_legalizer == 'R') ? 0 : 1);
                $req_action_id2 = $row->req_action_id2;
                $req_action_id3 = $row->req_action_id3;
                $req_action_id4 = $row->req_action_id4;
            }
        }

        if ($status_checker == 0) {
            $req_action_id = $req_action_id2;
        } else if ($status_checker == 1 && $status_approver == 0) {
            $req_action_id = $req_action_id3;
        } else if ($status_checker == 1 && $status_approver == 1 && $status_legalizer == 0) {
            $req_action_id = $req_action_id4;
        } else {
            $req_action_id = '';
        }

        $result = DB::connection('sqlsrv4')->table('Erp.ReqActs AS a')
            ->where('a.UserList', 'LIKE', '%' . $my_username . '%')
            ->where('a.ReqActionID', $req_action_id)
            ->count();
        return $result;
    }

    public static function get_attachment_list($pr_num)
    {
        $result = DB::connection('sqlsrv4')->table('Ice.XFileAttch as A')
            ->select('A.RelatedToFile', 'A.Key1', 'B.*')
            ->leftJoin('Ice.XFileRef as B', 'B.XFileRefNum', '=', 'A.XFileRefNum')
            ->where('A.Key1', '=', $pr_num)
            ->where('A.RelatedToFile', 'LIKE', '%Req%')
            ->get();
        return $result;
    }

    public static function get_comment_list($pr_num)
    {
        $result = DB::table('t500_comment')
            ->where('docnum', '=', $pr_num)
            ->where('key_comment', '=', 'ReqHead')
            ->orderBy('id', 'DESC')
            ->get();
        return $result;
    }

    // public static function get_email_by_username($user_id)
    // {
    //     $result = env('MAIL_USERNAME')."~System";
    //     $db_buyer = DB::table('users')
    //         ->where('username', "$user_id")
    //         ->get();
    //     if ($db_buyer->count() > 0) {
    //         foreach ($db_buyer as $row) {
    //             $result = ($row->email != '' ? $row->email : env('MAIL_USERNAME'))."~".$row->full_name ;
    //         }
    //     } 
    //     return $result;
    // }

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

    public static function get_list_email_buyer()
    { 
        $results = DB::connection('sqlsrv4')->table('Erp.PurAuth as a')
        ->join('Erp.UserFile as b', 'a.DcdUserID', '=', 'b.DcdUserID')
        ->join('Erp.UserComp as c', function($join) {
            $join->on('a.DcdUserID', '=', 'c.DcdUserID'); 
            $join->on('a.BuyerID', '=', 'c.PrimBuyerID'); 
        })
        ->whereNotNull('b.EMailAddress')
        ->where('a.BuyerID', 'REGULER')
        ->whereNotNull('c.PrimBuyerID')
        ->select('b.EMailAddress')  
        ->get();
        return $results ;
    }

    public static function get_req_action_name($req_action_id)
    {
        $db_buyer = DB::connection('sqlsrv4')->table('Erp.ReqActs')
            ->where('ReqActionID', '=', "$req_action_id")
            ->get();
        if ($db_buyer->count() > 0) {
            foreach ($db_buyer as $row) {
                $result = $row->ReqActionDesc;
            }
        } else {
            $result = '';
        }
        return $result;
    }

    public static function sent_comment($pr_num, $my_username, $my_fullname, $comment)
    {
        $result = DB::table('t500_comment')
            ->insert([
                'docnum' => $pr_num,
                'username' => $my_username,
                'fullname' => $my_fullname,
                'comment' => $comment,
                'key_comment' => 'ReqHead',
            ]);
        return $result;
    }

    public static function get_count_document_check($section_id)
    {
        $result = DB::connection('sqlsrv5')->table("f_pr_approval_status('$section_id')")
            ->where('status_checker', '=', 'P')
            ->count();
        return $result;
    }

    public static function get_count_document_approve($section_id)
    {
        $result = DB::connection('sqlsrv5')->table("f_pr_approval_status('$section_id')")
            ->where(function ($query) {
                $query->where('status_approver', '=', 'P');
                $query->where('status_checker', '=', 'A');
            })
            ->orWhere(function ($query) {
                $query->where('status_approver', '=', 'P');
                $query->where('status_checker', '=', null);
            })
            ->count();
        return $result;
    }

    public static function get_count_document_legal($section_id)
    {
        $result = DB::connection('sqlsrv5')->table("f_pr_approval_status('$section_id')")
            ->where(function ($query) {
                $query->where('status_approver', '=', 'A');
            })
            ->where(function ($query) {
                $query->where('status_legalizer', '=', 'P');
            })
            ->count();
        return $result;
    }

    public static function get_count_document($section_id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $start_date = date('Y-m-d', strtotime('-60 days'));
        $end_date = date('Y-m-d', strtotime('+1 day'));
        $result = DB::connection('sqlsrv5')->table("f_pr_approval_status('$section_id')")->whereBetween('docdate', [$start_date, $end_date])
            ->count();
        return $result;
    }

    public static function sum_detail_doc($pr_num)
    {
        $t = DB::connection('sqlsrv4')
            ->table('Erp.PODetail AS a')
            ->where('a.PONum', '=', $pr_num)
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

    public static function get_sequence_approval($pr_num, $section_id)
    {
        $result = DB::connection('sqlsrv5')->table("f_pr_approval_status('$section_id')")
            ->where('pr_num', $pr_num)
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


    public static function data_detail($pr_num)
    {
        $result = DB::connection('sqlsrv4')->table('Erp.ReqHead  AS a')
            ->join('Erp.ReqHead_UD AS b', 'a.SysRowID', '=', 'b.ForeignSysRowID')
            ->select('b.DocNum_C', 'b.ReqCategory_c', 'a.*')
            ->where('a.ReqNum', $pr_num)->get();

        return $result;
    }

    public static function summary_ammount($pr_num)
    {
        $result = DB::connection('sqlsrv4')->table('Erp.ReqDetail  AS a')
            ->where('a.ReqNum', $pr_num)
            ->select(DB::raw('SUM(a.DocUnitCost*a.OrderQty)  AS amount'))
            ->groupBy('a.ReqNum')
            ->get()->first()->amount;

        return $result;
    }

    public static function summary_qty($pr_num)
    {
        $result = DB::connection('sqlsrv4')->table('Erp.ReqDetail  AS a')
            ->where('a.ReqNum', $pr_num)
            ->select(DB::raw('SUM(a.XOrderQty)  AS amount'))
            ->groupBy('a.ReqNum')
            ->get()->first()->amount;

        return $result;
    }


    public static function detail_list_data($pr_num)
    {
        $t = DB::connection('sqlsrv4')->table('ReqDetail AS a')
            ->where('a.ReqNum', '=', $pr_num)
            ->select('a.*', DB::raw("NULL as PostCategoryID"),  DB::raw("NULL  AS Convertion"), DB::raw("NULL AS MLedgerID"))
            ->orderBy('a.ReqLine', 'ASC')
            ->get();
        return $t;
    }

    public static function load_data_print_page($pr_num, $Status, $print_option, $offset, $limit)
    {
        $query = DB::connection('sqlsrv4')->table('Erp.ReqDetail AS a')
            ->where('a.ReqNum', '=', $pr_num)
            ->select('a.*', DB::raw("NULL as PostCategoryID"))
            ->offset($offset)
            ->limit($limit)
            ->orderBy('a.ReqLine', 'ASC')
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

            $price = $l->DocUnitCost ;

            $line_x = $no . '^' . $Product . '^' . number_format($l->XOrderQty, 0) . '^' .  $l->IUM. '^' . $dtDelivery . '^' . number_format($l->OrderQty, 0) . '^' . $l->RUM . '^' . number_format($l->DocUnitCost, 0) . '^' . $l->DocUnitCost . '^' . $l->XOrderQty . '^' . $ItemName . '^' . $ItemNum_Req . '^' . $l->OrderQty ;

            $no++;
            $data[] = explode('^', chop($line_x));
        }
        return $data;
    }
}
