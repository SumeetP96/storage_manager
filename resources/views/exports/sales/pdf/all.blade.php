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
        .bold-text { font-weight: bold; }
    </style>
</head>
<body>
    <header>Storage Manager - Sales</header>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 1%">#</th>
                <th class="text-left">Invoice</th>
                <th class="text-left" style="width: 25%">From godown</th>
                <th class="text-left" style="width: 25%">To account</th>
                <th class="text-left">Agent & remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $index => $record)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-left">
                    {{ date('d/m/Y', strtotime($record->date)) }}
                    <div class="grey-text">{{ $record->invoiceNo }}</div>
                </td>
                <td class="text-left">{{ $record->fromName }}</td>
                <td class="text-left">{{ $record->toName }}</td>
                <td class="text-left">
                    @if ($record->agent || $record->remarks)
                    {{ $record->agent }}
                    <div class="grey-text">{{ $record->remarks }}</div>
                    @else
                    -
                    @endif
                </td>
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
