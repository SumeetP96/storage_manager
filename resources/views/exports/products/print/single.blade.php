@extends('layouts.export')

@section('content')
<header>Storage Manager - Product Details</header>

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
@endsection
