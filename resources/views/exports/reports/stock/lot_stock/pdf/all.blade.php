<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <style>
        body { font-size: 0.8rem }
        header { font-size: 0.9rem; margin-bottom: 10px; font-weight: bold; }
        table { border-collapse: collapse; page-break-inside: auto; width: 100% }
        th, td { border: 1px solid grey; padding: 8px; vertical-align: top; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .grey-text { color: darkslategrey; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <header>Storage Manager - Lot Stock</header>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 1%">#</th>
                <th class="text-center">Lot number</th>
                <th class="text-right">Compound stock</th>
                <th class="text-left">C Unit</th>
                <th class="text-right">Stock</th>
                <th class="text-left">Unit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $index => $record)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>

                <td class="text-center">{{ $record->lotNumber ? $record->lotNumber : 'Unassigned' }}</td>

                <td class="text-right font-bold">{{ $record->compoundUnit ? $record->compoundStock : '-' }}</td>

                <td>{{ $record->compoundUnit ? $record->compoundUnit : '-' }}</td>

                <td class="text-right font-bold">{{ number_format($record->stock / 100, 2) }}</td>

                <td>{{ $record->unit }}</td>
            </tr>
            @endforeach
        </tbody>
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