@extends('layouts.export')

@section('content')
<header>Storage Manager - Products Stock</header>

<table>
    <thead>
        <tr>
            <th class="text-center" style="width: 1%">#</th>
            <th class="text-left">Product</th>
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

            <td>{{ $record->name }}</td>

            <td class="text-right font-bold">{{ $record->compoundUnit ? $record->compoundStock : '-' }}</td>

            <td>{{ $record->compoundUnit ? $record->compoundUnit : '-' }}</td>

            <td class="text-right font-bold">{{ number_format($record->stock / 100, 2) }}</td>

            <td>{{ $record->unit }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
