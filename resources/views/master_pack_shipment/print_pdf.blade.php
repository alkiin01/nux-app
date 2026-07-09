<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <style>
        /* ─── Base ──────────────────────────────────────────────────────── */
        body {
            font-family: helvetica, sans-serif;
            font-size: 9pt;
            color: #1a1a1a;
            line-height: 1.5;
        }
        /* ─── Title Block ───────────────────────────────────────────────── */
        h2 {
            font-size: 15pt;
            margin: 0 0 4px 0;
            color: #1e3a5f;
            letter-spacing: 0.5px;
        }
        .sub-title { font-size: 8pt; color: #6b7280; letter-spacing: 0.2px; }
        .badge-submitted {
            background-color: #d1e7dd;
            color: #0f5132;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
            letter-spacing: 0.3px;
        }
        .print-time { font-size: 7.5pt; color: #9ca3af; margin-top: 5px; }
        /* ─── Separators ────────────────────────────────────────────────── */
        .sep-thick {
            border: none;
            border-top: 2px solid #1e3a5f;
            margin: 10px 0 12px 0;
        }
        .sep-thin {
            border: none;
            border-top: 1px solid #d1d5db;
            margin: 12px 0 10px 0;
        }
        /* ─── Info Table ────────────────────────────────────────────────── */
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 4px 0; vertical-align: top; font-size: 8.5pt; }
        .info-table .lbl {
            width: 110px;
            font-weight: bold;
            color: #374151;
            white-space: nowrap;
            padding-right: 4px;
        }
        .info-table .colon { width: 14px; color: #6b7280; text-align: center; }
        .info-table .val   { color: #1a1a1a; padding-right: 24px; }
        /* ─── Section Label ─────────────────────────────────────────────── */
        .section-label {
            font-size: 7.5pt;
            font-weight: bold;
            color: #1e3a5f;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 0 0 6px 0;
        }
        /* ─── Detail Table ──────────────────────────────────────────────── */
        .detail-table { width: 100%; border-collapse: collapse; }
        .detail-table thead th {
            background-color: #1e3a5f;
            color: #ffffff;
            font-weight: bolder;
            padding: 7px 10px;
            text-align: center;
            font-size: 8pt;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .detail-table thead th.center { text-align: center; }
        .detail-table tbody td {
            padding: 5px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 8.5pt;
            vertical-align: middle;
        }
        .detail-table tbody td.center { text-align: center; }
        .detail-table tbody tr:nth-child(even) td { background-color: #f4f7fb; }
        .detail-table tfoot td {
            background-color: #1e3a5f;
            color: #ffffff;
            padding: 6px 10px;
            font-size: 8pt;
            font-weight: bold;
        }
        /* ─── Footer ────────────────────────────────────────────────────── */
        .footer {
            position: fixed;
            bottom: 0;  
            font-size: 7pt;
            color: #9ca3af;
            text-align: center;
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            letter-spacing: 0.3px;
        }
    </style>
</head>
<body>


{{-- ─── Document Info ────────────────────────────────────────────────────── --}}
<table class="info-table" style="margin-bottom:4px">
    <tr>
        <td class="lbl">Packing List No.</td>
        <td class="colon">:</td>
        <td class="val"><strong>{{ $header->PackingListNum ?? '-' }}</strong></td>
        <td class="lbl">Customer</td>
        <td class="colon">:</td>
        <td class="val">{{ $header->CustomerName ?? '-' }}</td>
    </tr>
    <tr>
        <td class="lbl">Ship Via</td>
        <td class="colon">:</td>
        <td class="val">{{ $header->ShipViaCode ?? '-' }}</td>
        <td class="lbl">Nopol Kendaraan</td>
        <td class="colon">:</td>
        <td class="val">
            @if($header->TruckingID)
                {{ $header->Nopol ?? '-' }}{{ $header->Jenis ? ' ('.$header->Jenis.')' : '' }}
            @else
                {{ $header->ManualNopol ?? '-' }}
            @endif
        </td>
    </tr>
    <tr>
        <td class="lbl">Driver</td>
        <td class="colon">:</td>
        <td class="val">
            @if($header->TruckingID)
                {{ $header->Driver ?? '-' }}
            @else
                {{ $header->ManualDriver ?? '-' }}
            @endif
        </td>
        <td class="lbl">No. Telepon</td>
        <td class="colon">:</td>
        <td class="val">{{ ($header->TruckingID && $header->NoTlp) ? $header->NoTlp : '-' }}</td>
    </tr>
    @if($header->TruckingID && $header->DriverCadangan)
    <tr>
        <td class="lbl">Driver Cadangan</td>
        <td class="colon">:</td>
        <td class="val">{{ $header->DriverCadangan }}</td>
        <td class="lbl">No. Tlp Cadangan</td>
        <td class="colon">:</td>
        <td class="val">{{ $header->NoTlpCadangan ?? '-' }}</td>
    </tr>
    @endif
    <tr>
        <td class="lbl">Dibuat Oleh</td>
        <td class="colon">:</td>
        <td class="val">{{ $header->CreatedByName ?? '-' }}</td>
        <td class="lbl">Tanggal</td>
        <td class="colon">:</td>
        <td class="val">
            {{ $header->CreatedAt
                ? \Carbon\Carbon::parse($header->CreatedAt)->format('d M Y')
                : '-' }}
        </td>
    </tr>
</table>

<hr class="sep-thin">

{{-- ─── Detail Table ─────────────────────────────────────────────────────── --}}
<p class="section-label">Detail Surat Jalan</p>

<table class="detail-table">
    <thead>
        <tr style="background-color:#1e3a5f; color:#ffffff; margin-bottom: 8px;">
            <th class="center"><strong>No</strong></th>
            <th class="center"><strong>Pack Num</strong></th>
            <th class="center"><strong>No. Surat Jalan</strong></th>
            <th><strong>Item</strong></th>
            {{-- <th><strong>Total Palet</strong></th> --}}
        </tr>
    </thead>
    <tbody>
        @forelse($details as $i => $row)
        <tr style="margin-top: 10px;">
            <td class="center">{{ $i + 1 }}</td>
            <td class="center">{{ $row->PackNum }}</td>
            <td class="center">{{ $row->LegalNumber ?? '-' }}</td>
            <td class="center" style="font-size:8.5pt">
                {{ $items_map[$row->PackNum] ?? '-' }}
            </td>
            {{-- <td class="center">{{ 0 }}</td> --}}
        </tr>
        @empty
        <tr>
            <td colspan="4"
                style="text-align:center; color:#9ca3af; padding:14px;
                       font-style:italic; font-size:8pt">
                Tidak ada detail
            </td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" style="text-align:right; letter-spacing:0.3px">Total SJ</td>
            <td>{{ count($details) }}</td>
        </tr>
    </tfoot>
</table>

{{-- ─── Footer ──────────────────────────────────────────────────────────── --}}
<div class="footer">
    Master Pack Packing List &nbsp;·&nbsp; NUX Portal &nbsp;·&nbsp; {{ config('app.name') }}
</div>

</body>
</html>
