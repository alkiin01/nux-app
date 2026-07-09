<?php

namespace App\Http\Controllers;

use App\Models\AppModel;
use App\Models\MasterPackShipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PDF;

class MasterPackShipmentController extends Controller
{
    // ─── Main page ───────────────────────────────────────────────────────────

    public function index()
    {
        $my_id = Auth::user()->id;
        $uri   = explode('/', url()->current());
        $menu  = $this->menu($my_id, count($uri) < 5 ? 'home' : $uri[4]);

        $data['head_title']   = $menu['head_title'];
        $data['menu_level_1'] = $menu['menu_level_1'];
        $data['menu_level_2'] = $menu['menu_level_2'];
        $data['menu_level_3'] = $menu['menu_level_3'];
        $data['menu_level_4'] = $menu['menu_level_4'];

        return view('master_pack_shipment/index', $data);
    }

    // ─── Summary cards ───────────────────────────────────────────────────────

    public function get_count_document(Request $request)
    {
        $data['total']     = MasterPackShipment::count_total();
        $data['draft']     = MasterPackShipment::count_draft();
        $data['submitted'] = MasterPackShipment::count_submitted();
        echo json_encode($data);
    }

    // ─── Front-table (DataTable server-side) ─────────────────────────────────

    public function front_table(Request $request)
    {
        $search    = $request->front_table_search;
        $status_id = $request->status_id;

        $columns = [
            0 => 'id',
            1 => 'id',
            2 => 'PackingListNum',
            3 => 'CustomerName',
            4 => 'ShipViaCode',
            5 => 'id',
            6 => 'CreatedAt',
        ];

        $col_idx   = intval($request->input('order.0.column', 0));
        $order_col = $columns[$col_idx] ?? 'id';
        $dir       = $col_idx === 0 ? 'desc' : ($request->input('order.0.dir', 'desc'));

        $query     = MasterPackShipment::get_transaction_list($search, $status_id);
        $totalData = $query->count();
        $limit     = $request->input('length');
        $start     = $request->input('start');

        $posts = $query->orderBy($order_col, $dir)->offset($start)->limit($limit)->get();

        $data = [];
        $no   = $start + 1;

        foreach ($posts as $post) {
            $sys_id = Crypt::encryptString((string) $post->id);

            $status_badge = $post->PackingListNum
                ? '<span class="badge badge-light-success">Submitted</span>'
                : '<span class="badge badge-light-warning">Draft</span>';

            $action = '
                <button type="button" class="btn btn-icon btn-light-primary btn-xs"
                    onclick="openDocument(\'' . $sys_id . '\')" title="Open">
                    <span class="svg-icon svg-icon-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z" fill="black"/>
                            <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="black"/>
                        </svg>
                    </span>
                </button>';

            $nestedData['no']             = $no++;
            $nestedData['action']         = $action;
            $nestedData['PackingListNum'] = $post->PackingListNum ?? '-';
            $nestedData['CustomerName']   = $post->CustomerName ?? '-';
            $nestedData['ShipViaCode']  = $post->ShipViaCode ?? '-';
            if ($post->Nopol) {
                $nestedData['TruckNopol'] = $post->Nopol . ($post->Driver ? ' / ' . $post->Driver : '');
            } elseif ($post->ManualNopol) {
                $nestedData['TruckNopol'] = $post->ManualNopol . ($post->ManualDriver ? ' / ' . $post->ManualDriver : '');
            } else {
                $nestedData['TruckNopol'] = '-';
            }
            $nestedData['CreatedAt']    = $post->CreatedAt
                ? AppModel::local_date_formate(substr($post->CreatedAt, 0, 10))
                : '-';
            $nestedData['status'] = $status_badge;
            $data[] = $nestedData;
        }

        echo json_encode([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => intval($totalData),
            'recordsFiltered' => intval($totalData),
            'data'            => $data,
        ]);
    }

    // ─── Load document header data for the form tab ──────────────────────────

