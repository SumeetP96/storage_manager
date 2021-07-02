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
    <header>Storage Manager - Inter Godown Transfer Details</header>

    <table>
        <tr>
            <td style="width: 50%" colspan="2">
                <div class="heading">Transfer no</div>
                <div class="font-bold">{{ $record->inter_godown_no }}</div>
            </td>
            <td style="width: 50%" colspan="2">
                <div class="heading">Date</div>
                <div class="font-bold">{{ date('d-m-Y', strtotime($record->created_at)) }}</div>
            </td>
        </tr>

        <tr>
            <td style="width: 50%" colspan="2">
                <div class="heading">From Godown</div>
                <div class="font-bold">{{ $record->fromName }}</div>
                <div>{{ $record->fromAddress }}</div>
                <div>
                    {{ $record->fromContact1 }}
                    {{ ($record->fromContact1 && $record->fromContact2) ? ', ' : '' }}
                    {{ $record->fromContact2 }}
                </div>
                <div>{{ $record->fromEmail }}</div>
            </td>

            <td style="width: 50%" colspan="2">
                <div class="heading">To Godown</div>
                <div class="font-bold">{{ $record->toName }}</div>
                <div>{{ $record->toAddress }}</div>
                <div>
                    {{ $record->toContact1 }}
                    {{ ($record->toContact1 && $record->toContact2) ? ', ' : '' }}
                    {{ $record->toContact2 }}
                </div>
                <div>{{ $record->toEmail }}</div>
            </td>
        </tr>
    </table>

    <table style="margin-top: 10px">
        <tr>
            <td class="font-bold" style="width: 1%">#</td>
            <td class="font-bold" style="width: 30%">Products</td>
            <td class="text-center font-bold">Lot number</td>
            <td class="text-center font-bold">Rent</td>
            <td class="text-center font-bold">Loading</td>
            <td class="text-center font-bold">Unloading</td>
            <td class="text-right font-bold">Quantity (Nos)</td>
            <td class="text-left font-bold">Unit</td>
            <td class="text-right font-bold">Quantity (Kgs)</td>
            <td class="text-left font-bold" style="width: 1%">Unit</td>
        </tr>
        @foreach ($products as $index => $product)
        <tr>
            <td class="font-bold">{{ $index + 1 }}</td>
            <td class="font-bold">{{ $product->name }}</td>
            <td class="text-center font-bold">{{ $product->lotNumber }}</td>
            <td class="text-right">{{ $product->rent ? number_format($product->rent / 100, 1) : '-' }}</td>
            <td class="text-right">{{ $product->loading ? number_format($product->loading / 100, 1) : '-' }}</td>
            <td class="text-right">{{ $product->unloading ? number_format($product->unloading / 100, 1) : '-' }}</td>
            <td class="text-right font-bold">{{ number_format($product->quantityRaw, 2) }}</td>
            <td class="text-left">
                {{ $product->unit }} <span>({{ number_format($product->packing / 100, 0) }})</span>
            </td>
            <td class="text-right font-bold">{{ number_format($product->quantityKgs, 2) }}</td>
            <td class="text-left">{{ $product->unit }}</td>
        </tr>
        @endforeach
    </table>

    <table style="margin-top: 10px">
        <tr>
            <td>
                <div class="font-bold">Transport details</div>
                <div class="">{{ $record->transport_details ? $record->transport_details : '-' }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="font-bold">Remarks </div>
                <div>{{ $record->remarks ? $record->remarks : '-' }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
