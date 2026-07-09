<?php

namespace App\Http\Controllers;

use App\Models\EntertainReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use \PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class SalesReportEntertainController extends Controller
{
   
    protected function normalizeMembers(array $arr): array
    {
        $out = [];
        $seen = [];
        foreach ($arr as $v) {
            if (is_array($v)) {
                $v = $v['SAIMember'] ?? '';
            }
            $v = trim((string) $v);
            if ($v === '') continue;
            $k = mb_strtolower($v);
            if (!isset($seen[$k])) {
                $seen[$k] = true;
                $out[] = $v;
            }
        }
        return $out;
    }

    public function index()
    {
        $my_id = Auth::user()->id;
        $segment_number = env('SEGMENT_NUM');
        $uri = explode("/", url()->current());
        $menu = count($uri) <= $segment_number ? $this->menu($my_id, 'home') : $this->menu($my_id, $uri[$segment_number]);

        $data['head_title']   = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'];
        $data['menu_level_2'] = $menu['menu_level_2'];
        $data['menu_level_3'] = $menu['menu_level_3'];
        $data['menu_level_4'] = $menu['menu_level_4'];

        $data['header_data'] = EntertainReport::headers()->get();
        return view('sales_report_entertain.sales_report_entertain_index', $data);
    }

    public function store(Request $request)
    {
       
        $validated = $request->validate([
            'Date'        => ['required', 'date'],
            'Customer'    => ['required', 'string', 'max:50'],
            'Category'    => ['required', 'string', 'max:50'],
            'NumCA'       => ['required', 'string', 'max:50'],
            'Description' => ['nullable', 'string', 'max:500'],
            'ExternalMembers'   => ['sometimes', 'array'],
            'ExternalMembers.*' => ['string', 'max:50'],
            'InternalMembers'   => ['sometimes', 'array'],
        ]);

       
        $intFlat = $this->normalizeMembers((array) $request->input('InternalMembers', []));
        validator(['InternalMembers' => $intFlat], [
            'InternalMembers.*' => ['string', 'max:100'],
        ])->validate();

        $id = EntertainReport::createHeader([
            'Date'            => $validated['Date'],
            'Customer'        => $validated['Customer'],
            'Category'        => $validated['Category'],
            'NumCA'           => $validated['NumCA'],
            'Description'     => $validated['Description'] ?? null,
            'ExternalMembers' => $request->input('ExternalMembers', []),
            'InternalMembers' => $intFlat,
        ]);

        return response()->json(['success' => true, 'data' => ['SysID' => $id]]);
    }

    public function update_header(Request $request, int $SysID)
    {
        
        $validated = $request->validate([
            'Date'        => ['required', 'date'],
            'Customer'    => ['required', 'string', 'max:50'],
            'Category'    => ['required', 'string', 'max:50'],
            'NumCA'       => ['required', 'string', 'max:50'],
            'Description' => ['nullable', 'string', 'max:500'],
            'ExternalMembers'   => ['sometimes', 'array'],
            'ExternalMembers.*' => ['string', 'max:50'],
            'InternalMembers'   => ['sometimes', 'array'],
        ]);

        $payload = [
            'Date'        => $validated['Date'],
            'Customer'    => $validated['Customer'],
            'Category'    => $validated['Category'],
            'NumCA'       => $validated['NumCA'],
            'Description' => $validated['Description'] ?? null,
        ];

        if ($request->has('ExternalMembers')) {
            $payload['ExternalMembers'] = (array) $request->input('ExternalMembers');
        }
        if ($request->has('InternalMembers')) {
            $intFlat = $this->normalizeMembers((array) $request->input('InternalMembers', []));
            validator(['InternalMembers' => $intFlat], [
                'InternalMembers.*' => ['string', 'max:100'],
            ])->validate();
            $payload['InternalMembers'] = $intFlat;
        }

        EntertainReport::updateHeader($SysID, $payload);
        $report = EntertainReport::headerById($SysID);
        return response()->json(['success' => true, 'data' => $report]);
    }

    public function data_list_entertain_report_header(Request $request)
    {
        $keyword = trim((string)$request->input('keyword', ''));
        $headers = EntertainReport::headers($keyword)->get();

        $data = $headers->map(function ($h) {
            return [
                'SysID'       => $h->SysID,
                'Date'        => $h->Date,
                'Customer'    => $h->Customer,
                'Category'    => $h->Category,
                'NumCA'       => $h->NumCA,
                'CostCenter'  => $h->CostCenter ?: '-',
                'TotalAmount' => $h->TotalAmount,
                'Description' => $h->Description,
            ];
        })->values();

        return response()->json($data);
    }

    public function add_report(Request $request)
    {
        $customers = EntertainReport::getCustomer();
        $header = null;
        return view('sales_report_entertain.form_header', compact('customers', 'header'));
    }

    public function form_detail(Request $request)
    {
        $SysID       = (int) $request->input('SysID');
        $detailSysID = (int) $request->input('detailSysID');

        $detailCompat = null;
        if ($detailSysID) {
            $item = EntertainReport::itemById($detailSysID);
            if ($item) {
                $detailCompat = (object) [
                    'SysID'          => $item->SysID,
                    'Item'           => $item->Item,
                    'RestaurantShop' => $item->RestaurantShop,
                    'Amount'         => $item->Amount,
                ];
            }
        }

        return view('sales_report_entertain.form_detail', [
            'SysID'  => $SysID,
            'detail' => $detailCompat,
        ]);
    }

    public function edit_report_header(Request $request)
    {
        $SysID  = (int) $request->input('SysID');
        $header = EntertainReport::headerById($SysID);
        if (!$header) return response('Header not found', 404);

        $extMembers = EntertainReport::externalMembersByHeader($SysID)->pluck('CustomerMember');
        $intMembers = EntertainReport::internalMembersByHeader($SysID)->get();

        $customers = EntertainReport::getCustomer();

        return view('sales_report_entertain.form_header', compact(
            'SysID',
            'header',
            'extMembers',
            'intMembers',
            'customers'
        ));
    }

    public function delete_report(Request $request)
    {
        $SysID = (int) $request->input('SysID');
        $ok    = EntertainReport::deleteHeader($SysID);

        return response()->json(['success' => (bool)$ok, 'message' => $ok ? 'OK' : 'Failed to delete the report.']);
    }

    public function get_details(Request $request)
    {
        $idReport = (int) $request->input('ID_Report');
        if (!$idReport) {
            return response()->json(['success' => false, 'message' => 'ID_Report is not available'], 422);
        }

        $details = EntertainReport::itemDetails($idReport)
            ->get()
            ->map(fn($r) => [
                'SysID'          => (int) $r->SysID,
                'Item'           => $r->Item,
                'RestaurantShop' => $r->RestaurantShop,
                'Amount'         => (float) $r->Amount,
            ])->values();

        $members = EntertainReport::headerMembers($idReport);

        return response()->json(['success' => true, 'data' => ['items' => $details, 'members' => $members]]);
    }

    public function store_detail(Request $request)
{
    $headerId = (int) $request->input('ID_Report');
    if (!$headerId) {
        return response()->json(['success' => false, 'message' => 'ID_Report is requied'], 422);
    }

  
    $items   = (array) $request->input('Item', []);
    $shops   = (array) $request->input('RestaurantShop', []);
    $amounts = (array) $request->input('Amount', []);

    $rowsItem = [];
    $n = max(count($items), count($shops), count($amounts));
    for ($i = 0; $i < $n; $i++) {
        $item = trim((string)($items[$i] ?? ''));
        $shop = trim((string)($shops[$i] ?? ''));
        $amt  = (float)($amounts[$i] ?? 0);
        if ($item === '' || $amt < 0) continue;
        if ($shop === '') $shop = '-';
        $rowsItem[] = ['Item' => $item, 'RestaurantShop' => $shop, 'Amount' => $amt];
    }
    if (!$rowsItem) {
        return response()->json(['success' => false, 'message' => 'All lines are empty/invalid'], 422);
    }
    EntertainReport::createItems($headerId, $rowsItem);

    
    $custMembers = array_values(array_filter(array_map('trim', (array) $request->input('CustomerMember', [])), fn($v) => $v !== ''));
    $saiMembers  = $this->normalizeMembers((array) $request->input('SAIMember', []));

    
    $cc0 = trim((string) ($request->input('CostCenter.0') ?? ''));          
    if ($cc0 === '' && !empty($saiMembers)) {
        $cc0 = (string) (EntertainReport::getCostCenterByName($saiMembers[0]) ?? ''); 
    }

    if ($custMembers || $saiMembers) {
       
        EntertainReport::createMembers($headerId, $custMembers, $saiMembers, $cc0 ?: null); 
    }

    $newTotal = (float) (EntertainReport::headerById($headerId)->TotalAmount ?? 0);
    return response()->json([
        'success' => true,
        'data' => [
            'items'   => EntertainReport::itemDetails($headerId)->get(),
            'members' => EntertainReport::headerMembers($headerId),
            'total'   => $newTotal
        ]
    ]);
}

    public function update_detail(Request $request, $SysID)
    {
        $exists = DB::table('EntertainReportItemDtl')->where('SysID', (int)$SysID)->exists();
        if (!$exists) return response()->json(['success' => false, 'message' => 'Details not found'], 404);

        $headerId = (int) DB::table('EntertainReportItemDtl')->where('SysID', (int)$SysID)->value('HeaderID');

        $flat = [
            'Item'           => is_array($request->input('Item')) ? ($request->input('Item')[0] ?? null) : $request->input('Item'),
            'RestaurantShop' => is_array($request->input('RestaurantShop')) ? ($request->input('RestaurantShop')[0] ?? null) : $request->input('RestaurantShop'),
            'Amount'         => is_array($request->input('Amount')) ? ($request->input('Amount')[0] ?? null) : $request->input('Amount'),
            'CustomerMember' => is_array($request->input('CustomerMember')) ? ($request->input('CustomerMember')[0] ?? null) : $request->input('CustomerMember'),
            'SAIMember'      => is_array($request->input('SAIMember')) ? ($request->input('SAIMember')[0] ?? null) : $request->input('SAIMember'),
        ];

        $validated = validator($flat, [
            'Item'           => 'required|string|max:100',
            'RestaurantShop' => 'required|string|max:100',
            'Amount'         => 'required|numeric',
            'CustomerMember' => 'nullable|string|max:100',
            'SAIMember'      => 'nullable|string|max:100',
        ])->validate();

        EntertainReport::updateItem((int)$SysID, [
            'Item'           => $validated['Item'],
            'RestaurantShop' => $validated['RestaurantShop'],
            'Amount'         => (float)$validated['Amount'],
        ]);

        if (!empty($validated['CustomerMember'])) {
            DB::table('EntertainReportExternalMember')->updateOrInsert(
                ['HeaderID' => $headerId, 'CustomerMember' => $validated['CustomerMember']],
                ['CustomerMember' => $validated['CustomerMember']]
            );
        }

        if (!empty($validated['SAIMember'])) {
    $hasAnyCC = DB::table('EntertainReportInternalMember')
        ->where('HeaderID', $headerId)
        ->whereRaw("NULLIF(LTRIM(RTRIM(CostCenter)),'') IS NOT NULL")
        ->exists();

    $cc = $hasAnyCC ? null : EntertainReport::getCostCenterByName($validated['SAIMember']);

    DB::table('EntertainReportInternalMember')->updateOrInsert(
        ['HeaderID' => $headerId, 'SAIMember' => $validated['SAIMember']],
        ['SAIMember' => $validated['SAIMember'], 'CostCenter' => $cc]
    );

    if ($cc !== null) {
        DB::table('EntertainReportInternalMember')
            ->where('HeaderID', $headerId)
            ->where('SAIMember', '!=', $validated['SAIMember'])
            ->update(['CostCenter' => null]);
    }
}

        EntertainReport::recalcAndGetTotal($headerId);
        $newTotal = (float) (EntertainReport::headerById($headerId)->TotalAmount ?? 0);

        return response()->json([
            'success' => true,
            'data' => [
                'rows' => [[
                    'Item'           => $validated['Item'],
                    'RestaurantShop' => $validated['RestaurantShop'],
                    'Amount'         => (float)$validated['Amount'],
                    'CustomerMember' => $validated['CustomerMember'],
                    'SAIMember'      => $validated['SAIMember'],
                ]],
                'total' => $newTotal
            ]
        ]);
    }

    public function delete_detail(Request $request)
{
    $detailId = (int) $request->input('SysID');
    if (!$detailId) {
        return response()->json(['success' => false, 'message' => 'SysID detail is required'], 422);
    }

   
    $row = DB::table('EntertainReportItemDtl')
        ->select('HeaderID')
        ->where('SysID', $detailId)
        ->first();

    if (!$row) {
        return response()->json(['success' => false, 'message' => 'Details bot found'], 404);
    }

    $headerId = (int) $row->HeaderID;

    DB::beginTransaction();
    try {
        
        DB::table('EntertainReportItemDtl')->where('SysID', $detailId)->delete();

    
        DB::table('EntertainReportExternalMember')->where('HeaderID', $headerId)->delete();
        DB::table('EntertainReportInternalMember')->where('HeaderID', $headerId)->delete();

      
        $total = (float) DB::table('EntertainReportItemDtl')
            ->where('HeaderID', $headerId)
            ->sum('Amount');

        DB::table('EntertainReportHeader')
            ->where('SysID', $headerId)
            ->update(['TotalAmount' => $total]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Detail & members deleted successfully',
            'total'   => $total, 
        ]);
    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete details: ' . $e->getMessage(),
        ], 500);
    }
}


