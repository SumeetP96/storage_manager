<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <style>
        body { font-size: 0.75rem }
        header { font-size: 0.9rem; font-weight: bold; }
        table { border-collapse: collapse; page-break-inside: auto; width: 100% }
        th { border: 1px solid grey; padding: 4px; vertical-align: top; }
        td { border-left: 1px solid grey; border-right: 1px solid grey; padding: 5px 6px 0 6px; vertical-align: top; }
        tfoot > tr > td { border: 1px solid grey; padding: 4px; vertical-align: top; }
        .break > td { border-left: 1px solid grey; border-right: 1px solid grey; padding: 10px 0; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .grey-text { color: darkslategrey; }
        .bold-text { font-weight: bold; }
    </style>
</head>
<body>

    <table>
        <thead>
            <tr>
                <th class="text-right" style="width: 1%">Sr.</th>
                <th class="text-center" style="width: 1%">Lot number</th>
                <th class="text-left" style="width: 120px">Product</th>
                <th class="text-right">Qty (Nos)</th>
                <th class="text-center" style="width: 1%">Inward date</th>
                <th class="text-center" style="width: 1%">Outward date</th>
                <th class="text-right" style="width: 1%">Outward no</th>
                <th class="text-right" style="width: 1%">Mon</th>
                <th class="text-right">Weight (Kgs)</th>
                <th class="text-right" style="width: 1%">Rent</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($transfers as $key => $transfer)

                @foreach ($transfer as $trf)
                <tr>
                    <td class="text-right">{{ $trf['index'] }}</td>
                    <td class="text-center bold-text">{{ $key }}</td>
                    <td class="text-left">{{ $trf['name'] }}</td>
                    <td class="text-right">{{ $trf['quantity'] }}</td>
                    <td class="text-center">{{ $trf['inward_date'] }}</td>
                    <td class="text-center">{{ $trf['outward_date'] }}</td>
                    <td class="text-right {{ $trf['outward_no'] == 'Balance' ? 'bold-text' : '' }}">{{ $trf['outward_no'] }}</td>
                    <td class="text-right">{{ number_format($trf['month'], 1, '.', '') }}</td>
                    <td class="text-right">{{ $trf['packing'] }}</td>
                    <td class="text-right">{{ $trf['rent'] }}</td>
                    <td class="text-right">{{ $trf['amount'] }}</td>
                </tr>
                @endforeach

                <tr class="break"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
            @endforeach

            <tr>
                <td></td><td></td>
                <td class="bold-text" style="padding-bottom: 4px">Total</td>
                <td class="text-right bold-text" style="padding-bottom: 4px">{{ number_format($totals['quantity'], 2) }}</td>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="8"></td>
                <td colspan="3">
                    <table>
                        <tr>
                            <td style="border: none">Total</td>
                            <td class="text-right" style="border: none">{{ number_format($totals['total'], 2) }}</td>
                        </tr>
                        <tr>
                            <td style="border: none">Loading</td>
                            <td class="text-right" style="border: none">{{ number_format($totals['loading'], 2) }}</td>
                        </tr>
                        <tr>
                            <td style="border: none; padding-bottom: 6px">Unloading</td>
                            <td class="text-right" style="border: none; padding-bottom: 6px">{{ number_format($totals['unloading'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="bold-text" style="border:none; border-top: 1px solid grey; font-size: 0.8rem">
                                Bill Amount
                            </td>
                            <td class="bold-text text-right" style="border:none; border-top: 1px solid grey; font-size: 0.8rem">
                                {{ number_format($totals['total'] + $totals['loading'] + $totals['unloading'], 2) }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tfoot>
    </table>

    <footer>
        <script type="text/php">
            if (isset($pdf)) {
                $text = "{PAGE_NUM} of {PAGE_COUNT}";
                $size = 9;
                $font = $fontMetrics->getFont("Verdana");
                $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
                $x = ($pdf->get_width() - $width);
                $y = $pdf->get_height() - 35;
                $pdf->page_text($x, $y, $text, $font, $size);
            }
        </script>
    </footer>
</body>
</html>
