<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
 

class ReceiptEntry extends Model
{
    use HasFactory;

    public static function get_vendor_list()
    {
        $result = DB::connection('sqlsrv4')->table('Erp.Vendor')
        ->where('Inactive', 0)
        ->select('VendorNum', 'VendorID', 'Name') ;  
        return $result;
    }

    public static function get_qty_balance_po_line($poNum, $poLine, $poRelNum, $currentQty, $packSlip, $packLine, $vendorNum)
    {
        $db_po = DB::connection('sqlsrv4')->table('Erp.PORel AS a')
            ->where('a.PONum', $poNum)
            ->where('a.POLine', $poLine)
            ->where('a.PORelNum', $poRelNum)
            ->get();
        if ($db_po->count() > 0) {
            foreach ($db_po as $row) {
                $RelQty = $row->RelQty ;
            }
        } else {
            $RelQty = 0;
        }
        

        $db_gr = DB::connection('sqlsrv4')->table('Erp.RcvDtl AS a')
            ->where('a.PackSlip', '<>', "$packSlip") 
            ->where('a.PackLine', '<>', $packLine)
            ->where('a.VendorNum', '<>', $vendorNum)
            ->where('a.PONum', $poNum)
            ->where('a.POLine', $poLine) 
            ->where('a.PORelNum', $poRelNum) 
            ->select(DB::raw("SUM(OurQty) AS OurQty"))
            ->get();
        if ($db_gr->count() > 0) {
            foreach ($db_gr as $row) {
                $RcvQty = $row->OurQty ;
            }
        } else {
            $RcvQty = 0;
        }  
         
        $balanceQty = $RelQty - $RcvQty - $currentQty ; 
        return $balanceQty ;
    }

    public static function get_job_qty_completed($inputOurQty, $packSlip, $packLine, $jobNum, $vendorNum)
    {  
        $ProdQty = 0 ;
        $db_job = DB::connection('sqlsrv4')->table('Erp.JobHead AS a')
            ->where('a.JobNum', "$jobNum") 
            ->select(DB::raw("SUM(ProdQty) AS ProdQty"))
            ->get();
        if ($db_job->count() > 0) {
            foreach ($db_job as $row) {
                $ProdQty = ($row->ProdQty == null ? 0 : $row->ProdQty) ;
            }
        } 

        $RcvQty = 0 ;
        $db_gr = DB::connection('sqlsrv4')->table('Erp.RcvDtl AS a') 
            ->where('a.PackSlip', '<>', "$packSlip") 
            ->where('a.PackLine', '<>', $packLine)
            ->where('a.VendorNum', '<>', $vendorNum)
            ->where('a.JobNum', "$jobNum") 
            ->select(DB::raw("SUM(OurQty) AS OurQty"))
            ->get();
        if ($db_gr->count() > 0) {
            foreach ($db_gr as $row) {
                $RcvQty = ($row->OurQty == null ? 0 : $row->OurQty) ;
            }
        }  

        // dd($ProdQty, $inputOurQty, $RcvQtyBefore, $RcvQty);
        $balanceQty = $ProdQty - $RcvQty - $inputOurQty ; 
        // dd($balanceQty);
        return $balanceQty ;
    }

    
    public static function get_total_qty_before($packSlip, $packLine, $vendorNum)
    {   
        $RcvQty = 0 ;
        $db_gr = DB::connection('sqlsrv4')->table('Erp.RcvDtl AS a') 
            ->where('a.PackSlip', "$packSlip") 
            ->where('a.PackLine', $packLine)
            ->where('a.VendorNum', $vendorNum) 
            ->select(DB::raw("SUM(OurQty) AS OurQty"))
            ->get();
        if ($db_gr->count() > 0) {
            foreach ($db_gr as $row) {
                $RcvQty = ($row->OurQty == null ? 0 : $row->OurQty) ;
            }
        }     
        return $RcvQty ;
    //     $RcvQty = DB::connection('sqlsrv4')->table('Erp.RcvDtl AS a')
    //     ->where('a.PackSlip', $packSlip)
    //     ->where('a.PackLine', $packLine)
    //     ->where('a.VendorNum', $vendorNum)
    //     ->sum('OurQty'); // langsung sum

    // return $RcvQty ?? 0;
    }

