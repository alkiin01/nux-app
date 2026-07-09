<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Summary Update Quotation Supplier</title>
</head>

<body style="margin:0; padding:0; font-family: Arial, Helvetica, sans-serif; font-size:10px;">
    <table width="100%" style="background-color:#d7e6ff; font-weight:bold; padding:10px;">
        <tr>
            <td width="20%" align="left">
                <img src="{{ public_path('brand/' . $head->Logo) }}" width="100">
            </td>
            <td width="60%" align="center">
                <h2 style="margin:0; text-decoration:underline;">
                    SUMMARY UPDATE QUOTATION SUPPLIER
                </h2>
                <div>{{ strtoupper($supplier->Name) }}</div>
            </td>
            <td width="20%">
                &nbsp;
            </td>
        </tr>
    </table>
    <table style="width:100%; height:10px;">
        <tr>
            <td></td>
        </tr>
    </table>
    <table style="width:100%; border-collapse:collapse; font-size:10px;">
        <tr>
            <td style="width:60%; vertical-align:top;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr style="height:18px;">
                        <td style="width:30px;">To</td>
                        <td style="width:10px;">:</td>
                        <td style="width:100%; white-space:nowrap;">
                            PT Summit Adyawinsa Indonesia
                        </td>
                    </tr>
                    <tr style="height:18px;">
                        <td style="width:30px;">Attn</td>
                        <td style="width:10px;">:</td>
                        <td style="width:100%; white-space:nowrap;">
                            Mr Yoni Hendriana
                        </td>
                    </tr>
                </table>
            </td>

            <td style="width:40%; vertical-align:top;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr style="height:18px;">
                        <td style="width:90px;">No. Quotation</td>
                        <td style="width:10px;">:</td>
                        <td style="width:100%; white-space:nowrap;">
                            {{ str_replace('/QUO/', '/SUM/QUO/', $head->LegalNumber) }}
                        </td>
                    </tr>
                    <tr style="height:18px;">
                        <td style="width:90px;">Date</td>
                        <td style="width:10px;">:</td>
                        <td style="width:100%; white-space:nowrap;">
                            {{ \Carbon\Carbon::parse($head->UpdatedAt ?? $head->CreatedAt)->format('d-M-Y') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table style="width:100%; height:10px;">
        <tr>
            <td></td>
        </tr>
    </table>
    <div>
        Dear Sirs,<br>
        We would like to thank you for your constant procurement to our company.<br>
        We are pleased to submit our price quotation as follows :
    </div>
    <div style="margin-top:10px;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background-color:#d7e6ff;">
                    <th rowspan="2" style="border:1px solid black; text-align: center; width:3%;">No</th>
                    <th rowspan="2" style="border:1px solid black; text-align: center; width:12%;">Part No</th>
                    <th rowspan="2" style="border:1px solid black; text-align: center; width:18%;">Part Name</th>
                    <th rowspan="2" style="border:1px solid black; text-align: center; width:8%;">Customer</th>
                    <th rowspan="2" style="border:1px solid black; text-align: center; width:15%;">Raw Material</th>
                    <th rowspan="2" style="border:1px solid black; text-align: center; width:15%;">Spec Material</th>
                    <th rowspan="2" style="border:1px solid black; text-align: center; width:12%;">Old Price</th>
                    <th colspan="1" style="border:1px solid black; text-align: center; width:10%;">Update Price</th>
                    <th colspan="1" style="border:1px solid black; text-align: center; width:5%;">%</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $No = 1;
                    $totalPercent = 0;
                    $count = 0;
                @endphp

                @foreach ($table as $item)
                    @php
                        $old = (float) $item->PassTotalSalesPrice;
                        $new = (float) $item->CurrentTotalSalesPrice;
                        $percent = $old > 0 ? (($new - $old) / $old) * 100 : 0;
                        $totalPercent += $percent;
                        $count++;
                    @endphp

                    <tr>
                        <th style="border:1px solid black; font-size: 9px; width:3%; text-align: center;">{{ $No++ }}</th>
                        <th style="border:1px solid black; font-size: 9px; width:12%;">{{ $item->PartFG }}</th>
                        <th style="border:1px solid black; font-size: 9px; width:18%;">{{ $item->PartFGDesc }}</th>
                        <th style="border:1px solid black; font-size: 9px; width:8%;">{{ $item->Customer }}</th>
                        <th style="border:1px solid black; font-size: 9px; width:15%;">{{ $item->PartMtl }}</th>
                        <th style="border:1px solid black; font-size: 9px; width:15%;">{{ $item->PartMtlDesc }}</th>

                        <th style="border:1px solid black; font-size: 9px; width:12%; text-align:right;">
                            Rp. {{ number_format($old, 2, '.', ',') }}
                        </th>

                        <th style="border:1px solid black; font-size: 9px; width:10%; text-align:right;">
                            Rp. {{ number_format($new, 2, '.', ',') }}
                        </th>

                        <th style="border:1px solid black; font-size: 9px; width:5%; text-align:center;">
                            {{ number_format($percent, 2) }}%
                        </th>
                    </tr>
                @endforeach

                @php
                    $avgPercent = $count > 0 ? $totalPercent / $count : 0;
                @endphp

                <tr>
                    <th colspan="8" style="border:1px solid black; font-size: 9px; text-align:right;">
                        AVERAGE
                    </th>
                    <th style="border:1px solid black; font-size: 9px; text-align:center;">
                        {{ number_format($avgPercent, 2) }}%
                    </th>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="margin-top:10px;">
        <strong>Note :</strong>
        <ol>
            <li>Price : Exclude PPN 11%</li>
            <li>Payment : 30 Days</li>
            <li>Validity : {{ \Carbon\Carbon::parse($item->EffectiveDate)->format('d-M-Y') }} to
                {{ \Carbon\Carbon::parse($item->ExpiredDate)->format('d-M-Y') }}</li>
            <li>RM Price : CPS</li>
        </ol>
        Your prompt confirmation by re-email this quotation is highly appreciated.<br>Thank you and best regards.
    </div>
    <table style="width:100%; height:10px;">
        <tr>
            <td></td>
        </tr>
    </table>
    <table style="width:100%; border-collapse:collapse; margin-top:15px;">
        <tr>
            <td style="width:20%;"></td>

            <td style="width:80%;">
                <table style="width:100%; border-collapse:collapse; font-size:10px;">
                    <tr>
                        <td style="width:50%; padding-right:5px;">
                            <!-- PT Summit -->
                            <table style="width:100%; border-collapse:collapse; border:1px solid #000;">
                                <tr>
                                    <td colspan="2"
                                        style="background-color:#5b9bd5; color:#fff; text-align:center; font-weight:bold; padding:6px; border:1px solid black;">
                                        {{ strtoupper('PT Summit Adyawinsa Indonesia') }}
                                    </td>
                                </tr>
                                <tr style="font-weight:bold; border:1px solid black;">
                                    
                                    <td style="width:50%; text-align:center; padding:6px; border:1px solid black;">
                                        Approved by
                                    </td>
                                    <td style="width:50%; text-align:center; padding:6px; border-right:1px solid #000; border:1px solid black;">
                                        Legalized By
                                    </td>
                                </tr>
                                <tr>
                                    
                                    <td style="height:50px; text-align:center">
                                        <div style="height:50px; display:flex; align-items:center; justify-content:center;">
                                            @if (!empty($approval->Approval_2))
                                            <img src="{{ public_path('ttd_pa_yoni.png') }}" width="40" height="40">
                                            @else
                                            <img src="{{ public_path('ttd_404.png') }}" width="40" height="40">
                                            @endif
                                        </div>
                                    </td>
                                    <td style="height:50px; border-left:1px solid #000; text-align:center">
                                        <div style="height:50px; display:flex; align-items:center; justify-content:center;">
                                            @if (!empty($approval->Approval_3))
                                            <img src="{{ public_path('ttd_pak_eka.png') }}" width="40" height="40">
                                            @else
                                            <img src="{{ public_path('ttd_404.png') }}" width="40" height="40">
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    
                                    <td style="text-align:center; font-weight:bold; border-right:1px solid #000;">
                                        @if (!empty($approval->Approval_2))
                                        Yoni Hendriana
                                        @endif
                                    </td>
                                    <td style="text-align:center; font-weight:bold; border-right:1px solid #000;">
                                        @if (!empty($approval->Approval_3))
                                        Mr. Eka Novianto
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    
                                    <td style="text-align:center; font-style:italic; border:1px solid black;">
                                        Purchasing Dept Head
                                    </td>
                                    <td style="text-align:center; font-style:italic; border:1px solid black;">
                                        Vice President Director
                                    </td>
                                </tr>
                                <tr>
                                   
                                    <td style="text-align:center; font-style:italic; border:1px solid black;">
                                        @if (!empty($approval->Approval_2))
                                        {{ \Carbon\Carbon::parse($approval->Create_2)->format('d-M-Y') }}
                                        @endif
                                    </td>
                                    <td style="text-align:center; font-style:italic; border:1px solid black;">
                                        @if (!empty($approval->Approval_3))
                                        {{ \Carbon\Carbon::parse($approval->Create_3)->format('d-M-Y') }}
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>

                        <td style="width:50%; padding-left:5px;">
                            <table style="width:100%; border-collapse:collapse; border:1px solid #000;">
                                <tr>
                                    <td colspan="2"
                                        style="background-color:#5b9bd5; color:#fff; text-align:center; font-weight:bold; padding:6px; border:1px solid black;">
                                        {{ strtoupper($supplier->Name) }}
                                    </td>
                                </tr>
                                <tr style="font-weight:bold; border:1px solid black;">
                                    <td style="width:50%; text-align:center; padding:6px; border:1px solid #000;">
                                        Prepared by
                                    </td>
                                    <td style="width:50%; text-align:center; padding:6px; border:1px solid black;">
                                        Approved by
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height:50px; border-right:1px solid #000; text-align: center;">
                                        <div style="height:50px; display:flex; align-items:center; justify-content:center;">
                                            @if (!empty($head->full_name))
                                            <img src="https://vendor.summitadyawinsa.co.id/public/signature/{{ $head->signature }}" height="40" width="40">
                                            @endif
                                        </div>
                                    </td>
                                    <td style="height:50px; border-right:1px solid #000; text-align: center;">
                                        <div style="height:50px; display:flex; align-items:center; justify-content:center;">
                                            @if (!empty($approval->Approval_1))
                                        <img src="https://vendor.summitadyawinsa.co.id/public/signature/{{ $approval->signature }}" height="40" width="40">
                                        @endif
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align:center; font-weight:bold; border-right:1px solid #000;">
                                        {{ $head->full_name }}
                                    </td>
                                    <td style="text-align:center; font-weight:bold; border-right:1px solid #000;">
                                        @if (!empty($approval->Approval_1))
                                        {{ $approval->Approval_1 }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align:center; font-style:italic; border:1px solid black;">
                                        Staff
                                    </td>
                                    <td style="text-align:center; font-style:italic; border:1px solid black;">
                                        Manager
                                    </td>
                                </tr>
                                <tr>
                                    <td style="text-align:center; font-style:italic; border:1px solid black;">
                                        {{ \Carbon\Carbon::parse($head->CreatedAt)->format('d-M-Y') }}
                                    </td>
                                    <td style="text-align:center; font-style:italic; border:1px solid black;">
                                        @if (!empty($approval->Approval_1))
                                        {{ \Carbon\Carbon::parse($approval->Create_1)->format('d-M-Y') }}
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