    public function get_document_data(Request $request)
    {
        try {
            $doc_id = (int) Crypt::decryptString($request->trc_unix_id);
        } catch (\Exception $e) {
            echo json_encode(['ref_tab' => 0, 'error' => 'ID tidak valid']);
            return;
        }

        if ($doc_id <= 0) {
            echo json_encode(['ref_tab' => 0, 'error' => 'ID tidak valid']);
            return;
        }

        $header = MasterPackShipment::get_header($doc_id);

        if ($header) {
            $data['ref_tab']        = 1;
            $data['id']             = $header->id;
            $data['PackingListNum'] = $header->PackingListNum ?? '';
            $data['CustID']         = $header->CustID ?? '';
            $data['CustomerName']   = $header->CustomerName ?? '';
            $data['ShipViaCode']    = $header->ShipViaCode ?? '';
            $data['TruckingID']     = $header->TruckingID ?? null;
            $data['TruckingNopol']  = $header->Nopol ?? '';
            $data['TruckingDriver'] = $header->Driver ?? '';
            $data['TruckingNoTlp']  = $header->NoTlp ?? '';
            $data['TruckingJenis']  = $header->Jenis ?? '';
            $data['ManualNopol']    = $header->ManualNopol ?? '';
            $data['ManualDriver']   = $header->ManualDriver ?? '';
            $data['CreatedBy']      = $header->CreatedBy ?? '';
            $data['CreatedByName'] = $header->CreatedByName ?? '';
            $data['CreatedAt']      = $header->CreatedAt
                ? AppModel::local_date_formate(substr($header->CreatedAt, 0, 10))
                : '';
            $data['is_submitted']   = $header->PackingListNum ? 1 : 0;
            $data['trc_unix_id']    = $request->trc_unix_id;
        } else {
            $data['ref_tab'] = 0;
        }

        echo json_encode($data);
    }

    // ─── Store / update header ────────────────────────────────────────────────
    // Header hanya menyimpan ShipViaCode dan TrackingNumber.
    // Customer diisi otomatis saat scan surat jalan pertama.