    public static function get_status_issue_job($packSlip, $packLine, $vendorNum)
    {     
        $result = '0~0' ;
        $db_gr = DB::connection('sqlsrv4')->table('RcvDtl AS a') 
            ->where('a.PackSlip', "$packSlip") 
            ->where('a.PackLine', $packLine)
            ->where('a.VendorNum', $vendorNum) 
            ->select('JobReceiptStatus_c', 'IssueMaterialStatus_c')
            ->get();
        if ($db_gr->count() > 0) {
            foreach ($db_gr as $row) {
                $result = $row->IssueMaterialStatus_c.'~'.$row->JobReceiptStatus_c ; 
            }
        }     
        return $result ;
    }

    public static function get_po_header($packSlip, $vendorNum)
    {   
        $PONum = 0 ;
        $db_gr = DB::connection('sqlsrv4')->table('Erp.RcvHead AS a') 
            ->where('a.PackSlip', "$packSlip")  
            ->where('a.VendorNum', $vendorNum)  
            ->get();
        if ($db_gr->count() > 0) {
            foreach ($db_gr as $row) {
                $PONum = $row->PONum ;
            }
        }     
        return $PONum ;
    }

    public static function get_job_info($jobNum)
    {  
        $db_job = DB::connection('sqlsrv4')->table('Erp.JobHead AS a')
            ->where('a.JobNum', "$jobNum") 
            ->select('a.ProdQty')
            ->get();
        if ($db_job->count() > 0) {
            foreach ($db_job as $row) {
                $ProdQty = $row->ProdQty ;
            }
        } else {
            $ProdQty = 0 ;
        } 
 
        return $ProdQty ;
    }

    public static function update_job_receipt_status($packSlip, $vendorNum, $packLine)
    {  
        $result = DB::connection('sqlsrv4')->table('Erp.RcvDtl_UD as b')
                ->join('Erp.RcvDtl as a', 'a.SysRowID', '=', 'b.ForeignSysRowID')
                ->where('a.PackSlip', "$packSlip")
                ->where('a.VendorNum', $vendorNum)
                ->where('a.PackLine', $packLine)
                ->update([
                    'b.JobReceiptStatus_c' => 1
                ]);  
        return $result ;
    }

    public static function update_issue_material_status($packSlip, $vendorNum, $packLine)
    {  
        $result = DB::connection('sqlsrv4')->table('Erp.RcvDtl_UD as b')
                ->join('Erp.RcvDtl as a', 'a.SysRowID', '=', 'b.ForeignSysRowID')
                ->where('a.PackSlip', "$packSlip")
                ->where('a.VendorNum', $vendorNum)
                ->where('a.PackLine', $packLine)
                ->update([
                    'b.IssueMaterialStatus_c' => 1 
                ]); 
        return $result ;
    }
    public static function get_list_job_mtl($jobNum, $assemblySeq, $jobSeq)
    {
        $result = DB::connection('sqlsrv4')
            ->table('Erp.JobMtl as a')
            ->select(
                'a.JobNum',
                'a.PartNum',
                'a.MtlSeq',
                'a.AssemblySeq',
                'a.RelatedOperation',
                'a.RequiredQty',
                'a.QtyPer',
                'ud.WhseSupply_c as WarehouseCode',
                'w.Description as WarehouseName',
                'ud.BinSupply_c as BinNum',
                'wb.Description as BinName',
            )
            ->join('Erp.JobHead as b', 'a.JobNum', '=', 'b.JobNum')
            ->join('Erp.JobHead_UD as ud', 'b.SysRowID', '=', 'ud.ForeignSysRowID')
            ->leftJoin('Erp.Warehse as w', 'ud.WhseSupply_c', '=', 'w.WarehouseCode')
            ->leftJoin('Erp.WhseBin as wb', function ($join) {
                $join->on('ud.WhseSupply_c', '=', 'wb.WarehouseCode')
                    ->on('ud.BinSupply_c', '=', 'wb.BinNum');
            })
            ->where('a.JobNum', $jobNum)
            ->where('a.AssemblySeq', $assemblySeq)
            ->where('a.RelatedOperation', $jobSeq)
            ->get();

        return $result;
    }