public function import(Request $request)
{
    $file = $request->file('file');
    $data = \Maatwebsite\Excel\Facades\Excel::toArray([], $file);

    $normalizeDate = function ($value) {
        if ($value === null || $value === '') return '';
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        }
        $ts = strtotime((string)$value);
        return $ts ? date('Y-m-d', $ts) : '';
    };

    $makeKey = function ($date, $customer) use ($normalizeDate) {
        $d = $normalizeDate($date);
        $c = mb_strtoupper(trim((string)$customer));
        return $d . '|' . $c;
    };

    $fmtPairs = function (array $pairs) {
        return implode('; ', array_map(fn($p) => "{$p['date']} | {$p['cust']}", $pairs));
    };

   
    $IDX = [
        'date'        => 0,
        'customer'    => 1,
        'item'        => 2,
        'shop'        => 3,
        'amount'      => 4,
        'cust_member' => 5,
        'sai_member'  => 6,
        'cost_center' => 7,
    ];

   
    $HDX = [
        'date'        => 0,
        'customer'    => 1,
        'category'    => 2,
        'num_ca'      => 3,
        'cost_center' => 4, 
        'description' => 5,
    ];

    $splitList = function (?string $s): array {
        $s = trim((string)$s);
        if ($s === '') return [];
        $arr = preg_split('/[;,]/', $s);
        $arr = array_map(fn($v) => trim((string)$v), $arr);
        return array_values(array_filter($arr, fn($v) => $v !== ''));
    };

    $normAmount = function ($raw) {
        if (is_string($raw)) $raw = str_replace(',', '.', $raw);
        $amt = (float)($raw ?? 0);
        return $amt < 0 ? 0 : $amt;
    };

    DB::beginTransaction();
    try {
        $headerMap = [];
        $affected  = [];

        
        if (count($data) === 2) {
            $headerSheet = $data[0];
            $detailSheet = $data[1];

            
            $seenInFile = [];
            $dupInFile  = [];
            foreach ($headerSheet as $idx => $row) {
                if ($idx === 0 || empty(array_filter($row))) continue;

                $date = $normalizeDate($row[$HDX['date']] ?? '');
                $cust = trim((string)($row[$HDX['customer']] ?? ''));
                $cat  = trim((string)($row[$HDX['category']] ?? ''));
                $num  = trim((string)($row[$HDX['num_ca']] ?? ''));

              
                if ($date === '' || $cust === '' || $cat === '' || $num === '') {
                    throw new \Exception("Header: Date/Customer/Category/Num CA is empty on line".($idx+1));
                }

                $key = $makeKey($date, $cust);
                if (isset($seenInFile[$key])) {
                    $dupInFile[] = ['date'=>$date,'cust'=>$cust];
                } else {
                    $seenInFile[$key] = true;
                }
            }
            if ($dupInFile) {
                throw new \Exception('Duplicate headers in file: '.$fmtPairs($dupInFile).'. Clean Up the file first.');
            }

           
            $conflicts = [];
            foreach ($headerSheet as $idx => $row) {
                if ($idx === 0 || empty(array_filter($row))) continue;
                $date = $normalizeDate($row[$HDX['date']] ?? '');
                $cust = trim((string)($row[$HDX['customer']] ?? ''));
                $exists = DB::table('EntertainReportHeader')
                    ->where('Date', $date)
                    ->whereRaw('UPPER(Customer) = ?', [mb_strtoupper($cust)])
                    ->exists();
                if ($exists) {
                    $conflicts[$makeKey($date, $cust)] = ['date'=>$date,'cust'=>$cust];
                }
            }
            if ($conflicts) {
                throw new \Exception('This data already exists for: '.$fmtPairs(array_values($conflicts)).'. please fill in the form.');
            }

           
            $headerCCInputByKey = []; 
            foreach ($headerSheet as $idx => $row) {
                if ($idx === 0 || empty(array_filter($row))) continue;
                $date = $normalizeDate($row[$HDX['date']] ?? '');
                $cust = trim((string)($row[$HDX['customer']] ?? ''));
                $cat  = trim((string)($row[$HDX['category']] ?? ''));
                $num  = trim((string)($row[$HDX['num_ca']] ?? ''));
                $cc   = trim((string)($row[$HDX['cost_center']] ?? '')); 
                $desc = trim((string)($row[$HDX['description']] ?? ''));

                $hid = EntertainReport::createHeader([
                    'Date'        => $date,
                    'Customer'    => $cust,
                    'Category'    => $cat,
                    'NumCA'       => $num,      
                    'Description' => $desc ?: null,
                ]);
                $headerMap[$makeKey($date, $cust)] = $hid;
                $headerCCInputByKey[$makeKey($date, $cust)] = $cc;
            }

          
            $itemsByHeader   = [];
            $custByHeader    = [];
            $saiByHeader     = [];
            $firstCcByHeader = []; 

            foreach ($detailSheet as $idx => $row) {
                if ($idx === 0 || empty(array_filter($row))) continue;

                $date = $normalizeDate($row[$IDX['date']] ?? '');
                $cust = trim((string)($row[$IDX['customer']] ?? ''));
                $key  = $makeKey($date, $cust);
                if (!isset($headerMap[$key])) {
                    throw new \Exception("Detail row no-".($idx+1)." has no header (Date+Customer not found)");
                }
                $hid = $headerMap[$key];

                $ccDetail = trim((string)($row[$IDX['cost_center']] ?? ''));
                $ccHdrInp = trim((string)($headerCCInputByKey[$key] ?? ''));

               
                if ($ccDetail !== '' && $ccHdrInp !== '' &&
                    mb_strtoupper($ccDetail) !== mb_strtoupper($ccHdrInp)) {
                    throw new \Exception("Cost Center detail (row ".($idx+1).") is not equal to header for {$date} | {$cust}.");
                }

                if ($ccDetail !== '' && !isset($firstCcByHeader[$hid])) {
                    $firstCcByHeader[$hid] = $ccDetail; 
                }

                $item = trim((string)($row[$IDX['item']] ?? ''));
                $shop = trim((string)($row[$IDX['shop']] ?? ''));
                $amt  = $normAmount($row[$IDX['amount']] ?? 0);
                if ($item !== '') {
                    if ($shop === '') $shop = '-';
                    $itemsByHeader[$hid][] = [
                        'Item'           => $item,
                        'RestaurantShop' => $shop,
                        'Amount'         => $amt,
                    ];
                }

                foreach ($splitList($row[$IDX['cust_member']] ?? '') as $cm) {
                    $custByHeader[$hid][] = $cm;
                }
                foreach ($splitList($row[$IDX['sai_member']] ?? '') as $sm) {
                    $saiByHeader[$hid][] = $sm;
                }
            }

            foreach ($headerMap as $key => $hid) {
                if (!empty($itemsByHeader[$hid])) {
                    EntertainReport::createItems($hid, $itemsByHeader[$hid]);
                }
                $cm = $custByHeader[$hid] ?? [];
                $sm = $saiByHeader[$hid]  ?? [];
                $firstCc = $firstCcByHeader[$hid] ?? null;
                if ($cm || $sm) {
                    EntertainReport::createMembers($hid, $cm, $sm, $firstCc);
                }
                $affected[$hid] = true;
            }

       
        } elseif (count($data) === 1) {
            $detailSheet = $data[0];

            
            $keysInFile = [];
            foreach ($detailSheet as $idx => $row) {
                if ($idx === 0 || empty(array_filter($row))) continue;
                $date = $normalizeDate($row[$IDX['date']] ?? '');
                $cust = trim((string)($row[$IDX['customer']] ?? ''));
                if ($date === '' || $cust === '') {
                    throw new \Exception("Detail: Date/Customer is empty ont the row".($idx+1));
                }
                $keysInFile[$makeKey($date, $cust)] = ['date'=>$date,'cust'=>$cust];
            }

         
            $missing    = [];
            $ambiguous  = [];
            $headerMap  = [];
            foreach ($keysInFile as $key => $pair) {
                $rows = DB::table('EntertainReportHeader')
                    ->select('SysID')
                    ->where('Date', $pair['date'])
                    ->whereRaw('UPPER(Customer) = ?', [mb_strtoupper($pair['cust'])])
                    ->get();

                $count = $rows->count();
                if ($count === 0) {
                    $missing[] = $pair;
                } elseif ($count > 1) {
                    $ambiguous[] = $pair;
                } else {
                    $hid = (int)$rows->first()->SysID;

                    
                    $existCc = DB::table('EntertainReportInternalMember')
                        ->where('HeaderID', $hid)
                        ->whereRaw("NULLIF(LTRIM(RTRIM(CostCenter)),'') IS NOT NULL")
                        ->orderBy('SysID')
                        ->value('CostCenter');

                    $headerMap[$key] = ['id'=>$hid, 'exist_cc'=> (string)($existCc ?? '')];
                }
            }
            if ($missing) {
                throw new \Exception('Header bot found for: '.$fmtPairs($missing).'.');
            }
            if ($ambiguous) {
                throw new \Exception('this data is duplicate for: '.$fmtPairs($ambiguous).'. please fill in using the form.');
            }

           
            $itemsByHeader   = [];
            $custByHeader    = [];
            $saiByHeader     = [];
            $firstCcByHeader = [];

            foreach ($detailSheet as $idx => $row) {
                if ($idx === 0 || empty(array_filter($row))) continue;

                $date = $normalizeDate($row[$IDX['date']] ?? '');
                $cust = trim((string)($row[$IDX['customer']] ?? ''));
                $key  = $makeKey($date, $cust);

                $hid   = $headerMap[$key]['id'];
                $exist = trim((string)$headerMap[$key]['exist_cc']);

                $ccDetail = trim((string)($row[$IDX['cost_center']] ?? ''));

                
                if ($exist !== '' && $ccDetail !== '' &&
                    mb_strtoupper($ccDetail) !== mb_strtoupper($exist)) {
                    throw new \Exception("Cost Center detail (row ".($idx+1).") ≠ CC header (internal) for {$date} | {$cust}.");
                }

                
                if ($exist === '' && $ccDetail !== '' && !isset($firstCcByHeader[$hid])) {
                    $firstCcByHeader[$hid] = $ccDetail;
                }

                $item = trim((string)($row[$IDX['item']] ?? ''));
                $shop = trim((string)($row[$IDX['shop']] ?? ''));
                $amt  = $normAmount($row[$IDX['amount']] ?? 0);
                if ($item !== '') {
                    if ($shop === '') $shop = '-';
                    $itemsByHeader[$hid][] = [
                        'Item'           => $item,
                        'RestaurantShop' => $shop,
                        'Amount'         => $amt,
                    ];
                }

                foreach ($splitList($row[$IDX['cust_member']] ?? '') as $cm) {
                    $custByHeader[$hid][] = $cm;
                }
                foreach ($splitList($row[$IDX['sai_member']] ?? '') as $sm) {
                    $saiByHeader[$hid][] = $sm;
                }
            }

            foreach ($headerMap as $key => $info) {
                $hid = $info['id'];
                if (!empty($itemsByHeader[$hid])) {
                    EntertainReport::createItems($hid, $itemsByHeader[$hid]);
                }
                $cm = $custByHeader[$hid] ?? [];
                $sm = $saiByHeader[$hid]  ?? [];
                $firstCc = $firstCcByHeader[$hid] ?? null;
                if ($cm || $sm) {
                    EntertainReport::createMembers($hid, $cm, $sm, $firstCc);
                }
                $affected[$hid] = true;
            }

        } else {
            throw new \Exception("Invalid file format. Use 1 sheet (Detail only) or 2 sheet (Header & Detail).");
        }

        foreach (array_keys($affected) as $hid) {
            EntertainReport::recalcAndGetTotal($hid);
        }

        DB::commit();
        return back()->with('success', 'Import Success!');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}




    public function export_report(int $SysID)
    {
        $header  = \App\Models\EntertainReport::headerById($SysID);
        abort_if(!$header, 404, 'Header not found.');

        $details = \App\Models\EntertainReport::itemDetails($SysID)->orderBy('SysID')->get();
        $members = \App\Models\EntertainReport::headerMembers($SysID);

        $toArr       = fn($v) => (is_object($v) && method_exists($v, 'toArray')) ? $v->toArray() : (array)$v;
        $custMembers = $toArr($members['CustomerMember'] ?? []);
        $saiMembers  = $toArr($members['SAIMember']      ?? []);
        $totalCust   = count($custMembers);
        $totalSai    = count($saiMembers);


        $FILENAME = 'Export_Report.xlsx';
        $candidates = [
            resource_path('views/sales_report_entertain/template export/' . $FILENAME),
            storage_path('app/templates/' . $FILENAME),
            public_path($FILENAME),
            base_path($FILENAME),
        ];
        $tplPath = null;
        foreach ($candidates as $p) if (is_file($p)) {
            $tplPath = $p;
            break;
        }
        if (!$tplPath) {
            return response()->json([
                'success' => false,
                'message' => 'Excel template not found. Place it in resources/views/sales_report_entertain/template export/Export_Report.xlsx'
            ], 500);
        }

        $ss    = \PhpOffice\PhpSpreadsheet\IOFactory::load($tplPath);
        $sheet = $ss->getSheetByName('Sheet1') ?: $ss->getActiveSheet();

        $find = function (\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws, string $token, int $maxR = 800, int $maxC = 150) {
            $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($maxC);
            foreach ($ws->getRowIterator(1, $maxR) as $row) {
                $r = $row->getRowIndex();
                foreach ($ws->getColumnIterator('A', $lastCol) as $col) {
                    $c = $col->getColumnIndex();
                    $addr = $c . $r;
                    $val = (string)$ws->getCell($addr)->getValue();
                    if ($val !== '' && strpos($val, $token) !== false) return $addr;
                }
            }
            return null;
        };
        $rowOf = fn(string $addr) => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($addr)[1];

        $isInRange = static function (string $coordinate, string $range): bool {
            if (method_exists(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::class, 'coordinateIsInRange')) {
                return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateIsInRange($coordinate, $range);
            }
            [$colL, $row] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($coordinate);
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($colL);
            [$start, $end] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::rangeBoundaries($range);
            return $col >= $start[0] && $col <= $end[0] && $row >= $start[1] && $row <= $end[1];
        };

        $getWriteColFromAnchor = function (\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws, string $addr) use ($isInRange) {
            $coord = $ws->getCell($addr)->getCoordinate();
            foreach ($ws->getMergeCells() as $rng) {
                if ($isInRange($coord, $rng)) {
                    [$tl,] = explode(':', $rng);
                    [$colLeft,] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($tl);
                    return $colLeft;
                }
            }
            [$col,] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($addr);
            return $col;
        };


        $getMergeSpanCols = function (\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws, string $addr) use ($isInRange) {
            $coord = $ws->getCell($addr)->getCoordinate();
            foreach ($ws->getMergeCells() as $rng) {
                if ($isInRange($coord, $rng)) {
                    [$tl, $br] = explode(':', $rng);
                    [$cL,] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($tl);
                    [$cR,] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($br);
                    return [$cL, $cR];
                }
            }
            [$c,] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($addr);
            return [$c, $c];
        };

        $topLeftOf = function (\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws, string $addr) use ($isInRange, $getWriteColFromAnchor) {
            $col   = $getWriteColFromAnchor($ws, $addr);
            $coord = $ws->getCell($addr)->getCoordinate();
            $row   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($addr)[1];
            foreach ($ws->getMergeCells() as $rng) {
                if ($isInRange($coord, $rng)) {
                    [$tl,] = explode(':', $rng);
                    [, $row] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($tl);
                    break;
                }
            }
            return [$col, $row];
        };

        $clearTokenCell = function ($ws, string $addr) {
            $cur = (string)$ws->getCell($addr)->getValue();
            if ($cur !== '') {
                $cur = preg_replace('/\[\[[^\]]+\]\]/', '', $cur);
                $ws->setCellValue($addr, trim($cur) === '' ? null : $cur);
            }
        };
        $dupStyle = function ($ws, string $from, string $range) {
            $ws->duplicateStyle($ws->getStyle($from), $range);
        };
        $replaceAll = function ($ws, array $map, int $maxR = 800, int $maxC = 150) {
            if (!$map) return;
            $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($maxC);
            foreach ($ws->getRowIterator(1, $maxR) as $row) {
                $r = $row->getRowIndex();
                foreach ($ws->getColumnIterator('A', $lastCol) as $col) {
                    $c = $col->getColumnIndex();
                    $addr = $c . $r;
                    $val = (string)$ws->getCell($addr)->getValue();
                    if ($val === '') continue;
                    $new = str_replace(array_keys($map), array_values($map), $val);
                    if ($new !== $val) $ws->setCellValue($addr, $new);
                }
            }
        };
        $countReserved = function ($ws, string $col, int $row, int $limit = 50) {
            $n = 0;
            for ($i = 0; $i < $limit; $i++) {
                $style = $ws->getStyle($col . ($row + $i))->getBorders()->getBottom()->getBorderStyle();
                if ($style !== \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED) break;
                $n++;
            }
            return max(1, $n);
        };
        $ensureMerged = function (\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws, string $range) {
            foreach ($ws->getMergeCells() as $r) if ($r === $range) return;
            $ws->mergeCells($range);
        };


        $applyIdrAccounting = function (\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws, string $range, bool $bold = false) {
          
            $ws->getStyle($range)->getNumberFormat()->setFormatCode(
                '_-"IDR"* #,##0;_-"IDR"* -#,##0;_-"IDR"* "-"??;_-@'
            );
            $ws->getStyle($range)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                ->setWrapText(false);

            if ($bold) {
                $ws->getStyle($range)->getFont()->setBold(true);
            }
        };


        $replaceAll($sheet, [
            '[[DATE]]'           => (string)$header->Date,
            '[[CUST_NAME]]'      => (string)$header->Customer,
            '[[DESCRIPTION]]'    => (string)($header->Description ?? ''),
            '[[TOTAL_CUST_MEM]]' => (string)$totalCust,
            '[[TOTAL_SAI_MEM]]'  => (string)$totalSai,
        ]);


        $aItem = $find($sheet, '[[ITEM]]');
        $aShop = $find($sheet, '[[SHOP]]');
        $aAmt  = $find($sheet, '[[AMT]]');
        $aTot  = $find($sheet, '[[TOTAL_AMT]]');

        if ($aItem && $aShop && $aAmt && $aTot) {
            $cItem    = $getWriteColFromAnchor($sheet, $aItem);
            $cShop    = $getWriteColFromAnchor($sheet, $aShop);
            $cAmt     = $getWriteColFromAnchor($sheet, $aAmt);
            $startRow = $rowOf($aItem);
            $endRow0  = $rowOf($aTot) - 1;
            $reserved = max(0, $endRow0 - $startRow + 1);

            $N     = max(0, $details->count());
            $extra = max(0, $N - $reserved);
            if ($extra > 0) {
                $sheet->insertNewRowBefore($endRow0 + 1, $extra);
                $endRow0 += $extra;
            }

            $clearTokenCell($sheet, $aItem);
            $clearTokenCell($sheet, $aShop);
            $clearTokenCell($sheet, $aAmt);

            [$shopL, $shopR] = $getMergeSpanCols($sheet, $aShop);
            [$amtL, $amtR] = $getMergeSpanCols($sheet, $aAmt);

            for ($r = $startRow; $r <= $endRow0; $r++) {
                $ensureMerged($sheet, $shopL . $r . ':' . $shopR . $r);
                $ensureMerged($sheet, $amtL . $r . ':' . $amtR . $r);
            }

            $dupStyle($sheet, $shopL . $startRow . ':' . $shopR . $startRow, $shopL . $startRow . ':' . $shopR . ($startRow + max($N, 1) - 1));
            $dupStyle($sheet, $amtL . $startRow . ':' . $amtR . $startRow, $amtL . $startRow . ':' . $amtR . ($startRow + max($N, 1) - 1));
            $dupStyle($sheet, $cItem . $startRow,                 $cItem . $startRow . ':' . $cItem . ($startRow + max($N, 1) - 1));

            $shopRange = $shopL . $startRow . ':' . $shopR . $endRow0;

            $amtRange  = $amtL . $startRow . ':' . $amtR . $endRow0;

            $sheet->getStyle($shopRange)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                ->setWrapText(false);

            
            $applyIdrAccounting($sheet, $amtRange, false);

            $r = $startRow;
            foreach ($details as $d) {
                $sheet->setCellValue($cItem . $r, (string)($d->Item ?? ''));
                $sheet->setCellValue($cShop . $r, (string)($d->RestaurantShop ?? '-'));
                $sheet->setCellValueExplicit(
                    $cAmt . $r,
                    (float)($d->Amount ?? 0),
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC
                );
                $r++;
            }
            for (; $r <= $endRow0; $r++) {
                $sheet->setCellValue($cItem . $r, null);
                $sheet->setCellValue($cShop . $r, null);
                $sheet->setCellValue($cAmt . $r, null);
            }

            $aTotNow = $find($sheet, '[[TOTAL_AMT]]');
            if ($aTotNow) {
                $clearTokenCell($sheet, $aTotNow);
                [$totCol, $totRow] = $topLeftOf($sheet, $aTotNow);
                [$totL, $totR]     = $getMergeSpanCols($sheet, $aTotNow);

                $sheet->setCellValueExplicit(
                    $totCol . $totRow,
                    (float)$details->sum('Amount'),
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC
                );

                $totRange = $totL . $totRow . ':' . $totR . $totRow;
              
                $applyIdrAccounting($sheet, $totRange, true);
            }
        }


        $aCust = $find($sheet, '[[CUST_MEM]]');
        $aSai  = $find($sheet, '[[SAI_MEM]]');

        if ($aCust && $aSai) {
            $startRow = min($rowOf($aCust), $rowOf($aSai));
            $colCust  = $getWriteColFromAnchor($sheet, $aCust);
            $colSai   = $getWriteColFromAnchor($sheet, $aSai);

            $reserved = min(
                $countReserved($sheet, $colCust, $startRow, 40),
                $countReserved($sheet, $colSai, $startRow, 40)
            );

            $custCount = count($custMembers);
            $saiCount  = count($saiMembers);
            $N         = max($custCount, $saiCount);

            $extra = max(0, $N - $reserved);
            if ($extra > 0) {
                $sheet->insertNewRowBefore($startRow + $reserved, $extra);
            }


            $totalRows = $reserved + $extra;
            $dupStyle($sheet, $colCust . $startRow, $colCust . $startRow . ':' . $colCust . ($startRow + $totalRows - 1));
            $dupStyle($sheet, $colSai . $startRow, $colSai . $startRow . ':' . $colSai . ($startRow + $totalRows - 1));


            $clearTokenCell($sheet, $aCust);
            $clearTokenCell($sheet, $aSai);


            $cNo = 1;
            $sNo = 1;
            for ($i = 0; $i < $N; $i++) {
                $cName = isset($custMembers[$i]) ? trim((string)$custMembers[$i]) : '';
                $sName = isset($saiMembers[$i])  ? trim((string)$saiMembers[$i])  : '';

                $sheet->setCellValue($colCust . ($startRow + $i), $cName === '' ? null : ($cNo++ . ' ' . $cName));
                $sheet->setCellValue($colSai . ($startRow + $i), $sName === '' ? null : ($sNo++ . ' ' . $sName));
            }
            for ($i = $N; $i < $totalRows; $i++) {
                $sheet->setCellValue($colCust . ($startRow + $i), null);
                $sheet->setCellValue($colSai . ($startRow + $i), null);
            }


            $keepEmpty   = 0;
            $desiredRows = max(1, min($totalRows, $N + $keepEmpty));
            if ($totalRows > $desiredRows) {
                $sheet->removeRow($startRow + $desiredRows, $totalRows - $desiredRows);
            }
        }

        $filename = "Entertain_Report_{$SysID}.xlsx";
        return response()->streamDownload(function () use ($ss) {
            \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($ss, 'Xlsx')->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    public function update_members(Request $request, int $SysID)
{
    $ext    = (array) $request->input('ExternalMembers', []);
    $intRaw = (array) $request->input('InternalMembers', []);
    $intFlat = $this->normalizeMembers($intRaw);

  
    $cc0 = trim((string) ($request->input('CostCenter.0') ?? ''));

    validator(['InternalMembers' => $intFlat, 'ExternalMembers' => $ext], [
        'InternalMembers.*' => ['string', 'max:100'],
        'ExternalMembers.*' => ['string', 'max:50'],
    ])->validate();

    DB::transaction(function () use ($SysID, $ext, $intFlat, $cc0) {
        DB::table('EntertainReportExternalMember')->where('HeaderID', $SysID)->delete();
        DB::table('EntertainReportInternalMember')->where('HeaderID', $SysID)->delete();

     
        EntertainReport::createMembers($SysID, $ext, $intFlat, $cc0);
    });

    return response()->json(['success' => true]);
}


    #region summary

    public function summary(Request $request)
    {
        $head_title = 'Summary Report';
        $customers  = EntertainReport::getCustomer();

        
        $categories = EntertainReport::getHeaderCategories(); 

       

        return view(
            'sales_report_entertain.summary_report',
            compact('head_title', 'customers', 'categories')
        );
    }


    public function summaryData(Request $request)
    {
        $start = $request->query('start_date');
        $end   = $request->query('end_date');
        $groupBy   = $request->query('group_by', 'customer'); 
        $groupVal  = trim((string)$request->query('group_value', ''));
        $custFallback = $request->query('customer');          

        $startDt = $start ? \Carbon\Carbon::parse($start)->startOfDay() : null;
        $endDt   = $end   ? \Carbon\Carbon::parse($end)->endOfDay()   : null;

        $norm = fn($col) => "COALESCE(NULLIF(LTRIM(RTRIM($col)), ''), '-')";
        $monthKey = "CONVERT(VARCHAR(7), h.Date, 126)"; 
        $custExpr = $norm('h.Customer');

        
        $q = DB::table('EntertainReportHeader as h');

      
        $sumExpr   = 'SUM(COALESCE(h.TotalAmount,0))';
        $countExpr = 'COUNT(*)';

       
        if ($startDt) $q->where('h.Date', '>=', $startDt);
        if ($endDt)   $q->where('h.Date', '<=', $endDt);

        if ($groupBy === 'customer') {
            if ($groupVal === '' && $custFallback) $groupVal = $custFallback;
            if ($groupVal !== '') $q->whereRaw("LTRIM(RTRIM(h.Customer)) = ?", [$groupVal]);
        } elseif ($groupBy === 'category') {
            if ($groupVal !== '') $q->whereRaw("LTRIM(RTRIM(h.Category)) = ?", [$groupVal]);
        } elseif ($groupBy === 'num_ca') {
            if ($groupVal !== '') $q->whereRaw("LTRIM(RTRIM(h.NumCA)) = ?", [$groupVal]);
        } elseif ($groupBy === 'cost_center') {
            
            $iSum = DB::table('EntertainReportItemDtl')
                ->selectRaw('HeaderID, SUM(COALESCE(Amount,0)) as Amt')
                ->groupBy('HeaderID');

            
            $q->leftJoinSub($iSum, 'i', 'i.HeaderID', '=', 'h.SysID');

            if ($groupVal !== '') {
                
                $q->whereExists(function ($qx) use ($groupVal) {
                    $qx->from('EntertainReportInternalMember as im')
                        ->whereRaw('im.HeaderID = h.SysID')
                        ->whereRaw('LTRIM(RTRIM(im.CostCenter)) = ?', [$groupVal]);
                });
            } else {
               
                $q->whereExists(function ($qx) {
                    $qx->from('EntertainReportInternalMember as im')
                        ->whereRaw('im.HeaderID = h.SysID');
                });
            }

           
            $sumExpr   = 'SUM(COALESCE(i.Amt,0))';
            $countExpr = 'COUNT(DISTINCT h.SysID)';
        }

        
        $rowsDb = $q->selectRaw("$monthKey as [Month]")
            ->selectRaw("$custExpr as [Customer]")
            ->selectRaw("$countExpr as Usage")
            ->selectRaw("$sumExpr as Amount")
            ->groupByRaw("$monthKey, $custExpr")
            ->orderBy('Month')
            ->get();

        $rows = $rowsDb->map(fn($r) => [
            'Month'    => (string) $r->Month,
            'Customer' => (string) ($r->Customer ?? '-'),
            'Usage'    => (int) $r->Usage,
            'Amount'   => (float) $r->Amount,
        ]);

        return response()->json([
            'success' => true,
            'meta'    => ['label' => 'Customer'],
            'rows'    => $rows,
        ]);
    }


    public function summaryExport(Request $r)
{
   
    $start = $r->query('start_date');
    try {
        $year = $start ? \Carbon\Carbon::parse($start)->year : now()->year;
    } catch (\Throwable $e) {
        $year = now()->year;
    }
    $lastYear = $year - 1;

    
    $gb   = strtolower($r->query('group_by', 'customer'));
    $gval = $r->query('group_value');
    $cust = $r->query('customer');

    
    $cte = "
      WITH it AS (
        SELECT HeaderID, SUM(ISNULL(Amount,0)) AS AmountSum
        FROM EntertainReportItemDtl
        GROUP BY HeaderID
      )
    ";

    $extraWhere = '';
    $bindExtra  = [];
    if ($gb === 'category' && $gval !== null && $gval !== '') {
        $extraWhere .= " AND h.Category = ?";
        $bindExtra[] = $gval;
    } elseif ($gb === 'num_ca' && $gval !== null && $gval !== '') {
        $extraWhere .= " AND h.NumCA = ?";
        $bindExtra[] = $gval;
    } elseif ($gb === 'customer' && $gval !== null && $gval !== '') {
        $extraWhere .= " AND h.Customer = ?";
        $bindExtra[] = $gval;
    }
    if (!empty($cust)) {
        $extraWhere .= " AND h.Customer = ?";
        $bindExtra[] = $cust;
    }

    
    $sqlCur = $cte . "
      SELECT
        h.Customer                                AS Customer,
        COALESCE(im.CC,'-')                       AS CC,
        MAX(COALESCE(im.SaiMem,''))               AS SaiMem,
        DATEPART(MONTH, h.[Date])                 AS M,
        SUM(ISNULL(it.AmountSum,0))               AS Amount
      FROM EntertainReportHeader h
      LEFT JOIN it ON it.HeaderID = h.SysID
      OUTER APPLY (
        SELECT TOP 1
          NULLIF(LTRIM(RTRIM(m.CostCenter)),'') AS CC,
          m.SAIMember                            AS SaiMem
        FROM EntertainReportInternalMember m
        WHERE m.HeaderID = h.SysID
        ORDER BY
          CASE WHEN NULLIF(LTRIM(RTRIM(m.CostCenter)),'') IS NULL THEN 1 ELSE 0 END,
          m.SysID ASC
      ) im
      WHERE YEAR(h.[Date]) = ? $extraWhere
      GROUP BY h.Customer, COALESCE(im.CC,'-'), DATEPART(MONTH,h.[Date])
    ";
    $cur = DB::connection('sqlsrv')->select($sqlCur, array_merge([$year], $bindExtra));

    
    $sqlLast = $cte . "
      SELECT
        h.Customer                                AS Customer,
        COALESCE(im.CC,'-')                       AS CC,
        MAX(COALESCE(im.SaiMem,''))               AS SaiMem,
        SUM(ISNULL(it.AmountSum,0))               AS Amount
      FROM EntertainReportHeader h
      LEFT JOIN it ON it.HeaderID = h.SysID
      OUTER APPLY (
        SELECT TOP 1
          NULLIF(LTRIM(RTRIM(m.CostCenter)),'') AS CC,
          m.SAIMember                            AS SaiMem
        FROM EntertainReportInternalMember m
        WHERE m.HeaderID = h.SysID
        ORDER BY
          CASE WHEN NULLIF(LTRIM(RTRIM(m.CostCenter)),'') IS NULL THEN 1 ELSE 0 END,
          m.SysID ASC
      ) im
      WHERE YEAR(h.[Date]) = ? $extraWhere
      GROUP BY h.Customer, COALESCE(im.CC,'-')
    ";
    $last = DB::connection('sqlsrv')->select($sqlLast, array_merge([$lastYear], $bindExtra));

   
    $sqlSaiCur = "
      SELECT
        h.Customer AS Customer,
        NULLIF(LTRIM(RTRIM(m.CostCenter)),'') AS CC,
        NULLIF(LTRIM(RTRIM(m.SAIMember)),'')  AS SaiMem
      FROM EntertainReportHeader h
      JOIN EntertainReportInternalMember m ON m.HeaderID = h.SysID
      WHERE YEAR(h.[Date]) = ? $extraWhere
    ";
    $saiCur  = DB::connection('sqlsrv')->select($sqlSaiCur,  array_merge([$year],     $bindExtra));

    $sqlSaiLast = "
      SELECT
        h.Customer AS Customer,
        NULLIF(LTRIM(RTRIM(m.CostCenter)),'') AS CC,
        NULLIF(LTRIM(RTRIM(m.SAIMember)),'')  AS SaiMem
      FROM EntertainReportHeader h
      JOIN EntertainReportInternalMember m ON m.HeaderID = h.SysID
      WHERE YEAR(h.[Date]) = ? $extraWhere
    ";
    $saiLast = DB::connection('sqlsrv')->select($sqlSaiLast, array_merge([$lastYear], $bindExtra));

    
    $groups = [];
    $keyOf  = fn($c) => trim((string)$c) === '' ? '-' : trim((string)$c);

    
    foreach ($cur as $row) {
        $cust = $keyOf($row->Customer ?? '-');
        if (!isset($groups[$cust])) {
            $groups[$cust] = [
                'customer' => $cust,
                'months'   => array_fill(1, 12, 0.0),
                'last'     => 0.0,
               
                'cc_set'   => [],
                'sai_set'  => [],
                
                'cc'       => '',
                'sai'      => '',
            ];
        }

        
        $m = (int)($row->M ?? 0);
        if ($m >= 1 && $m <= 12) {
            $groups[$cust]['months'][$m] += (float)($row->Amount ?? 0);
        }

        
        $cc  = trim((string)($row->CC ?? ''));
        if ($cc !== '' && $cc !== '-')  { $groups[$cust]['cc_set'][$cc] = true; }

        
    }

    
    foreach ($last as $row) {
        $cust = $keyOf($row->Customer ?? '-');
        if (!isset($groups[$cust])) {
            $groups[$cust] = [
                'customer' => $cust,
                'months'   => array_fill(1, 12, 0.0),
                'last'     => 0.0,
                'cc_set'   => [],
                'sai_set'  => [],
                'cc'       => '',
                'sai'      => '',
            ];
        }

        $groups[$cust]['last'] += (float)($row->Amount ?? 0);

       
        $cc  = trim((string)($row->CC ?? ''));
        if ($cc !== '' && $cc !== '-')  { $groups[$cust]['cc_set'][$cc] = true; }

        
    }

    
    $normName = function (string $s): string {
        $s = preg_replace('/\s+/', ' ', trim($s));
        return mb_strtoupper($s);
    };

    foreach ($saiCur as $row) {
        $cust = $keyOf($row->Customer ?? '-');
        if (!isset($groups[$cust])) {
            $groups[$cust] = [
                'customer' => $cust,
                'months'   => array_fill(1, 12, 0.0),
                'last'     => 0.0,
                'cc_set'   => [],
                'sai_set'  => [],
                'cc'       => '',
                'sai'      => '',
            ];
        }
        $cc = trim((string)($row->CC ?? ''));
        if ($cc !== '' && $cc !== '-')  { $groups[$cust]['cc_set'][$cc] = true; }

        $name = trim((string)($row->SaiMem ?? ''));
        if ($name !== '') {
            $groups[$cust]['sai_set'][$normName($name)] = $name; 
        }
    }

    foreach ($saiLast as $row) {
        $cust = $keyOf($row->Customer ?? '-');
        if (!isset($groups[$cust])) {
            $groups[$cust] = [
                'customer' => $cust,
                'months'   => array_fill(1, 12, 0.0),
                'last'     => 0.0,
                'cc_set'   => [],
                'sai_set'  => [],
                'cc'       => '',
                'sai'      => '',
            ];
        }
        $cc = trim((string)($row->CC ?? ''));
        if ($cc !== '' && $cc !== '-')  { $groups[$cust]['cc_set'][$cc] = true; }

        $name = trim((string)($row->SaiMem ?? ''));
        if ($name !== '') {
            $groups[$cust]['sai_set'][$normName($name)] = $name;
        }
    }

    
    foreach ($groups as &$g) {
        $ccs  = array_keys($g['cc_set']);
        sort($ccs);
        $g['cc']  = implode(',', $ccs);

        
        $sais = array_values($g['sai_set']);
        sort($sais);
        $g['sai'] = implode(', ', $sais);

        unset($g['cc_set'], $g['sai_set']);
    }
    unset($g);

    
    uasort($groups, fn($a,$b) => [$a['customer'],$a['cc']] <=> [$b['customer'],$b['cc']]);
    $groups = array_values($groups);

    
    $FILENAME  = 'SummaryReport.xlsx';
    $candidates = [
        resource_path('views/sales_report_entertain/template export/' . $FILENAME),
        storage_path('app/templates/' . $FILENAME),
        public_path($FILENAME),
        base_path($FILENAME),
    ];
    $tplPath = null;
    foreach ($candidates as $p) if (is_file($p)) {
        $tplPath = $p;
        break;
    }
    if (!$tplPath) {
        return response()->json(['success' => false, 'message' => 'Template SummaryReport.xlsx not found'], 500);
    }

    $ss    = \PhpOffice\PhpSpreadsheet\IOFactory::load($tplPath);
    $sheet = $ss->getSheetByName('Sheet1') ?: $ss->getActiveSheet();

    
    $find = function (\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $ws, string $token, int $maxR = 500, int $maxC = 150) {
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($maxC);
        foreach ($ws->getRowIterator(1, $maxR) as $row) {
            $r = $row->getRowIndex();
            foreach ($ws->getColumnIterator('A', $lastCol) as $col) {
                $c = $col->getColumnIndex();
                $addr = $c . $r;
                $val  = (string)$ws->getCell($addr)->getValue();
                if ($val !== '' && strpos($val, $token) !== false) return $addr;
            }
        }
        return null;
    };
    $rowOf = fn($addr) => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($addr)[1];
    $colOf = fn($addr) => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($addr)[0];
    $colIndex = fn($colL) => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($colL);
    $colLetter = fn($idx) => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($idx);

    $aCustomer   = $find($sheet, '[[Customer]]');
    $aCC         = $find($sheet, '[[CC]]');
    $aAmtLast    = $find($sheet, '[[AMT_Last_Year]]');
    $aAmtMonth   = $find($sheet, '[[AMTmonth]]');
    $aSaiMem     = $find($sheet, '[[SaiMem]]');         
    $colSai      = $aSaiMem ? $colOf($aSaiMem) : null;

    if (!$aCustomer || !$aCC || !$aAmtLast || !$aAmtMonth) {
        return response()->json(['success' => false, 'message' => 'incopmlete anchor in template'], 500);
    }

    $rowBudget   = $rowOf($aCustomer);
    $rowActual   = $rowOf($aAmtMonth);
    $colCustomer = $colOf($aCustomer);
    $colCC       = $colOf($aCC);
    $colAmtLast  = $colOf($aAmtLast);

    $startMonthColIdx = $colIndex($colOf($aAmtMonth));
    $lastColIdx       = $startMonthColIdx + 11;
    $lastColLetter    = $colLetter($lastColIdx);

    $contentColIdx    = $startMonthColIdx - 1;
    $contentColLetter = $colLetter($contentColIdx);

    
    foreach ([$aCustomer, $aCC, $aAmtLast, $aAmtMonth, $aSaiMem] as $addr) {
        if ($addr) {
            $val = (string)$sheet->getCell($addr)->getValue();
            $sheet->setCellValue($addr, preg_replace('/\[\[[^\]]+\]\]/','', $val));
        }
    }

   
    $tmplRangeBudget = "A{$rowBudget}:{$lastColLetter}{$rowBudget}";
    $tmplRangeActual = "A{$rowActual}:{$lastColLetter}{$rowActual}";

   
    $N = max(1, count($groups));
    if ($N > 1) {
        
        $sheet->insertNewRowBefore($rowActual + 1, 2 * ($N - 1));
    }

   
    for ($i = 0; $i < $N; $i++) {
        $rBudget = $rowBudget + ($i * 2);
        $rActual = $rBudget + 1;
        $sheet->duplicateStyle($sheet->getStyle($tmplRangeBudget), "A{$rBudget}:{$lastColLetter}{$rBudget}");
        $sheet->duplicateStyle($sheet->getStyle($tmplRangeActual), "A{$rActual}:{$lastColLetter}{$rActual}");
    }

    
    $fmtAmtLast = $sheet->getStyle($colAmtLast . $rowBudget)->getNumberFormat()->getFormatCode();
    $fmtMonth   = $sheet->getStyle($colLetter($startMonthColIdx) . $rowActual)->getNumberFormat()->getFormatCode();

    
    $i = 0;
    foreach ($groups as $g) {
        $rBudget = $rowBudget + ($i * 2);
        $rActual = $rBudget + 1;

        
        $sheet->setCellValue($contentColLetter.$rBudget, 'BUDGET');
        $sheet->setCellValue($contentColLetter.$rActual, 'ACTUAL');

        
        $sheet->setCellValue($colCustomer.$rBudget, $g['customer']);
        $sheet->setCellValue($colCustomer.$rActual, null);

        
        $sheet->setCellValue($colCC.$rBudget, $g['cc']);
        $sheet->setCellValue($colCC.$rActual, null);

        if ($colSai) {
            $sheet->setCellValue($colSai.$rBudget, ($g['sai'] ?? '') !== '' ? $g['sai'] : null); 
            $sheet->setCellValue($colSai.$rActual, null);                                        
        }

      
        $hasCustomer = trim((string)($g['customer'] ?? '')) !== '';
        $vLast = (float)($g['last'] ?? 0);

        if (!$hasCustomer) {
            
            $sheet->setCellValue($colAmtLast.$rBudget, null);
        } else {
            if (abs($vLast) < 0.000001) {
                
                $sheet->setCellValue($colAmtLast.$rBudget, '-');
            } else {
                $sheet->setCellValueExplicit(
                    $colAmtLast.$rBudget,
                    $vLast,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC
                );
                if ($fmtAmtLast) {
                    $sheet->getStyle($colAmtLast.$rBudget)
                          ->getNumberFormat()->setFormatCode($fmtAmtLast);
                }
            }
        }

        for ($m = 1; $m <= 12; $m++) {
            $c    = $colLetter($startMonthColIdx + ($m - 1));
            $vMon = (float)($g['months'][$m] ?? 0);
            if (abs($vMon) < 0.000001) {
                $sheet->setCellValue($c.$rActual, null);
            } else {
                $sheet->setCellValueExplicit(
                    $c.$rActual,
                    $vMon,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC
                );
                if ($fmtMonth) {
                    $sheet->getStyle($c.$rActual)
                          ->getNumberFormat()->setFormatCode($fmtMonth);
                }
            }
        }

        $i++;
    }

   
    $lastActualRow = $rowBudget + (2 * $N) - 1;

    
    $addrBudgetTot = $find($sheet, 'Budget Total') ?: $find($sheet, 'BUDGET TOTAL');
    $addrActualTot = $find($sheet, 'Actual Total') ?: $find($sheet, 'ACTUAL TOTAL');

    
    $rowsTotal = [];
    if ($addrBudgetTot) $rowsTotal[] = $rowOf($addrBudgetTot);
    if ($addrActualTot) $rowsTotal[] = $rowOf($addrActualTot);
    sort($rowsTotal);

    
    if (!empty($rowsTotal)) {
        $rowFirstTotal = $rowsTotal[0];
        
        $removeStart = $lastActualRow + 1;
        $removeCount = max(0, $rowFirstTotal - $removeStart);
        if ($removeCount > 0) {
            $sheet->removeRow($removeStart, $removeCount);
        }
    }

    
    if (function_exists('ini_set')) {
        @ini_set('zlib.output_compression', '0');
        @ini_set('output_buffering', 'Off');
    }
    if (function_exists('ob_get_level')) while (ob_get_level() > 0) @ob_end_clean();

    $stamp    = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d');   // 2025-09-11
    $filename = "Summary_{$stamp}.xlsx";

    return response()->streamDownload(function () use ($ss) {
        \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($ss, 'Xlsx')->save('php://output');
    },
    $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
}

    
    #end region summary


    public function refEmployeeNames(Request $r)
{
    $q       = trim((string) $r->query('q', ''));
    $page    = max(1, (int) $r->query('page', 1));
    $perPage = 20;

    $res = EntertainReport::internalMemberNames($q, $page, $perPage);

    $results = collect($res['data'])->map(fn ($name) => ['id' => $name, 'text' => $name]);
    return response()->json([
        'results'    => $results,
        'pagination' => ['more' => $res['hasMore']],
    ]);
}


public function refEmployeeByName(Request $r)
{
    $name = $r->query('name');
    $cc   = EntertainReport::getCostCenterByName($name);

    return response()->json([
        'name' => $name,
        'cc'   => $cc ?: null,
    ]);
}


}