    public function store_head(Request $request)
    {
        $username = Auth::user()->username;
        date_default_timezone_set('Asia/Jakarta');

        $doc_id = (int) $request->doc_id;

        $ship_via_code = strip_tags($request->ship_via_code);
        $is_dpk        = (strtoupper($ship_via_code) === 'DPK');

        $payload = [
            'ShipViaCode'  => $ship_via_code,
            'TruckingID'   => $is_dpk ? ((int)$request->trucking_id ?: null) : null,
            'ManualNopol'  => $is_dpk ? null : strip_tags($request->manual_nopol ?? ''),
            'ManualDriver' => $is_dpk ? null : strip_tags($request->manual_driver ?? ''),
            'UpdatedAt'    => now(),
            'UpdatedBy'    => $username,
        ];

        try {
            if ($doc_id === 0) {
                $payload['CreatedAt'] = now();
                $payload['CreatedBy'] = $username;
                $new_id = MasterPackShipment::store_header($payload);
                $name = User::where('username', $new_id)->value('name');
                echo json_encode(['status' => 1, 'message' => 'Packing list berhasil dibuat', 'id' => $new_id]);
            } else {
                $header = MasterPackShipment::get_header($doc_id);

                if (!$header) {
                    echo json_encode(['status' => 0, 'message' => 'Dokumen tidak ditemukan']);
                    return;
                }
                // if ($header->PackingListNum) {
                //     echo json_encode(['status' => 0, 'message' => 'Dokumen sudah disubmit, tidak bisa diedit']);
                //     return;
                // }

                MasterPackShipment::update_header($doc_id, $payload);
                echo json_encode(['status' => 1, 'message' => 'Packing list berhasil diupdate', 'id' => $doc_id]);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 0, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // ─── Submit: generate PackingListNum ─────────────────────────────────────

    public function submit_document(Request $request)
    {
        $doc_id = (int) $request->doc_id;

        $header = MasterPackShipment::get_header($doc_id);
        if (!$header) {
            echo json_encode(['status' => 0, 'message' => 'Dokumen tidak ditemukan']);
            return;
        }
        if ($header->PackingListNum) {
            echo json_encode(['status' => 0, 'message' => 'Dokumen sudah disubmit']);
            return;
        }

        $details = MasterPackShipment::get_details($doc_id);
        if ($details->isEmpty()) {
            echo json_encode(['status' => 0, 'message' => 'Tidak ada surat jalan dalam packing list ini']);
            return;
        }

        $packing_list_num = MasterPackShipment::generate_packing_list_num();

        try {
            MasterPackShipment::update_header($doc_id, [
                'PackingListNum' => $packing_list_num,
                'UpdatedAt'      => now(),
                'UpdatedBy'      => Auth::user()->username,
            ]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 0, 'message' => 'Gagal submit: ' . $e->getMessage()]);
            return;
        }

        echo json_encode([
            'status'          => 1,
            'message'         => 'Packing list berhasil disubmit',
            'PackingListNum'  => $packing_list_num,
        ]);
    }

    // ─── Unsubmit: clear PackingListNum → kembali ke Draft ───────────────────

    public function unsubmit_document(Request $request)
    {
        $doc_id = (int) $request->doc_id;

        $header = MasterPackShipment::get_header($doc_id);
        if (!$header) {
            echo json_encode(['status' => 0, 'message' => 'Dokumen tidak ditemukan']);
            return;
        }
        if (!$header->PackingListNum) {
            echo json_encode(['status' => 0, 'message' => 'Dokumen belum disubmit']);
            return;
        }

        try {
            MasterPackShipment::update_header($doc_id, [
                'PackingListNum' => null,
                'UpdatedAt'      => now(),
                'UpdatedBy'      => Auth::user()->username,
            ]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 0, 'message' => 'Gagal unsubmit: ' . $e->getMessage()]);
            return;
        }

        echo json_encode(['status' => 1, 'message' => 'Dokumen berhasil di-unsubmit dan dapat diedit kembali']);
    }

    // ─── Detail table (DataTable server-side) ────────────────────────────────

    public function detail_table(Request $request)
    {
        $doc_id = (int) $request->doc_id;
        $search = $request->detail_table_search;

        $header     = MasterPackShipment::get_header($doc_id);
        $can_delete = ($header && !$header->PackingListNum) ? 1 : 0;

        $query     = MasterPackShipment::get_detail_list($doc_id, $search);
        $totalData = $query->count();
        $limit     = $request->input('length');
        $start     = $request->input('start');

        $posts = $query->orderBy('id', 'asc')->offset($start)->limit($limit)->get();

        $data = [];
        $no   = $start + 1;

        foreach ($posts as $post) {
            $delete_btn = $can_delete
                ? '<button type="button" class="btn btn-icon btn-light-danger btn-xs"
                        onclick="deleteDetail(' . $post->id . ', ' . $doc_id . ')" title="Hapus">
                        <span class="svg-icon svg-icon-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="black"/>
                                <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="black"/>
                                <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="black"/>
                            </svg>
                        </span>
                   </button>'
                : '';

            $nestedData['no']          = $no++;
            $nestedData['PackNum']     = $post->PackNum;
            $nestedData['LegalNumber'] = $post->LegalNumber ?? '-';
            $nestedData['PONum']       = $post->PONum ?? '-';
            $nestedData['action']      = $delete_btn;
            $data[] = $nestedData;
        }

        echo json_encode([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => intval($totalData),
            'recordsFiltered' => intval($totalData),
            'data'            => $data,
        ]);
    }

    // ─── Scan QR code (add surat jalan to packing list) ──────────────────────

    public function scan_qr(Request $request)
    {
        $doc_id   = (int) $request->doc_id;
        $pack_num = (int) $request->pack_num;

        if ($doc_id <= 0 || $pack_num <= 0) {
            echo json_encode(['status' => 0, 'message' => 'Input tidak valid']);
            return;
        }

        $header = MasterPackShipment::get_header($doc_id);
        if (!$header) {
            echo json_encode(['status' => 0, 'message' => 'Dokumen tidak ditemukan']);
            return;
        }
        if ($header->PackingListNum) {
            echo json_encode(['status' => 0, 'message' => 'Dokumen sudah disubmit']);
            return;
        }

        // ── Validasi ke Epicor ShipHead ──────────────────────────────────────
        try {
            $ship_head = DB::connection('sqlsrv4')
                ->table('ShipHead AS a')
                ->join('Erp.Customer AS b', 'a.CustNum', '=', 'b.CustNum')
                ->where('a.PackNum', $pack_num)
                ->select('a.PackNum', 'a.LegalNumber', 'a.ShipStatus', 
                         'b.CustID', 'b.Name AS CustomerName')
                ->first();
        } catch (\Exception $e) {
            echo json_encode(['status' => 0, 'message' => 'Gagal mengakses data Epicor: ' . $e->getMessage()]);
            return;
        }

        if (!$ship_head) {
            echo json_encode(['status' => 0, 'message' => 'PackNum ' . $pack_num . ' tidak ditemukan di Epicor']);
            return;
        }
        if (strtolower($ship_head->ShipStatus) !== 'open') {
            echo json_encode(['status' => 0, 'message' => 'Surat jalan ' . $pack_num . ' tidak berstatus Open (status: ' . $ship_head->ShipStatus . ')']);
            return;
        }

        // ── Validasi customer: harus sama dengan header ──────────────────────
        if ($header->CustID && $header->CustID !== $ship_head->CustID) {
            echo json_encode([
                'status'  => 0,
                'message' => 'Customer tidak sesuai. Packing list ini untuk customer ' . $header->CustomerName . ' (' . $header->CustID . ')',
            ]);
            return;
        }

        // ── Cek duplikasi ────────────────────────────────────────────────────
        if (MasterPackShipment::check_pack_num_in_doc($pack_num, $doc_id) > 0) {
            echo json_encode(['status' => 0, 'message' => 'PackNum ' . $pack_num . ' sudah ada di packing list ini']);
            return;
        }
        if (MasterPackShipment::check_pack_num_globally($pack_num, $doc_id) > 0) {
            echo json_encode(['status' => 0, 'message' => 'PackNum ' . $pack_num . ' sudah digunakan di packing list lain']);
            return;
        }

        // ── Call ERP API: Shipment/Shipped ───────────────────────────────────
        try {
            $client   = new Client();
            $password = Crypt::decryptString(Auth::user()->epicor_password) ;  
            $api_resp = $client->request('POST', self::get_host_api() . 'Shipment/Shipped', [
                'json' => [
                    'packNum'        => $pack_num,
                    'readyToInvoice' => true,
                    'nik'            => Auth::user()->username,
                    'password'       => $password,
                ],
                'headers' => ['Content-Type' => 'application/json'],
                'verify'  => false,
                'timeout' => 30,
            ]);
            $api_body = json_decode((string) $api_resp->getBody(), true);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $err_desc = null;
            if ($e->hasResponse()) {
                $err_json = json_decode((string) $e->getResponse()->getBody(), true);
                $err_desc = $err_json['desc'] ?? null;
            }
            echo json_encode(['status' => 0, 'message' => 'Gagal menghubungi API: ' . ($err_desc ?? $e->getMessage())]);
            return;
        } catch (\Exception $e) {
            echo json_encode(['status' => 0, 'message' => 'Gagal menghubungi API: ' . $e->getMessage()]);
            return;
        }

        if (empty($api_body) || ($api_body['code'] ?? 0) != 200) {
            $desc = $api_body['desc'] ?? 'Gagal memproses pengiriman di Epicor';
            echo json_encode(['status' => 0, 'message' => $desc]);
            return;
        }

        // ── Simpan detail ────────────────────────────────────────────────────
        $detail_id = MasterPackShipment::store_detail([
            'MasterPackID' => $doc_id,
            'PackNum'      => $pack_num,
            'LegalNumber'  => $ship_head->LegalNumber,
            // 'PONum'        => $ship_head->PONum,
        ]);

        // ── Auto-fill customer di header (jika belum terisi) ─────────────────
        if (!$header->CustID) {
            MasterPackShipment::update_header($doc_id, [
                'CustID'       => $ship_head->CustID,
                'CustomerName' => $ship_head->CustomerName,
                'UpdatedAt'    => now(),
                'UpdatedBy'    => Auth::user()->username,
            ]);
        }

        echo json_encode([
            'status'       => 1,
            'message'      => 'Scan berhasil: ' . ($ship_head->LegalNumber ?: $pack_num),
            'detail_id'    => $detail_id,
            'PackNum'      => $pack_num,
            'LegalNumber'  => $ship_head->LegalNumber,
            'CustID'       => $ship_head->CustID,
            'CustomerName' => $ship_head->CustomerName,
        ]);
    }

    // ─── Delete a single detail row ───────────────────────────────────────────

    public function delete_detail(Request $request)
    {
        $detail_id = (int) $request->detail_id;
        $doc_id    = (int) $request->doc_id;

        $header = MasterPackShipment::get_header($doc_id);
        if ($header && $header->PackingListNum) {
            echo json_encode(['status' => 0, 'message' => 'Dokumen sudah disubmit, tidak bisa hapus item']);
            return;
        }

        // Ambil PackNum dari detail yang akan dihapus
        $detail = DB::table('DtlPackShipment')->where('id', $detail_id)->first();
        if (!$detail) {
            echo json_encode(['status' => 0, 'message' => 'Item tidak ditemukan']);
            return;
        }

        // ── Call ERP API: Shipment/Shipped (readyToInvoice=false) ────────────
        try {
            $client   = new Client();
            $password = Crypt::decryptString(Auth::user()->epicor_password);
            $api_resp = $client->request('POST', self::get_host_api() . 'Shipment/Shipped', [
                'json' => [
                    'packNum'        => (int) $detail->PackNum,
                    'readyToInvoice' => false,
                    'nik'            => Auth::user()->username,
                    'password'       => $password,
                ],
                'headers' => ['Content-Type' => 'application/json'],
                'verify'  => false,
                'timeout' => 30,
            ]);
            $api_body = json_decode((string) $api_resp->getBody(), true);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $err_desc = null;
            if ($e->hasResponse()) {
                $err_json = json_decode((string) $e->getResponse()->getBody(), true);
                $err_desc = $err_json['desc'] ?? null;
            }
            echo json_encode(['status' => 0, 'message' => 'Gagal menghubungi API: ' . ($err_desc ?? $e->getMessage())]);
            return;
        } catch (\Exception $e) {
            echo json_encode(['status' => 0, 'message' => 'Gagal menghubungi API: ' . $e->getMessage()]);
            return;
        }

        if (empty($api_body) || ($api_body['code'] ?? 0) != 200) {
            $desc = $api_body['desc'] ?? 'Gagal membatalkan pengiriman di Epicor';
            echo json_encode(['status' => 0, 'message' => $desc]);
            return;
        }

        // ── Hapus detail ─────────────────────────────────────────────────────
        try {
            MasterPackShipment::delete_detail($detail_id);
        } catch (\Exception $e) {
            echo json_encode(['status' => 0, 'message' => 'Gagal menghapus item: ' . $e->getMessage()]);
            return;
        }
        echo json_encode(['status' => 1, 'message' => 'Item berhasil dihapus']);
    }

    // ─── Soft-delete entire document ─────────────────────────────────────────

    public function delete_document(Request $request)
    {
        $doc_id = (int) $request->doc_id;

        $header = MasterPackShipment::get_header($doc_id);
        if (!$header) {
            echo json_encode(['status' => 0, 'message' => 'Dokumen tidak ditemukan']);
            return;
        }
        if ($header->PackingListNum) {
            echo json_encode(['status' => 0, 'message' => 'Dokumen sudah disubmit, tidak bisa dihapus']);
            return;
        }

        $detail_count = DB::table('DtlPackShipment')->where('MasterPackID', $doc_id)->count();
        if ($detail_count > 0) {
            echo json_encode(['status' => 0, 'message' => 'Dokumen masih memiliki ' . $detail_count . ' item, hapus semua item terlebih dahulu']);
            return;
        }

        try {
            MasterPackShipment::soft_delete_header($doc_id);
        } catch (\Exception $e) {
            echo json_encode(['status' => 0, 'message' => 'Gagal menghapus dokumen: ' . $e->getMessage()]);
            return;
        }
        echo json_encode(['status' => 1, 'message' => 'Dokumen berhasil dihapus']);
    }

    // ─── Lookup: TruckingNumber ───────────────────────────────────────────────

    public function get_trucking_list(Request $request)
    {
        try {
            $query = DB::table('TruckingNumber')->orderBy('Nopol');

            if ($request->filled('search')) {
                $term = $request->search;
                $query->where(function ($q) use ($term) {
                    $q->where('Nopol',  'like', '%' . $term . '%')
                      ->orWhere('Driver', 'like', '%' . $term . '%');
                });
            }

            $rows = $query->get();
            $data = $rows->map(fn($r) => [
                'id'     => $r->id,
                'text'   => $r->Nopol . ' - ' . ($r->Driver ?? '-'),
                'nopol'  => $r->Nopol,
                'driver' => $r->Driver ?? '',
                'noTlp'  => $r->NoTlp ?? '',
                'jenis'  => $r->Jenis ?? '',
            ])->values()->all();

            echo json_encode(['status' => 1, 'data' => $data]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 0, 'message' => 'Gagal memuat data trucking: ' . $e->getMessage()]);
        }
    }