    // public static function get_list_job_mtl($jobNum, $assemblySeq, $jobSeq)
    // {   
    //     $result = DB::connection('sqlsrv4')->table('Erp.JobMtl as a')
    //     ->select(
    //         'a.JobNum',
    //         'a.PartNum',
    //         'a.MtlSeq',
    //         'a.AssemblySeq',
    //         'a.RelatedOperation',
    //         'a.RequiredQty',
    //         'a.QtyPer',
    //         'b.WhseSupply_c AS WarehouseCode',
    //         'b1.Description as WarehouseName',
    //         'b.BinSupply_c AS BinNum',
    //         'c1.Description as BinName'
    //     )
    //     ->join('JobHead as b', function ($join) {
    //         $join->on('a.JobNum', '=', 'b.JobNum') ; 
    //     })
    //     ->join('Warehse as b1', 'b.WhseSupply_c', '=', 'b1.WarehouseCode') 
    //     ->leftJoin('Erp.WhseBin as c1', function ($join) {
    //         $join->on('b.WhseSupply_c', '=', 'c1.WarehouseCode')
    //              ->on('b.BinSupply_c', '=', 'c1.BinNum');
    //     })
    //     ->where('a.JobNum', "$jobNum")
    //     ->where('a.AssemblySeq', $assemblySeq)
    //     ->where('a.RelatedOperation', $jobSeq)
    //     ->get(); 
        
    //     return $result ;
    // }

    public static function get_part_whse_receipt($partNum)
    {
        $db_partWhse = DB::connection('sqlsrv4')->table('Part AS a')
            ->where('a.PartNum', "$partNum")  
            ->get();
        if ($db_partWhse->count() > 0) {
            foreach ($db_partWhse as $row) {
                $WarehouseCode = ($row->WhseReceipt_c == '' ? '05-08-01' : $row->WhseReceipt_c) ;
                $BinNum = ($row->BinReceipt_c == '' ? 'GENERAL' : $row->BinReceipt_c) ;
            }
        } else {
            $WarehouseCode = '05-08-01' ;
            $BinNum = 'GENERAL' ;
        } 
        $WhseResult = $WarehouseCode.'~'.$BinNum ;
        return $WhseResult ;
    }  
 
    public static function get_transaction_list($search, $vendor_id, $status_id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $start_date = date('Y-m-d', strtotime('-15 days'));
        $end_date = date('Y-m-d', strtotime('+2 day'));
        $result = DB::connection('sqlsrv4')->table('RcvHead AS a')
        ->join('Vendor AS b', 'a.VendorNum', 'b.VendorNum') ;
    
        if (!empty($search)) {
            $result = $result->where(function ($query) use ($search) {
                $query->where('a.LegalNumber', 'LIKE', "%$search%")
                    ->orWhere('a.PackSlip', 'LIKE', "%$search%") ;
            });
        } else { 
            $result = $result->whereBetween('a.EntryDate', [$start_date, $end_date]); 
        }

        if ($vendor_id > 0) {
            $result = $result->where(function ($query) use ($vendor_id) {
                $query->where('a.VendorNum', '=', $vendor_id);
            });
        }

        if ($status_id == 1) {
            $result = $result->where(function ($query) {
                $query->where('a.Received', '=', 1);
            });
        } else if ($status_id == 0) {
            $result = $result->where(function ($query) {
                $query->where('a.Received', '=', 0);
            });
        } 
        return $result;
    }

    public static function get_count_document($vendor_id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $start_date = date('Y-m-d', strtotime('-15 days'));
        $end_date = date('Y-m-d', strtotime('+2 day'));
        $result = DB::connection('sqlsrv4')->table('RcvHead AS a')
        ->join('Vendor AS b', 'a.VendorNum', 'b.VendorNum')
        ->whereBetween('a.EntryDate', [$start_date, $end_date]) ;
        
        if ($vendor_id != 'null') {
            $result = $result->where(function ($query) use ($vendor_id) {
                $query->where('a.VendorNum', '=', $vendor_id);
            });
        }
        return $result->count() ;
    }

