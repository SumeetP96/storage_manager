@extends('layouts.export')

@section('content')
    <div class="container">
        <header><h3>Storage Manager - Sale Details</h3></header>

        <table class="table table-bordered">
            <tr>
                <td style="width: 25%">
                    <div class="heading">Date</div>
                    <div class="font-weight-bold">{{ date('d-m-Y', strtotime($record->created_at)) }}</div>
                </td>
                <td style="width: 25%">
                    <div class="heading">Invoice no</div>
                    <div class="font-weight-bold">{{ $record->invoice_no ? $record->invoice_no : '-' }}</div>
                </td>
                <td style="width: 25%">
                    <div class="heading">Order no</div>
                    <div class="font-weight-bold">{{ $record->order_no ? $record->order_no : '-' }}</div>
                </td>
                <td style="width: 25%">
                    <div class="heading">Eway Bill no</div>
                    <div class="font-weight-bold">{{ $record->eway_bill_no ? chunk_split($record->eway_bill_no, 4, ' ') : '-' }}</div>
                </td>
            </tr>

            <tr>
                <td style="width: 50%" colspan="2">
                    <div class="heading">From Godown</div>
                    <div class="font-weight-bold">{{ $record->fromName }}</div>
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
                    <div class="font-weight-bold">{{ $record->toName }}</div>
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
                    <span class="font-weight-bold">{{ $record->agentName ? $record->agentName : '-' }}</span>
                </td>
            </tr>
        </table>

        <table class="table table-bordered" style="margin-top: 10px">
            <tr>
                <td class="font-weight-bold" style="width: 1%">#</td>
                <td class="font-weight-bold" style="width: 30%">Products</td>
                <td class="text-center font-weight-bold">Lot number</td>
                <td class="text-right font-weight-bold">C Quantity</td>
                <td class="text-left font-weight-bold" style="width: 1%">Unit</td>
                <td class="text-right font-weight-bold">Quantity</td>
                <td class="text-left font-weight-bold" style="width: 1%">Unit</td>
            </tr>
            @foreach ($products as $index => $product)
            <tr>
                <td class="font-weight-bold">{{ $index + 1 }}</td>
                <td class="font-weight-bold">{{ $product->name }}</td>
                <td class="text-center">{{ $product->lotNumber ? $product->lotNumber : '-' }}</td>
                <td class="text-right font-weight-bold">{{ $product->compoundQuantityRaw ? $product->compoundQuantityRaw : '-' }}</td>
                <td class="text-left">{{ $product->compoundUnit ? $product->compoundUnit : '-' }}</td>
                <td class="text-right font-weight-bold">{{ number_format($product->quantityRaw, 2) }}</td>
                <td class="text-left">{{ $product->unit }}</td>
            </tr>
            @endforeach
        </table>

        <table class="table table-bordered" style="margin-top: 10px">
            <tr>
                <td>
                    <div class="heading">Transport details</div>
                    <div>Delivery slip : <span class="font-weight-bold">{{ $record->delivery_slip_no ? $record->delivery_slip_no : '-' }}</span></div>
                    <div>Delivered by : <span class="font-weight-bold">{{ $record->transport_details ? $record->transport_details : '-' }}</span></div>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="font-weight-bold">Remarks :  </span>
                    <span>{{ $record->remarks ? $record->remarks : '-' }}</span>
                </td>
            </tr>
        </table>
    </div>
@endsection
