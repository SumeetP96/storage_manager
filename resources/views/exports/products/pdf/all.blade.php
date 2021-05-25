<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <style>
        table { border-collapse: collapse; page-break-inside: auto; width: 100% }
        th, td { border: 1px solid grey; padding: 8px; vertical-align: top; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .grey-text { color: darkslategrey; }
    </style>
</head>
<body>
    <header><h3>Storage Manager - Products</h3></header>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 1%">#</th>
                <th class="text-left" style="width: 40%">Name</th>
                <th class="text-center">Lot</th>
                <th class="text-center">Unit</th>
                <th class="text-center" style="width: 10%">C Unit</th>
                <th class="text-center">Updated</th>
                <th class="text-center">Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $index => $record)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <span>
                        {{ $record->name }}
                        @if ($record->alias) ({{ $record->alias }}) @endif
                    </span>
                    <div class="grey-text">{{ $record->remarks }}</div>
                </td>
                <td class="text-center">{{ $record->lot_number }}</td>
                <td class="text-center">{{ $record->unit }}</td>
                <td class="text-center">{{ $record->compound_unit }} ({{ $record->packing / 100 }})</td>
                <td class="text-center">{{ date('d/m/Y', strtotime($record->updated_at)) }}</td>
                <td class="text-center">{{ date('d/m/Y', strtotime($record->created_at)) }}</td>
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