    // ─── Lookup: ShipViaCode (Epicor Erp.ShipVia) ────────────────────────────

    public function get_ship_via_list(Request $request)
    {
        try {
            $query = DB::connection('sqlsrv4')
                ->table('Erp.ShipVia')
                ->select('ShipViaCode', 'Description')
                ->orderBy('ShipViaCode');

            if ($request->filled('search')) {
                $term = $request->search;
                $query->where(function ($q) use ($term) {
                    $q->where('ShipViaCode', 'like', '%' . $term . '%')
                      ->orWhere('Description', 'like', '%' . $term . '%');
                });
            }

            $rows = $query->get();

            $data = $rows->map(fn($r) => [
                'code' => $r->ShipViaCode,
                'desc' => $r->Description,
            ])->values()->all();

            echo json_encode(['status' => 1, 'data' => $data]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 0, 'message' => 'Gagal memuat Ship Via: ' . $e->getMessage()]);
        }
    }

    // ─── Print PDF ───────────────────────────────────────────────────────────

    public function print_pdf(Request $request)
    {
        $doc_id = (int) $request->doc_id;
        $header = MasterPackShipment::get_header($doc_id);

        if (!$header) {
            abort(404);
        }

        $details = MasterPackShipment::get_details($doc_id);

        // ── Build items map per PackNum from Epicor ShipDtl ────────────────────
        $items_map = [];
        $pack_nums = $details->pluck('PackNum')->filter()->unique()->values()->toArray();
        if (!empty($pack_nums)) {
            try {
                $rows = DB::connection('sqlsrv4')
                    ->table('Erp.ShipDtl')
                    ->whereIn('PackNum', $pack_nums)
                    ->select('PackNum', DB::raw('COUNT(DISTINCT PartNum) AS PartCount'))
                    ->groupBy('PackNum')
                    ->get();

                foreach ($rows as $row) {
                    $items_map[$row->PackNum] = $row->PartCount;
                }
            } catch (\Exception $e) {
                // Epicor tidak tersedia — kolom items kosong
            }
        }

        $qr_content = $header->PackingListNum ?? ('PKL-' . $doc_id);
        // $qr_base64  = 'data:image/png;base64,' . base64_encode(
        //     QrCode::format('png')->size(80)->margin(1)->generate($qr_content)
        // );
$style = array(
            'border' => false,
            'vpadding' => 2,
            'hpadding' => 2,
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        $html = view('master_pack_shipment/print_pdf', [
            'header'    => $header,
            'details'   => $details,
            'items_map' => $items_map,
        ])->render();

        $filename = 'PackingList_' . ($header->PackingListNum ?? $doc_id) . '.pdf';
        PDF::SetAuthor('Al Kindi');
        PDF::SetCreator('NUX Portal');
        PDF::SetTitle($filename);
        PDF::SetMargins(10, 28, 10);
        PDF::SetAutoPageBreak(true, 15);
        PDF::setPrintHeader(true);
        PDF::setHeaderCallback(function ($pdf) use ($header, $qr_content, $style) {
            $pdf->SetFont('helvetica', '', 9);
            $printTime = \Carbon\Carbon::now()->format('d M Y H:i');
            $pdf->write2DBarcode($qr_content, 'QRCODE,H', 190, 13, 10, 10, $style, 'N');
            $headerHtml = '<table style="width:100%; border-collapse:collapse">'
                . '<tr>'
                . '<td style="width:62%; vertical-align:bottom; padding:0">'
                . '<span style="font-size:15pt; font-weight:bold; color:#1e3a5f; letter-spacing:0.5px;">Packing List</span><br>'
                . '<span style="font-size:8pt; color:#6b7280; letter-spacing:0.2px;">NUX Portal  Epicor ERP Integration</span>'
                . '</td>'
                . '<td style="width:38%; text-align:right; vertical-align:top; padding:0">'
                . '<span style="background-color:#d1e7dd; color:#0f5132; padding:3px 10px; border-radius:4px; font-size:8pt; font-weight:bold;">Submitted</span><br>'
                . '<span style="font-size:7.5pt; color:#9ca3af;">Dicetak: ' . $printTime . '</span>'
                . '</td>'
                . '</tr>'
                . '</table>'
                
                ;

            $pdf->writeHTMLCell(190, 0, 10, 5, $headerHtml, 0, 1);
            $pdf->SetLineWidth(0.5);
            $pdf->SetDrawColor(30, 58, 95);
            $pdf->Line(10, 24, 200, 24);

        });
        PDF::AddPage('P', 'A4');
        PDF::SetFont('helvetica', '', 9);
        PDF::writeHTML($html, true, false, true, false, '');
        PDF::Output($filename, 'I');
    }
}
