<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <style>
        body { font-size: 0.8rem }
        header { font-size: 0.9rem; font-weight: bold; }
        table { border-collapse: collapse; page-break-inside: auto; width: 100% }
        th, td { border: 1px solid grey; padding: 8px; vertical-align: top; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .grey-text { color: darkslategrey; }
        .bold-text { font-weight: bold; }
    </style>
</head>
<body>

    <table>
        <tr>
            <td style="border: none">
                <h3 style="margin-bottom: 2px; padding: 0">{{ $company->name }}</h3>
                <p style="margin: 0; padding: 0">{{ $company->address }}</p>
                <p style="margin: 0; padding: 0">
                    @if ($company->contact_1 || $company->contact_2)
                        <span>Contact: </span>
                        <span class="bold-text">{{ $company->contact_1 }}</span>
                        <span class="bold-text">{{ ($company->contact_1 && $company->contact_2) ? ', ' : '' }}</span>
                        <span class="bold-text">{{ $company->contact_2 }}</span>
                    @endif
                    <div>{{ $company->email }}</div>
                </p>
            </td>
            <td style="border: none" class="text-right">
                <header><h3 style="padding-top: 10px; padding: 0">Delivery Slip</h3></header>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="2" rowspan="2">
                <div style="margin-bottom: 2px">Deliver to</div>
                <div class="bold-text">{{ $record->toName }}</div>
                <div>{{ $record->toAddress }}</div>
                <div class="bold-text">
                    {{ $record->toContact1 }}
                    {{ ($record->toContact1 && $record->toContact2) ? ', ' : '' }}
                    {{ $record->toContact2 }}
                </div>
                <div>{{ $record->toEmail }}</div>
            </td>
            <td>
                <div>Date</div>
                <div class="bold-text">{{ $record->dateRaw }}</div>
            </td>
        </tr>
        <tr>
            <td style="width: 25%">
                <div>Delivery slip no</div>
                <div class="bold-text">{{ $record->sale_no }}</div>
            </td>
        </tr>
    </table>

    <table style="margin-top: 10px">
        <tr>
            <td class="bold-text" style="width: 1%">#</td>
            <td class="text-center bold-text">Lot number</td>
            <td class="bold-text" style="width: 30%">Product</td>
            <td class="text-right bold-text">Quantity (Nos)</td>
            <td class="text-left bold-text" style="width: 1%">Unit</td>
            <td class="text-right bold-text">Quantity (Kgs)</td>
            <td class="text-left bold-text" style="width: 1%">Unit</td>
        </tr>
        @foreach ($products as $index => $product)
        <tr>
            <td class="bold-text">{{ $index + 1 }}</td>
            <td class="text-center bold-text">{{ $product->lotNumber ? $product->lotNumber : '-' }}</td>
            <td>{{ $product->name }}</td>
            <td class="text-right bold-text">{{ $product->quantityRaw ? $product->quantityRaw : '-' }}</td>
            <td class="text-left">{{ $product->unit }}</td>
            <td class="text-right bold-text">{{ number_format($product->quantityRaw * $product->packing, 2) }}</td>
            <td class="text-left">KGS</td>
        </tr>
        @endforeach
    </table>

    <table style="margin-top: 10px">
        <tr>
            <td style="width: 60%">
                <div>Notes / remarks  </div>
                <div class="bold-text">{{ $record->transport_details ? $record->transport_details : '-' }}</div>
            </td>
            <td class="text-right">
                <div class="bold-text">For, Aadhya Distribution Co</div>
                <div style="margin-top: 30px">Authorised signatory</div>
            </td>
        </tr>
    </table>

    <footer>
        <script type="text/php">
            if (isset($pdf)) {
                $text = "page {PAGE_NUM} / {PAGE_COUNT}";
                $size = 10;
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
