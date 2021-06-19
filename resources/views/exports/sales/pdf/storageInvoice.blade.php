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
        th, td { border: 1px solid grey; padding: 6px 8px; vertical-align: top; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .grey-text { color: darkslategrey; }
        .bold-text { font-weight: bold; }
        .invalid { color: red; }
    </style>
</head>
<body>
    <h3 class="text-center" style="margin: 0; padding: 0">Storage Invoice</h3>
    <table style="margin-top: 10px">
        <tr>
            <td colspan="2" rowspan="2" style="padding-bottom: 0">
                <div style="margin-bottom: 2px">Godown</div>
                <div class="bold-text">{{ $record->fromName }}</div>
                <div>{{ $record->fromAddress }}</div>
                <div class="bold-text">
                    {{ $record->fromContact1 }}
                    {{ ($record->fromContact1 && $record->fromContact2) ? ', ' : '' }}
                    {{ $record->fromContact2 }}
                </div>
                <div>{{ $record->fromEmail }}</div>
            </td>
            <td>
                <div>Outward date</div>
                <div class="bold-text">{{ $record->dateRaw }}</div>
            </td>
        </tr>
        <tr>
            <td style="width: 25%">
                <div>Sale no</div>
                <div class="bold-text">{{ $record->sale_no }}</div>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <th class="text-left" style="width: 1%">#</th>
            <th class="text-center">Lot no</th>
            <th class="text-left">Product</th>
            <th class="text-center">Inw date</th>
            <th class="text-center" style="width: 1%">Mon</th>
            <th class="text-right">Qty Nos</th>
            <th class="text-right">Qty Kgs</th>
            <th class="text-right">Rent</th>
            <th class="text-right">Amount</th>
        </tr>

        @if ($products->count() > 0)
        @foreach ($products as $index => $product)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td class="text-center bold-text">{{ $product->lotNumber }}</td>
            <td>{{ $product->name }}</td>
            <td class="text-center {{ $product->months < 0 ? 'invalid bold-text' : '' }}">
                {{ date('d/m/Y', strtotime($product->inwardDate)) }}
            </td>
            <td class="text-center {{ $product->months < 0 ? 'invalid bold-text' : '' }}">
                {{ number_format($product->months, 1) }}
            </td>
            <td class="text-right bold-text">{{ number_format($product->compoundQuantity / 100, 2) }}</td>
            <td class="text-right">{{ number_format($product->quantity / 100, 2) }}</td>
            <td class="text-right">{{ number_format($product->rent / 100, 2) }}</td>
            <td class="text-right bold-text {{ $product->months < 0 ? 'invalid' : '' }}">
                {{ number_format($product->amount, 2) }}
            </td>
        </tr>
        @endforeach
        @endif
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