    public static function get_count_document_draft($vendor_id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $start_date = date('Y-m-d', strtotime('-15 days'));
        $end_date = date('Y-m-d', strtotime('+2 day'));

        $result = DB::connection('sqlsrv4')->table('RcvHead AS a')
        ->join('Vendor AS b', 'a.VendorNum', 'b.VendorNum') 
        ->where('a.Received', '=', 0) ; 

        if ($vendor_id != 'null') {
            $result = $result->where(function ($query) use ($vendor_id) {
                $query->where('a.VendorNum', '=', $vendor_id);
            });
        } 
        return $result->count() ;
    }
  
    public static function check_job_status($jobNum)
    {  
        $db_job_mtl = DB::connection('sqlsrv4')->table('Erp.JobHead as a')  
        ->select(
            'a.JobReleased',
            'a.JobEngineered',  
            'a.JobClosed'
        )
        ->where('a.JobNum', "$jobNum")  
        ->get(); 

        $result = ['code' => 1, 'status' => 'Ok !'] ; 
        if ($db_job_mtl->count() > 0) {
            foreach ($db_job_mtl AS $row) { 
                if ($row->JobReleased == 0) {
                    $result = ['code' => 0, 'status' => 'Job Belum direlease !'] ; 
                } else if ($row->JobEngineered == 0) {
                    $result = ['code' => 0, 'status' => 'Check Job Engineered !'] ; 
                } else if ($row->JobClosed == 1) {
                    $result = ['code' => 0, 'status' => 'Job Sudah Closed !'] ; 
                }
            }
        }  
        return $result ;
    }

    public static function check_part_onhand_status($jobNum, $thisTranQty, $lotNum)
    { 
        $qty = abs($thisTranQty);
        // dd($jobNum, $thisTranQty, $lotNum);
        $db_job_mtl = DB::connection('sqlsrv4')->table('Erp.JobHead as a')
        ->select(
            'a.JobNum',
            'a.PartNum', 
            DB::raw("c.OnHandQty - $qty AS StockStatus") 
        )
        ->join('JobHead as b', function ($join) {
            $join->on('a.JobNum', '=', 'b.JobNum') ;
        }) 
        ->leftJoin('Erp.PartBin as c', function ($join) {
            $join->on('a.PartNum', '=', 'c.PartNum')
            ->on('b.WhseReceipt_c', '=', 'c.WarehouseCode')
            ->on('b.BinReceipt_c', '=', 'c.BinNum') ;
        }) 
        ->where('a.JobNum', "$jobNum") 
        ->where('c.LotNum', "$lotNum") 
        ->get(); 

        $result = ['code' => 1, 'status' => 'Ok !'] ;
        $total_minus = 0 ; 
        if ($db_job_mtl->count() > 0) {
            foreach ($db_job_mtl AS $row) {
                $StockStatus = $row->StockStatus ;
                if ($StockStatus < 0) {
                    $total_minus = $total_minus + 1 ;
                }
            }
        } 
        // else {
        //     $result = ['code' => 0, 'status' => 'Warehouse belum disetup!'] ;
        // } 

        if ($total_minus > 0) {
            $result = ['code' => 0, 'status' => 'Part minus stock!'] ;
        } 
        return $result ;
    }

    public static function check_material_onhand_status($jobNum, $assemblySeq, $jobSeq, $thisTranQty, $lotNum)
    {
        $db_job_mtl = DB::connection('sqlsrv4')->table('Erp.JobMtl as a')
        ->select(
            'a.JobNum',
            'a.PartNum',
            'a.MtlSeq',
            'a.AssemblySeq',
            'a.RelatedOperation', 
            DB::raw("c.OnHandQty - (a.QtyPer * $thisTranQty) AS StockStatus")
        )
        ->join('JobHead as b', function ($join) {
            $join->on('a.JobNum', '=', 'b.JobNum') ;
        }) 
        ->join('Erp.PartBin as c', function ($join) {
            $join->on('a.PartNum', '=', 'c.PartNum')
            ->on('b.WhseSupply_c', '=', 'c.WarehouseCode')
            ->on('b.BinSupply_c', '=', 'c.BinNum') ;
        }) 
        ->where('a.JobNum', "$jobNum")
        ->where('a.AssemblySeq', $assemblySeq)
        ->where('a.RelatedOperation', $jobSeq)
        ->where('c.LotNum', "$lotNum")
        ->get();  

        $result = ['code' => 1, 'status' => 'Ok !'] ;
        $total_minus = 0 ; 
        if ($db_job_mtl->count() > 0) {
            foreach ($db_job_mtl AS $row) {
                $StockStatus = $row->StockStatus ;
                if ($StockStatus < 0) {
                    $total_minus = $total_minus + 1 ;
                }
            }
        } 
        // else {
        //     $result = ['code' => 0, 'status' => 'Warehouse belum disetup!'] ;
        // } 

        if ($total_minus > 0) {
           $result = ['code' => 0, 'status' => 'Beberapa komponen minus stock!'] ;
        } 
        return $result ;
    } 

