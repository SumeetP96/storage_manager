@extends('layouts.export')

@section('content')
<header>Storage Manager - Product Movement</header>

<table>
    <thead>
        <tr>
            <th class="text-center" style="width: 1%">#</th>
            <th class="text-center" style="width: 12%">Date</th>
            <th class="text-center">Transfer</th>
            <th class="text-left" style="width: 20%">From</th>
            <th class="text-left" style="width: 20%">To</th>
            <th class="text-right">C Qty</th>
            <th class="text-left">C Unit</th>
            <th class="text-right">Qty</th>
            <th class="text-left" style="width: 1%">Unit</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $index => $record)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>

            <td class="text-center font-bold">{{ date('d-m-Y', strtotime($record->date)) }}</td>

            <td class="text-center">{{ $record->transferType }}</td>

            <td>{{ $record->fromName }}</td>

            <td>{{ $record->toName }}</td>

            <td class="text-right font-bold">{{ $record->compoundUnit ? $record->compoundQuantity / 100 : '-' }}</td>

            <td>{{ $record->compoundUnit ? $record->compoundUnit . ' (' . $record->packing / 100 . ')' : '-' }}</td>

            <td class="text-right font-bold">{{ number_format($record->quantity / 100, 2) }}</td>

            <td>{{ $record->unit }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
