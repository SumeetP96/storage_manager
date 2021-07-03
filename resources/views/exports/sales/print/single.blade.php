@extends('layouts.export')

@section('content')
<header><h3>Storage Manager - Sale Details</h3></header>

<table>
    <tr>
        <td style="width: 25%">
            <div class="heading">Sale no</div>
            <div class="font-bold">{{ $record->sale_no }}</div>
        </td>
        <td style="width: 25%">
            <div class="heading">Date</div>
            <div class="font-bold">{{ date('d-m-Y', strtotime($record->created_at)) }}</div>
        </td>
        <td style="width: 25%">
            <div class="heading">Invoice no</div>
            <div class="font-bold">{{ $record->invoice_no ? $record->invoice_no : '-' }}</div>
        </td>
        <td style="width: 25%">
            <div class="heading">Outward no</div>
            <div class="font-bold">{{ $record->order_no ? $record->order_no : '-' }}</div>
        </td>
    </tr>

    <tr>
        <td style="width: 50%" colspan="2">
            <div class="heading">From Account</div>
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

    <tr>
        <td colspan="4">
            <span class="heading">Agent : </span>
            <span class="font-bold">{{ $record->agentName ? $record->agentName : '-' }}</span>
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

    @php
        $totalQty = 0;
        $totalKgs = 0;
    @endphp
    @foreach ($products as $index => $product)
        @php
            $totalQty += $product->quantityRaw;
            $totalKgs += $product->quantityKgs;
        @endphp
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
    <tr>
        <td></td>
        <td colspan="5" class="font-bold">Total</td>
        <td class="text-right font-bold">{{ number_format($totalQty, 2) }}</td>
        <td></td>
        <td class="text-right font-bold">{{ number_format($totalKgs, 2) }}</td>
        <td></td>
    </tr>
</table>

<table style="margin-top: 10px">
    <tr>
        <td>
            <span class="font-bold">Transport : </span>
            <span class="">{{ $record->transport_details ? $record->transport_details : '-' }}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="font-bold">Remarks : </span>
            <span>{{ $record->remarks ? $record->remarks : '-' }}</span>
        </td>
    </tr>
</table>
@endsection