    public static function get_detail_transaction_list($search, $PackSlip, $VendorNum)
    {
        $result = DB::connection('sqlsrv4')->table('RcvDtl')
        ->where('PackSlip', "$PackSlip")
        ->where('VendorNum', $VendorNum) ; 
        if (!empty($search)) {
            $result = $result->where(function ($query) use ($search) {
                $query->where('PartNum', 'LIKE', "%$search%");
            });
        }  
        return $result ;
    }

    public static function get_detail_po_list($PONum, $PartNum)
    {
        $result = DB::connection('sqlsrv4')->table('Erp.PODetail')
        ->where('PONUM', $PONum) ; 
        if (!empty($PartNum)) {
            $result = $result->where(function ($query) use ($PartNum) {
                $query->where('PartNum', 'LIKE', "%$PartNum%");
            });
        }  
        return $result ;
    }

    public static function get_rcv_dtl($PackSlip, $VendorNum)
    {
        $result = DB::connection('sqlsrv4')->table('Erp.RcvDtl AS a') 
        ->join('Erp.Part AS b', 'a.PartNum', '=', 'b.PartNum')
        ->join('Vendor AS c', 'a.VendorNum', '=', 'c.VendorNum')
        ->join('Erp.RcvHead AS d', function($join) {
            $join->on('a.VendorNum', '=', 'd.VendorNum');
            $join->on('a.PackSlip', '=', 'd.PackSlip');
        })
        ->join('Erp.Warehse AS e', 'a.WareHouseCode', '=', 'e.WarehouseCode') 
        ->where('a.PackSlip', "$PackSlip")
        ->where('a.VendorNum', $VendorNum) 
        ->select([
            'a.PackSlip',
            'a.VendorNum',
            'd.LegalNumber',
            'a.PartNum',
            'a.PartDescription',
            'b.ClassID',
            'b.ProdCode',
            'a.OurQty',
            'a.ArrivedDate',
            'c.Name AS VendorName',
            'c.VendorCode_c',
            'd.EntryPerson',
            'a.IUM',
            'a.WareHouseCode',
            DB::raw('e.Description AS WhseName'),
            'a.LotNum',
            'a.PackLine'
        ])
        ->get();
        return $result ;
    }

    public static function get_detail_issue_gr($PackSlip, $VendorNum)
    {
        $result = DB::connection('sqlsrv4')->table('RcvDtl')
        ->where('PackSlip', "$PackSlip")
        ->where('VendorNum', $VendorNum) 
        ->where('IssueMaterialStatus_c', 1) 
        ->select('PackSlip', 'VendorNum', DB::raw('SUM(OurQty) AS OurQty'), 'LotNum', 'WareHouseCode', 'PartNum', 'BinNum', 'AssemblySeq', 'JobSeq', 'JobNum')
        ->groupBy('PackSlip', 'VendorNum', 'LotNum', 'WareHouseCode', 'PartNum', 'BinNum', 'AssemblySeq', 'JobSeq', 'JobNum') ;
        return $result ;
    }

