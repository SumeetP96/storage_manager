@extends('layouts.export')

@section('content')
<header>Storage Manager - Product Lot Stock</header>

<table>
    <thead>
        <tr>
            <th class="text-center" style="width: 1%">#</th>
            <th class="text-center">Lot number</th>
            <th class="text-left">Product</th>
            <th class="text-right">C Stock</th>
            <th class="text-left" style="width: 12%">C Unit</th>
            <th class="text-right">Stock</th>
            <th class="text-left" style="width: 1%">Unit</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $index => $record)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>

            <td class="text-center">{{ $record->lotNumber ? $record->lotNumber : 'Unassigned' }}</td>

            <td>{{ $record->name }}</td>

            <td class="text-right font-bold">{{ $record->compoundUnit ? $record->compoundStock : '-' }}</td>

            <td>{{ $record->compoundUnit ? $record->compoundUnit . ' (' . $record->packing / 100 . ')' : '-' }}</td>

            <td class="text-right font-bold">{{ number_format($record->currentStock / 100, 2) }}</td>

            <td>{{ $record->productUnit }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
