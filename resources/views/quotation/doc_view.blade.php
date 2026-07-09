<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quotation Preview</title>
</head>

<body>
    <style>
        body,
        table,
        td,
        th {
            font-family: "Times New Roman", Times, serif;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        td,
        th {
            border: 1px solid #000;
            font-size: 11px;
            padding: 5px;
            line-height: 14px;
        }

        .header-title {
            padding: 5px 5px;
            text-align: center;
            vertical-align: middle;
            font-size: 12px;
            font-weight: bold;
        }

        .section-title {
            font-weight: bold;
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .col-number {
            width: 35px;
            text-align: center;
            padding: 2px 2px;
            white-space: nowrap;
        }

        .title-center {
            width: 100%;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }

        .currency {
            text-align: left;
        }

        .amount {
            text-align: right;
        white-space: nowrap;
        }
    </style>

    <div>
        <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td class="bold" style="padding: 5px 5px; text-align:center; vertical-align:middle;">
                    <div>
                        <img src="{{ public_path('assets/logo/image-transparent.png') }}" width="150">
                    </div>
                </td>

                <td class="header-title no-border">
                    <div class="title-center">SUBCONT PROCESS EVALUATION</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border:none; height:10px;"></td>
            </tr>
        </table>
        <table>
            <tr>
                <td class="bold" style="border:none;">PART NO</td>
                <td class="bold" style="border:none;">: {{ $material->PartFG ?? '-' }}</td>
            </tr>
            <tr>
                <td class="bold" style="border:none;">PART NAME</td>
                <td class="bold" style="border:none;">: {{ $material->PartFGDesc ?? '-' }}</td>
            </tr>
            <tr>
                <td class="bold" style="border:none;">SUPPLIER CANDIDATE</td>
                <td class="bold" style="border:none;">: {{ $header->SupplierName ?? '-' }}</td>
            </tr>
            <tr>
                <td colspan="2" style="border:none; height:10px;"></td>
            </tr>
        </table>
        <table>
            <tr>
                <td colspan="6" class="section-title" style="background-color:#7ecdec;width:535px;">ORIGINAL QUOTATION</td>
            </tr>
            <tr>
                <td colspan="5" style="text-align:center; width:425px;">Item</td>
                <td style="text-align:center;width:110px;">Estimate</td>
            </tr>
            <tr>
                <td class="col-number">(1)</td>
                <td style="width: 100px">Material Cost</td>
                <td colspan="3" style="text-align:center; color:blue; width:290px;">
                    (Material Spec. {{ $material->PartMtlDesc ?? '-' }})
                </td>
                <td style="background-color:#c6efce; white-space:nowrap; width:110px;">
                    @if ($material && $material->MtlCEstimate !== null)
                        <span class="currency">
                            Rp
                        </span>
                        <span class="amount">
                            {{ number_format($material->MtlCEstimate, 0, '.', ',') }}
                        </span>
                    @else
                        -
                    @endif
                </td>
            </tr>

            <tr>
                <td class="col-number"></td>
                <td>Material Weight/Kg.</td>
                <td class="center" style=" width: 80px white-space:nowrap;">
                    @if ($material && $material->MtlWQty)
                    {{ number_format($material->MtlWQty, 3) }}
                    @else
                        -
                    @endif
                </td>
                <td style="width: 90px white-space:nowrap;">
                    @if ($material && $material->MtlWPrice)
                        <span class="currency">
                            Rp
                        </span>
                        <span class="amount">
                            {{ number_format($material->MtlWPrice, 0, '.', ',') }}
                        </span>
                    @else
                        -
                    @endif
                </td>
                <td style="width: 120px; text-align: center;">
                    Validity :
                    <br>
                    {{ \Carbon\Carbon::parse($material->EffectiveDate)->format('d M y') ?? '' }}
                    -
                    {{ \Carbon\Carbon::parse($material->ExpiredDate)->format('d M y') ?? '' }}
                </td>
                <td style="white-space:nowrap;width: 110px;">
                    <span class="currency">
                        Rp
                    </span>
                    <span class="amount">
                        {{ number_format((int) $material->MtlWEstimate, 0, '.', ',') }}
                    </span>
                </td>
            </tr>

            <tr>
                <td class="col-number"></td>
                <td>Part Weight/Kg.</td>
                <td class="center" style=" ">
                    @if ($material && $material->PartWQty)
                        {{ number_format($material->PartWQty, 3) }}
                    @else
                        -
                    @endif
                </td>
                <td style="white-space:nowrap;">

                </td>
                <td></td>
                <td style="white-space:nowrap;">

                </td>
            </tr>

            <tr>
                <td class="col-number"></td>
                <td>Scrap</td>
                <td class="center" style=" ">
                    @if ($material && $material->ScrapQty)
                        {{ number_format($material->ScrapQty, 3) }}
                    @else
                        -
                    @endif
                </td>
                <td style="white-space:nowrap;">
                    @if ($material->ScrapPrice > 0)
                        <span class="currency">
                            Rp
                        </span>
                        <span class="amount">
                            {{ number_format($material->ScrapPrice, 0, '.', ',') }}
                        </span>
                    @endif
                </td>
                <td></td>
                <td style="color:red; white-space:nowrap;">
                    @if ($material->ScrapEstimate)
                        <span class="currency">
                            Rp
                        </span>
                        <span class="amount">
                            -{{ number_format($material->ScrapEstimate, 0, '.', ',') }}
                        </span>
                    @endif
                </td>
            </tr>
            <tr>
                <td></td>
                <td colspan="3">{{ $material->Note }}</td>
                <td></td>
                <td></td>
            </tr>
            @php
                $totalEstimate = 0;
            @endphp
            @if ($purchase && count($purchase) > 0)
                @php
                    $totalEstimate = $purchase->sum('Estimate');
                @endphp
                <tr>
                    <td class="col-number">(2)</td>
                    <td class="center">Purchase Part Cost</td>
                    <td class="center">Spec Purchase Part</td>
                    <td class="center">Quantity</td>
                    <td class="center">Price</td>
                    <td style="white-space:nowrap;">
                        <span class="currency">
                            Rp
                        </span>
                        <span class="amount">
                            {{ number_format($totalEstimate, 0, ',', '.') }}
                        </span>
                    </td>
                </tr>

                @php $purchaseNo = 1; @endphp
                @foreach ($purchase as $pu)
                    <tr>
                        <td class="col-number">2,{{ $purchaseNo++ }}</td>
                        <td class="center">{{ ucwords(strtolower($pu->PurchasePart)) }}</td>
                        <td class="center">{{ ucwords(strtolower($pu->SpecPurchasePart)) }}</td>
                        <td class="center">{{ number_format($pu->Qty, 0) }}</td>
                        <td style="white-space:nowrap;">
                            <span class="currency">
                                Rp
                            </span>
                            <span class="amount">
                                {{ number_format($pu->Price, 0, ',', '.') }}
                            </span>
                        </td>
                        <td style="white-space:nowrap;">
                            <span class="currency">
                                Rp
                            </span>
                            <span class="amount">
                                {{ number_format($pu->Estimate, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr class="center">
                    <td class="col-number">(2)</td>
                    <td>Purchase Part Cost</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endif

            <tr>
                <td class="col-number">(3)</td>
                <td colspan="4">Manufacturing Cost</td>
                <td style="background-color:#c6efce; white-space:nowrap;">
                    @php
                        $manufacturingCost = $process->sum('Estimate');
                    @endphp
                    @if ($manufacturingCost)
                        <span class="currency">
                            Rp
                        </span>
                        <span class="amount">
                            {{ number_format($manufacturingCost, 0, '.', ',') }}
                        </span>
                    @else
                        -
                    @endif
                </td>
            </tr>

            <tr class="center">
                <td colspan="2">Process</td>
                <td>Machine</td>
                <td>Stroke</td>
                <td>Rate</td>
                <td>Estimate</td>
            </tr>
            @php
                $no = 1;
            @endphp
            @foreach ($process as $item)
                <tr>
                    <td class="col-number">3,{{ $no++ }}</td>
                    <td>{{ ucwords(strtolower($item->NameProcess)) }}</td>
                    <td class="center">{{ ucwords(strtolower($item->Machine)) }}</td>
                    <td class="center" style=" " >{{ $item->Stroke == 0 ? '' : rtrim(rtrim($item->Stroke, '0'), '.') }}</td>
                    <td style="white-space:nowrap;">
                        <span class="currency">
                            Rp
                        </span>
                        <span class="amount">
                            {{ rtrim(rtrim(number_format((float) $item->Rate, 2, '.', ','), '0'), '.') }}
                        </span>
                    </td>
                    <td style="white-space:nowrap;">
                        <span class="currency">
                            Rp
                        </span>
                        <span class="amount">
                            {{ rtrim(rtrim(number_format((float) $item->Estimate, 2, '.', ','), '0'), '.') }}
                        </span>
                    </td>
                </tr>
            @endforeach
            @php
                $sub_total = $material->MtlCEstimate + $totalEstimate + $manufacturingCost;
            @endphp
            <tr>
                <td class="center" colspan="3">Sub Total = &Sigma; (1)+(2)+(3)</td>
                <td class="center">Percentage</td>
                <td class="center">Quantity</td>
                <td style="background-color:#c6efce; white-space:nowrap;">
                    <span class="currency">
                        Rp
                    </span>
                    <span class="amount">
                        {{ number_format($sub_total, 0, '.', ',') }}
                    </span>
                </td>
            </tr>
            @php
                $no = count($process) + 1;
                $totalSubTotal = 0;
            @endphp
            @foreach ($other as $item)
                @php
                    $additionTypes = ['x_manufactur_cost', 'x_material_cost', 'blank_cost','x_sub_total'];

                    if (in_array($item->AdditionType, $additionTypes)) {
                        $totalSubTotal += $item->Estimate;
                    } elseif ($item->AdditionType === 'discount') {
                        $totalSubTotal -= $item->Estimate;
                    }
                @endphp
                <tr>
                    <td class="col-number">({{ $no++ }})</td>
                    <td colspan="2">{{ $item->NameItem }}</td>
                    <td class="center" style=" ">
                        @if ($item->Percentage == 0)
                            -
                        @else
                            {{ rtrim(rtrim(number_format((float) $item->Percentage, 2, '.', ','), '0'), '.') }}%
                        @endif
                    </td>
                    <td style="background-color:#f6f4aa; text-align: center;">
                        {{ str_replace('_', ' ', $item->AdditionType) }}
                        {{-- @if ($item->AdditionType == 'x_manufactur_cost')
                            X Manufactur Cost
                        @elseif($item->AdditionType == 'x_material_cost')
                            X Material Cost
                        @elseif($item->AdditionType == 'blank_cost')
                            Blank Cost
                        @else
                            -
                        @endif --}}
                    </td>
                    <td style="white-space:nowrap;">
                        <span class="currency">
                            Rp
                        </span>
                        <span class="amount">
                            {{ rtrim(rtrim(number_format((float) $item->Estimate, 2, '.', ','), '0'), '.') }}
                        </span>
                    </td>
                </tr>
            @endforeach
            @php
                $salesPrice = $totalSubTotal + $sub_total;
            @endphp
            <tr class="bold" style="background:#f2f2f2;">
                <td class="col-number">{{ $no++ }}</td>
                <td colspan="4" style="text-align: left;">Total (Sales Price)</td>
                <td style="white-space:nowrap;">
                    <span class="currency">
                        Rp
                    </span>
                    <span class="amount">
                        {{ number_format($salesPrice, 0, '.', ',') }}
                    </span>
                </td>
            </tr>

        </table>
    </div>
    <div>
        <table style="width:100%; font-size:12px;border:none;">
            <tr>
                <td style="width:30px; vertical-align:top; border:none;">a)</td>
                <td style="width:10px;border:none">:</td>
                <td style="vertical-align:top; width:400px; border:none; text-align: left;">Indonesian Rupiah</td>
            </tr>

            <tr>
                <td style="border:none;">b)</td>
                <td style="width:10px;border:none">:</td>
                <td style="border:none; text-align:left;">Volume Qty / month =
                    <span style="border:1px solid #000; background-color:#f6f4aa;; padding:2px 6px; font-weight:bold;">
                        {{ number_format($material->VolQty, 0) }}
                    </span>
                </td>
            </tr>
            <tr>
                <td style="border:none;">c)</td>
                <td style="width:10px;border:none">:</td>
                <td style="border:none;text-align: left;">Delivery : Franco PT Summit Adyawinsa Indonesia</td>
            </tr>

            <tr>
                <td style="border:none;">d)</td>
                <td style="width:10px;border:none">:</td>
                <td style="border:none;text-align: left;">Material : Sistem pembelian material adalah CPS dan akan di
                    update pada
                    ({{ \Carbon\Carbon::parse($material->EffectiveDate)->format('d M y') }}
                    -
                    {{ \Carbon\Carbon::parse($material->ExpiredDate)->format('d M y') }}
                    )
                </td>
            </tr>

            <tr>
                <td style="border:none;">e)</td>
                <td style="width:10px;border:none">:</td>
                <td style="border:none;text-align: left;">Harga material pada quotation ini adalah harga material untuk
                    masspro.
                </td>
            </tr>

            @if ($material->DepreciationQty && $material->DepreciationPrice)
                @php
                    $results = $material->DepreciationQty * $material->DepreciationPrice;
                @endphp
                <tr>
                    <td style="border:none;">f)</td>
                    <td style="width:10px;border:none">:</td>
                    <td style="border:none;text-align: left;">Volume amortization pallet/box yaitu
                        {{ number_format($material->DepreciationPrice, 0, ',', '.') }} (untuk part
                        Qty/car = 1) dan {{ number_format($results, 0, ',', '.') }} (untuk part Qty/car =
                        {{ number_format($material->DepreciationQty, 0, ',', '.') }}).Setelah itu pallet/box adalah
                        milik
                        SAI.
                    </td>
                </tr>
            @endif
        </table>
    </div>
    <div>
        <table style="width:100%; border-collapse: collapse; text-align:center;">
            {{-- <tr>
                <th colspan="2" style="border:1px solid #000; padding:2px;">
                    PT SUMMIT ADYAWINSA INDONESIA
                </th>
                <th colspan="3" style="border:1px solid #000; padding:2px;">
                    {{ strtoupper($header->SupplierName) }}
                </th>
            </tr> --}}
            {{-- <tr>
                <td height="60" style="border:1px solid #000;">
                </td>
                <td height="60" style="border:1px solid #000;">
                </td>
                <td height="60" style="border:1px solid #000;">
                </td>
                <td height="60" style="border:1px solid #000;">
                </td>
                <td height="60" style="border:1px solid #000;">

                </td>
            </tr>
            <tr>
                <td style="border:1px solid #000; padding:5px;">

                </td>
                <td style="border:1px solid #000; padding:5px;">

                </td>
                <td style="border:1px solid #000; padding:5px;">

                </td>
                <td style="border:1px solid #000; padding:5px;">

                </td>
                <td style="border:1px solid #000; padding:5px;">

                </td>
            </tr> --}}
        </table>

    </div>

</body>

</html>