    public static function get_detail_receipt_gr($PackSlip, $VendorNum)
    {
        $result = DB::connection('sqlsrv4')->table('RcvDtl')
        ->where('PackSlip', "$PackSlip")
        ->where('VendorNum', $VendorNum) 
        ->where('JobReceiptStatus_c', 1) 
        ->select('PackSlip', 'VendorNum', DB::raw('SUM(OurQty) AS OurQty'), 'LotNum', 'WareHouseCode', 'PartNum', 'BinNum', 'AssemblySeq', 'JobSeq', 'JobNum')
        ->groupBy('PackSlip', 'VendorNum', 'LotNum', 'WareHouseCode', 'PartNum', 'BinNum', 'AssemblySeq', 'JobSeq', 'JobNum') ;
        return $result ;
    }
         

   

    public static function sum_detail_doc($vendor_num,$pack_slip){   
        $t = DB::connection('sqlsrv4')->table('Erp.RcvDtl')
        ->where('VendorNum', '=', $vendor_num)
        ->where('PackSlip', '=', "$pack_slip") 
        ->select(DB::raw("SUM(VendorQty) as result_sum"))
        ->get() ;    
        if($t->count() > 0){
        foreach($t as $h){
        $result = $h->result_sum ; }
        }else{ $result = 0 ; }

        return $result ; 
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
  
    public static function data_header($PackNum, $VendorNum)
    {
        $result = DB::connection('sqlsrv4')->table('RcvHead AS a')
        ->join('Vendor AS b', 'a.VendorNum', 'b.VendorNum')
            ->where('a.PackSlip', "$PackNum")
            ->where('a.VendorNum', $VendorNum) 
        ->get();
        return $result;
    }

    public static function data_detail($PackSlip, $PackLine, $VendorNum)
    {
        $result = DB::connection('sqlsrv4')->table('Erp.RcvDtl AS a') 
            ->where('a.PackSlip', "$PackSlip")
            ->where('a.PackLine', $PackLine)
            ->where('a.VendorNum', $VendorNum) 
        ->get();
        return $result;
    }

    public static function get_attachment_list($PackSlip, $VendorNum)
    {
        $result = DB::connection('sqlsrv4')->table('Ice.XFileAttch as A')
            ->select('A.RelatedToFile', 'A.Key1', 'B.*')
            ->leftJoin('Ice.XFileRef as B', 'B.XFileRefNum', '=', 'A.XFileRefNum')
            ->where('A.Key1', '=', "$VendorNum")
            ->where('A.Key3', '=', "$PackSlip")
            ->where('A.RelatedToFile', 'LIKE', '%Rcv%')
            ->get();
        return $result;
    }

    public static function uom_list($uom)
    {
        $result = DB::connection('sqlsrv4')->table('Erp.UOM AS a')
        ->select('a.UOMCode', DB::raw("CASE WHEN a.UOMCode = '$uom' THEN 'selected' ELSE '' END AS selected"))
        ->get();     
        return $result;
    }

    public static function wh_list($WHCode)
    {
        $result = DB::connection('sqlsrv4')->table('Erp.Warehse AS a')
        ->select('a.WarehouseCode', 'Description', DB::raw("CASE WHEN a.WarehouseCode = '$WHCode' THEN 'selected' ELSE '' END AS selected"))
        ->get();     
        return $result;
    }


    public static function detail_list_data($vendor_num,$pack_slip){  
        return DB::connection('sqlsrv4')->table('Erp.RcvDtl AS a')
        ->where('a.VendorNum', '=', $vendor_num)
        ->where('a.PackSlip', '=', "$pack_slip") 
        ->select('a.*', DB::raw("NULL as PostCategoryID"),  DB::raw("NULL AS Convertion"), DB::raw("NULL AS MLedgerID"))
        ->orderBy('a.PackLine','ASC')
        ->get() ; 
    }

    public static function load_data_print_page($vendor_num,$pack_slip,$Status,$print_option,$offset,$limit){ 
        $query = DB::connection('sqlsrv4')->table('Erp.RcvDtl AS a') 
        ->where('a.VendorNum', '=', $vendor_num)
        ->where('a.PackSlip', '=', "$pack_slip") 
        ->select('a.*', DB::raw("NULL AS qty_bk_order"), DB::raw("NULL as PostCategoryID"))
        ->offset($offset)
        ->limit($limit)
        ->orderBy('a.PackLine','ASC')
        ->get() ;
         
        $data = array();
        $no = $offset + 1 ; 
            
        foreach($query as $l) { 
        $dtDelivery = AppModel::local_date_formate_name(substr($l->ReceiptDate,0,10)) ;
        $ItemName = $l->PartDescription ;
        $ItemNum_Req = $l->PartNum ;
        if($Status==1){$StatusX = '- Draft';}else{$StatusX = '';} 
        if($print_option!=0){
        $Product = $ItemNum_Req.' - '.$ItemName ;
        }else{
        $Product = $ItemName ;   
        }  
            
        
        $line_x = $no.'^'.$ItemNum_Req.'^'.$dtDelivery.'^'.
        number_format(round($l->VendorQty)).'^'.$l->PUM.'^'.
        number_format(round($l->VendorUnitCost,2),2).'^'.number_format(0,2).'^'.
        number_format(round($l->VendorQty*$l->VendorUnitCost,2),2).'^'.number_format(round($l->OurQty)).'^'.
        $l->IUM.'^'.$l->PostCategoryID.'^'.$ItemName.'^'.number_format(round($l->VendorUnitCost,0)).'^* PO : '.(int) $l->PONum."/".$l->POLine ; ;
        
        $no++;
        $data[] = explode('^', chop($line_x));
        }
        return $data;
      }
      public static function save_tag($packSlip,$packLine,$poNum,$poLine,$seqnum,$inputOurQty, $vendorNum,$lotTag,$partNum,$partName,$warehouseCode,$binNum) 
      { 
        $result = DB::table('t510_Receipt_Tag')
            ->insert([
                'pack_slip' => $packSlip,
                'pack_line' => $packLine,
                'po_num' => $poNum,
                'po_line' => $poLine,
                'po_rel' => 1, // Assuming po_rel is always 1, adjust if needed
                'seq_num' => $seqnum,
                'qty' => $inputOurQty,
                'vendor_num' => $vendorNum,
                'lotnum' => $lotTag,
                'item_no' => $partNum,
                'item_name' => $partName,
                'warehouse_code' => $warehouseCode,
                'BinNum' => $binNum,
                'created_at' => now(),
                'created_by' => Auth::user()->username,
                'is_deleted' => 0
            ]);
        return $result;
      }
      public static function delete_tag($packSlip,$packLine,$poNum,$poLine)
      {
        $result = DB::table('t510_Receipt_Tag')
            ->where('pack_slip', $packSlip)
            ->where('pack_line', $packLine) // Assuming pack_line is the inputOurQty, adjust if needed
            ->where('po_num', $poNum)
            ->where('po_line', $poLine)
           
            ->update([
                'is_deleted' => 1,
                'deleted_at' => now(),
                'deleted_by' => Auth::user()->username
            ]);
        return $result;
      }
      public static function get_tag_list($packSlip,$packLine,$poNum,$poLine, $partNum, $warehouseCode, $binNum,$lotTag,$seqnum)
      {
        $result = DB::table('t510_Receipt_Tag')
            ->where('pack_slip', $packSlip)
            ->where('po_num', $poNum)
            ->where('po_line', $poLine)
            ->where('item_no', $partNum)
            ->where('warehouse_code', $warehouseCode)
            ->where('BinNum', $binNum)
            ->where('lotnum', $lotTag)
            ->where('seq_num', $seqnum)
            ->where('is_deleted', 0)
            ->get();
        return $result;
      }
      public static function get_data_part_ps($partnum){
        $result = DB::table('partps AS a')
            ->where('a.partnum_ps', $partnum)
            ->select('a.partnum_ps', 'a.mtlpart_ps', 'a.qtyper_ps', 'a.warehouse_ps', 'a.binnum_ps', 'a.lotnum_ps','a.ium_ps')
            ->get();
        return $result;
      }
      public static function vendor_shp_update($id, $status1, $status2)
    {
        $head = DB::connection('vendor-app-epicor')
        ->table('VendorShpHead')
        ->where('id', $id)
        ->update(['Status' => $status1]);
        if ($head) {
            $dtl = DB::connection('vendor-app-epicor')
            ->table('VendorShpDtl')
            ->where('VendorShpHeadID', $id)
            ->update(['Status' => $status2]);
            if ($dtl) {
                return true;
            } else {
               return false;
            }
        } else {
           return false;
        }
    }
}
