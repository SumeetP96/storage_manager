@extends('layouts.export')

@section('content')
<header>Storage Manager - Product Details</header>

<table>
    <tr>
        <th class="text-left" style="width: 20%">Name</th>
        <td class="font-bold">{{ $record->name }}</td>
    </tr>

    <tr>
        <th class="text-left">Alias</th>
        <td>{{ $record->alias ? $record->alias : '-' }}</td>
    </tr>

    <tr>
        <th class="text-left">Unit</th>
        <td>{{ $record->unit }}</td>
    </tr>

    <tr>
        <th class="text-left">Packing</th>
        @if ($record->packing)
        <td>
            <span class="font-bold">{{ number_format($record->packing / 100) }}</span>
            <span>(KGS)</span>
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
@endsection
