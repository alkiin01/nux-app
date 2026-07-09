<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CustomerShipment extends Model
{
    use HasFactory;

    public static function get_transaction_list($search, $status_id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $start_date = date('Y-m-d', strtotime('-15 days'));
        $end_date = date('Y-m-d', strtotime('+2 day'));
        $result = DB::connection('sqlsrv4')->table('ShipHead')->where('ReadyToInvoice', 0);
        if (!empty($search)) {
            $result = $result->where(function ($query) use ($search) {
                $query->where('LegalNumber', 'LIKE', "%$search%")
                    ->orWhere('PackNum', 'LIKE', "%$search%");
            });
        } else {
            $result = $result->whereBetween('ShipDate', [$start_date, $end_date]);
        }
        if ($status_id == 1) {
            $result = $result->where(function ($query) {
                $query->where('ReadyToPrint_c', '=', 0);
            });
        } else if ($status_id == 2) {
            $result = $result->where(function ($query) {
                $query->where('ReadyToPrint_c', '=', 1);
            });
        }
        $result = $result->select('LegalNumber', 'PackNum', 'ShipDate', 'EntryPerson', 'ReadyToInvoice', 'ReadyToPrint_c');
        return $result;
    }

    public static function check_access_buyer($po_num, $my_username)
    {

        $db_approval = DB::connection('sqlsrv5')->table('f_po_approval_status()')->where('po_num', $po_num)->get();

        $status_checker = 0;
        $status_approver = 0;
        $status_legalizer = 0;
        if ($db_approval->count() > 0) {
            foreach ($db_approval as $row) {
                $status_checker = ($row->status_checker == '' ? 0 : 1);
                $status_approver = ($row->status_approver == '' ? 0 : 1);
                $status_legalizer = ($row->status_legalizer == '' ? 0 : 1);
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

    public static function get_count_document_check()
    {
        $result = DB::connection('sqlsrv5')->table('f_po_approval_status()')
            ->where(function ($query) {
                $query->where('status_checker', '=', 'PENDING')
                    ->orWhere('status_checker', '=', '');
            })
            ->count();
        return $result;
    }

    public static function get_count_document_approve()
    {
        $result = DB::connection('sqlsrv5')->table('f_po_approval_status()')
            ->where(function ($query) {
                $query->where('status_checker', '=', 'APPROVED');
            })
            ->where(function ($query) {
                $query->where('status_approver', '=', 'PENDING')
                    ->orWhere('status_approver', '=', '');
            })
            ->count();
        return $result;
    }

    public static function get_count_document_legal()
    {
        $result = DB::connection('sqlsrv5')->table('f_po_approval_status()')
            ->where(function ($query) {
                $query->where('status_checker', '=', 'APPROVED');
            })
            ->where(function ($query) {
                $query->where('status_approver', '=', 'APPROVED');
            })
            ->where(function ($query) {
                $query->where('status_legalizer', '=', 'PENDING')
                    ->orWhere('status_legalizer', '=', '');
            })
            ->count();
        return $result;
    }

    public static function get_count_document()
    {
        $result = DB::connection('sqlsrv5')->table('f_po_approval_status()')
            ->where(function ($query) {
                $query->where('status_legalizer', '=', 'PENDING')
                    ->orWhere('status_legalizer', '=', '');
            })
            ->count();
        return $result;
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
        $result = DB::connection('sqlsrv5')->table('f_po_approval_status()')
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
            ->where('a.PONum', '=', $po_num)
            ->select('a.*', DB::raw("NULL as PostCategoryID"),  DB::raw("NULL  AS Convertion"), DB::raw("NULL AS MLedgerID"))
            ->orderBy('a.POLine', 'ASC')
            ->get();
        return $t;
    }

    public static function load_all_data_print_page($trc_type_id, $month_id, $trc_id, $rev_id, $Status, $print_option)
    {
        $query = DB::connection('sqlsrv2')->table('Q_G2_Proc')
            ->where('C010_TrcTypeID', '=', $trc_type_id)
            ->where('C011_Month', '=', $month_id)
            ->where('C012_TrcID', '=', $trc_id)
            ->where('C050_Rev', '=', $rev_id)
            ->select('Q_G2_Proc.*', DB::raw("NULL as PostCategoryID"))
            ->orderBy('C000_SysID', 'ASC')
            ->get();

        $data = array();
        $no =  1;

        foreach ($query as $l) {
            $dtDelivery = AppModel::local_date_formate_name(substr($l->C063_dtDelivery, 0, 10));
            $ItemName = $l->ItemName_Out;
            $ItemNum_Req = $l->ItemNum_Req;
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

            $line_x = $no . '^' . $Product . '^' . $dtDelivery . '^' . number_format(round($l->C110_Qty2)) . '^' . $l->Unit_Req . '^' . number_format(round($l->C102_PriceInt, 2), 2) . '^' . number_format(round($l->C127_AmountDiscount, 2), 2) . '^' . number_format(round($l->C125_AmountInt, 2), 2) . '^' . number_format(round($l->C110_Qty)) . '^' . $l->Unit_Out . '^' . $l->PostCategoryID;
            $no++;
            $data[] = explode('^', chop($line_x));
        }
        return $data;
    }

    public static function load_data_print_page($po_num, $Status, $print_option, $offset, $limit)
    {
        $query = DB::connection('sqlsrv4')->table('Erp.PODetail AS a')
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
                'a.UnitCost',
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
                'a.UnitCost',
                'a.DocExtCost',
                'a.PUM',
                'a.IUM',
                'e.ReqCategory_c',
                'b.ReqNum',
                'b.BaseQty',
                'b.RelQty'
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


            $line_x = $no . '^' . $ItemNum_Req . '^' . $dtDelivery . '^' . number_format(round($l->BaseQty)) . '^' . $l->PUM . '^' .
                number_format(round($l->UnitCost, 2), 2) . '^' . number_format(round($l->UnitCost * $l->RelQty, 2), 2) . '^' .
                number_format(round($l->UnitCost * $l->RelQty, 2), 2) . '^' . number_format(round($l->RelQty)) . '^' . $l->IUM . '^0^' . $ItemName
                . '^' . $l->ReqCategory_c . '^' . $l->ReqNum;

            $no++;
            $data[] = explode('^', chop($line_x));
        }
        return $data;
    }

    public static function load_all_data_print_page_sbc($trc_type_id, $month_id, $trc_id, $rev_id, $Status, $print_option)
    {
        $query = DB::connection('sqlsrv2')->table('Q_G2_Proc_Subcon')
            ->where('C010_TrcTypeID', '=', $trc_type_id)
            ->where('C011_Month', '=', $month_id)
            ->where('C012_TrcID', '=', $trc_id)
            ->where('C050_Rev', '=', $rev_id)
            ->select('Q_G2_Proc_Subcon.*', DB::raw("NULL as PostCategoryID"))
            ->orderBy('C000_SysID', 'ASC')
            ->get();
        $data = array();
        $no = 1;
        foreach ($query as $l) {
            $dtDelivery = AppModel::local_date_formate_name(substr($l->C063_dtDelivery, 0, 10));
            $ItemName = $l->ItemName_Out;
            $ItemNum_Req = $l->ItemNum_Req;
            if ($Status == 1) {
                $StatusX = '- Draft';
            } else {
                $StatusX = '';
            }
            if ($print_option != 0) {
                $Product = $ItemNum_Req . ' - ' . $ItemName;
            } else {
                $Product = $ItemNum_Req . ' - ' . $ItemName;
            }

            $line_x = $no . '^' . $Product . '^' . $dtDelivery . '^' . number_format(round($l->C110_Qty2)) . '^' . $l->Unit_Req . '^' . number_format(round($l->C102_PriceInt, 2), 2) . '^' . number_format(round($l->C127_AmountDiscount)) . '^' . number_format(round($l->C125_AmountInt, 2), 2) . '^' . number_format(round($l->C110_Qty)) . '^' . $l->Unit_Out . '^' . $l->PostCategoryID;
            $no++;
            $data[] = explode('^', chop($line_x));
        }
        return $data;
    }

    public static function load_data_print_page_sbc($trc_type_id, $month_id, $trc_id, $rev_id, $Status, $print_option, $offset, $limit)
    {
        $query = DB::connection('sqlsrv2')->table('Q_G2_Proc_Subcon')
            ->where('C010_TrcTypeID', '=', $trc_type_id)
            ->where('C011_Month', '=', $month_id)
            ->where('C012_TrcID', '=', $trc_id)
            ->where('C050_Rev', '=', $rev_id)
            ->select('Q_G2_Proc_Subcon.*', DB::raw("NULL as PostCategoryID"))
            ->offset($offset)
            ->limit($limit)
            ->orderBy('C000_SysID', 'ASC')
            ->get();
        $data = array();
        $no = $offset + 1;
        foreach ($query as $l) {
            $dtDelivery = AppModel::local_date_formate_name(substr($l->C063_dtDelivery, 0, 10));
            $ItemName = $l->ItemName_Out;
            $ItemNum_Req = $l->ItemNum_Req;
            if ($Status == 1) {
                $StatusX = '- Draft';
            } else {
                $StatusX = '';
            }
            if ($print_option != 0) {
                $Product = $ItemNum_Req . ' - ' . $ItemName;
            } else {
                $Product = $ItemNum_Req . ' - ' . $ItemName;
            }

            $line_x = $no . '^' . $ItemNum_Req . '^' . $dtDelivery . '^' . number_format(round($l->C110_Qty2)) . '^' . $l->Unit_Req . '^' . number_format(round($l->C102_PriceInt, 2), 2) . '^' . number_format(round($l->C127_AmountDiscount, 2), 2) . '^' . number_format(round($l->C125_AmountInt, 2), 2) . '^' . number_format(round($l->C110_Qty)) . '^' . $l->Unit_Out . '^' . $l->PostCategoryID . '^' . $ItemName;
            $no++;
            $data[] = explode('^', chop($line_x));
        }
        return $data;
    }

    public static function load_all_data_print_page_others($trc_type_id, $month_id, $trc_id, $rev_id, $Status, $print_option)
    {
        $query = DB::connection('sqlsrv2')->table('Q_G2_Proc_Other')
            ->where('C010_TrcTypeID', '=', $trc_type_id)
            ->where('C011_Month', '=', $month_id)
            ->where('C012_TrcID', '=', $trc_id)
            ->where('C050_Rev', '=', $rev_id)
            ->select('Q_G2_Proc_Other.*', 'ItemName AS ItemName_Out', 'ItemNum AS ItemNum_Out', 'C100_ItemIntID AS C100_ItemExtID', 'C150_UnitConvertion AS QtyOutToReq', 'Unit AS Unit_Out', 'BuyUnit AS Unit_Req')
            ->orderBy('C000_SysID', 'ASC')
            ->get();
        $data = array();
        $no = 1;
        foreach ($query as $l) {
            $dtDelivery = AppModel::local_date_formate_name(substr($l->C063_dtDelivery, 0, 10));
            $ItemName = $l->ItemName_Out;
            $ItemNum_Req = $l->ItemNum_Out;
            if ($Status == 1) {
                $StatusX = '- Draft';
            } else {
                $StatusX = '';
            }
            if ($print_option != 0) {
                $Product = $ItemNum_Req . ' - ' . $ItemName;
            } else {
                $Product = $ItemNum_Req . ' - ' . $ItemName;
            }

            $line_x = $no . '^' . $Product . '^' . $dtDelivery . '^' . number_format(round($l->C110_Qty2)) . '^' . $l->Unit_Req . '^' . number_format($l->C102_PriceInt, 2) . '^' . number_format(round($l->C127_AmountDiscount, 2), 2) . '^' . number_format($l->C125_AmountInt, 2) . '^' . number_format(round($l->C110_Qty)) . '^' . $l->Unit_Out . '^' . $l->PostCategoryID;
            $no++;
            $data[] = explode('^', chop($line_x));
        }
        return $data;
    }

    public static function load_data_print_page_others($trc_type_id, $month_id, $trc_id, $rev_id, $Status, $print_option, $offset, $limit)
    {
        $query = DB::connection('sqlsrv2')->table('Q_G2_Proc_Other')
            ->where('C010_TrcTypeID', '=', $trc_type_id)
            ->where('C011_Month', '=', $month_id)
            ->where('C012_TrcID', '=', $trc_id)
            ->where('C050_Rev', '=', $rev_id)
            ->select('Q_G2_Proc_Other.*', 'ItemName AS ItemName_Out', 'ItemNum AS ItemNum_Out', 'C100_ItemIntID AS C100_ItemExtID', 'C150_UnitConvertion AS QtyOutToReq', 'Unit AS Unit_Out', 'BuyUnit AS Unit_Req')
            ->offset($offset)
            ->limit($limit)
            ->orderBy('C000_SysID', 'ASC')
            ->get();
        $data = array();
        $no = $offset + 1;
        foreach ($query as $l) {
            $dtDelivery = AppModel::local_date_formate_name(substr($l->C063_dtDelivery, 0, 10));
            $ItemName = $l->ItemName_Out;
            $ItemNum_Req = $l->ItemNum_Out;
            if ($Status == 1) {
                $StatusX = '- Draft';
            } else {
                $StatusX = '';
            }
            if ($print_option != 0) {
                $Product = $ItemNum_Req . ' - ' . $ItemName;
            } else {
                $Product = $ItemNum_Req . ' - ' . $ItemName;
            }

            $line_x = $no . '^' . $ItemNum_Req . '^' . $dtDelivery . '^' . number_format(round($l->C110_Qty2)) . '^' . $l->Unit_Req . '^' . number_format(round($l->C102_PriceInt, 2), 2) . '^' . number_format(round($l->C127_AmountDiscount, 2), 2) . '^' . number_format(round($l->C125_AmountInt, 2), 2) . '^' . number_format(round($l->C110_Qty)) . '^' . $l->Unit_Out . '^' . $l->PostCategoryID . '^' . $ItemName;
            $no++;
            $data[] = explode('^', chop($line_x));
        }
        return $data;
    }

    public static function load_data_print_jurnal($trc_type_id, $month_id, $trc_id, $rev_id)
    {
        $query = DB::connection('sqlsrv2')->table('Q610_Jurnal')
            ->groupBy('C010_TrcTypeID', 'C011_Month', 'C000_SysID', 'C050_Rev', 'Code', 'Account', 'MinPlus', 'Currency')
            ->having('C010_TrcTypeID', '=', $trc_type_id)
            ->having('C011_Month', '=', $month_id)
            ->having('C000_SysID', '=', $trc_id)
            ->having('C050_Rev', '=', $rev_id)
            ->select('C010_TrcTypeID', 'C011_Month', 'C000_SysID', 'C050_Rev', 'Code', 'Account', 'MinPlus', DB::raw('SUM(Debet_IDR) AS Debet_IDR'), DB::raw('SUM(Credit_IDR) AS Credit_IDR'), 'Currency')
            ->orderBy('MinPlus', 'DESC');
        $data = array();
        $no = 1;
        foreach ($query->get() as $l) {
            $line_x = $no . '^' . $l->Code . '^' . $l->Account . '^' . $l->Currency . '^' . ($l->Debet_IDR == 0 ? '' : number_format($l->Debet_IDR, 2)) . '^' . ($l->Credit_IDR == 0 ? '' : number_format($l->Credit_IDR, 2));
            $no++;
            $data[] = explode('^', chop($line_x));
        }
        return $data;
    }

    public static function rows_data_print_jurnal($trc_type_id, $month_id, $trc_id, $rev_id)
    {
        $query = DB::connection('sqlsrv2')->table('Q610_Jurnal')
            ->groupBy('C010_TrcTypeID', 'C011_Month', 'C000_SysID', 'C050_Rev', 'Code', 'Account', 'MinPlus')
            ->having('C010_TrcTypeID', '=', $trc_type_id)
            ->having('C011_Month', '=', $month_id)
            ->having('C000_SysID', '=', $trc_id)
            ->having('C050_Rev', '=', $rev_id)
            ->select('C010_TrcTypeID', 'C011_Month', 'C000_SysID', 'C050_Rev', 'Code', 'Account', 'MinPlus', DB::raw('SUM(Debet_IDR) AS Debet_IDR'), DB::raw('SUM(Credit_IDR) AS Credit_IDR'))
            ->orderBy('MinPlus', 'DESC')
            ->get();;
        return $query;
    }
}
