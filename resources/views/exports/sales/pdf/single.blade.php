<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <style>
        table { border-collapse: collapse; page-break-inside: auto; width: 100% }
        th, td { border: 1px solid grey; padding: 10px 12px; vertical-align: top; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .grey-text { color: darkslategrey; }
        .font-bold { font-weight: bold }
        .heading { padding-bottom: 5px }
    </style>
</head>
<body>
    <header><h3>Storage Manager - Purchase Details</h3></header>

    <table>
        <tr>
            <td style="width: 25%">
                <div class="heading">Date</div>
                <div class="font-bold">{{ date('d-m-Y', strtotime($record->created_at)) }}</div>
            </td>
            <td style="width: 25%">
                <div class="heading">Invoice no</div>
                <div class="font-bold">{{ $record->invoice_no }}</div>
            </td>
            <td style="width: 25%">
                <div class="heading">Order no</div>
                <div class="font-bold">{{ $record->order_no }}</div>
            </td>
            <td style="width: 25%">
                <div class="heading">Eway Bill no</div>
                <div class="font-bold">{{ chunk_split($record->eway_bill_no, 4, ' ') }}</div>
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
                <div class="heading">To Account</div>
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

        <tr>
            <td colspan="4">
                <span class="heading">Agent : </span>
                <span class="font-bold">{{ $record->agentName }}</span>
            </td>
        </tr>
    </table>

    <table style="margin-top: 10px">
        <tr>
            <td class="font-bold" style="width: 1%">#</td>
            <td class="font-bold" style="width: 30%">Products</td>
            <td class="text-center font-bold">Lot number</td>
            <td class="text-right font-bold">C Quantity</td>
            <td class="text-left font-bold" style="width: 1%">Unit</td>
            <td class="text-right font-bold">Quantity</td>
            <td class="text-left font-bold" style="width: 1%">Unit</td>
        </tr>
        @foreach ($products as $index => $product)
        <tr>
            <td class="font-bold">{{ $index + 1 }}</td>
            <td class="font-bold">{{ $product->name }}</td>
            <td class="text-center">{{ $product->lotNumber ? $product->lotNumber : '-' }}</td>
            <td class="text-right font-bold">{{ $product->compoundQuantityRaw ? $product->compoundQuantityRaw : '-' }}</td>
            <td class="text-left">{{ $product->compoundUnit ? $product->compoundUnit : '-' }}</td>
            <td class="text-right font-bold">{{ number_format($product->quantityRaw, 2) }}</td>
            <td class="text-left">{{ $product->unit }}</td>
        </tr>
        @endforeach
    </table>

    <table style="margin-top: 10px">
        <tr>
            <td>
                <div class="heading">Transport details</div>
                <div>Delivery slip : <span class="font-bold">{{ $record->delivery_slip_no }}</span></div>
                <div>Delivered by : <span class="font-bold">{{ $record->transport_details }}</span></div>
            </td>
        </tr>
        <tr>
            <td>
                <span class="font-bold">Remarks :  </span>
                <span>{{ $record->remarks }}</span>
            </td>
        </tr>
    </table>
</body>
</html>
