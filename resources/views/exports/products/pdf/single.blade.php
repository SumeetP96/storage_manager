<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <style>
        table { border-collapse: collapse; page-break-inside: auto; width: 100% }
        th, td { border: 1px solid grey; padding: 16px; vertical-align: top; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .grey-text { color: darkslategrey; }
    </style>
</head>
<body>
    <header><h3>Storage Manager - Product Details</h3></header>

    <table>
        <tr>
            <th class="text-left" style="width: 20%">Name</th>
            <td>{{ $record->name }}</td>
        </tr>

        <tr>
            <th class="text-left">Alias</th>
            <td>{{ $record->alias ? $record->alias : '-' }}</td>
        </tr>

        <tr>
            <th class="text-left">Lot number</th>
            <td>{{ $record->lot_number ? $record->lot_number : '-' }}</td>
        </tr>

        <tr>
            <th class="text-left">Unit</th>
            <td>{{ $record->unit }}</td>
        </tr>

        <tr>
            <th class="text-left">Compound unit</th>
            <td>{{ $record->compound_unit ? $record->compound_unit : '-' }}</td>
        </tr>

        <tr>
            <th class="text-left">Packing</th>
            @if ($record->packing)
            <td>
                {{ $record->packing / 100 }}
                <small class="grey-text">({{ $record->unit }})</small>
            </td>
            @else
            <td>-</td>
            @endif
        </tr>

        <tr>
            <th class="text-left">Remarks</th>
            <td>{{ $record->remarks ? $record->remarks : '-' }}</td>
        </tr>

        <tr>
            <th class="text-left">Updated at</th>
            <td>{{ date('d-m-Y H:i:s', strtotime($record->updated_at)) }}</td>
        </tr>

        <tr>
            <th class="text-left">Created at</th>
            <td>{{ date('d-m-Y H:i:s', strtotime($record->created_at)) }}</td>
        </tr>
    </table>
</body>
</html>
