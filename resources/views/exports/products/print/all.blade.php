@extends('layouts.export')

@section('content')
<header>Storage Manager - Products</header>

<table>
    <thead>
        <tr>
            <th class="text-center" style="width: 1%">#</th>
            <th class="text-left" style="width: 40%">Name</th>
            <th class="text-center">Unit</th>
            <th class="text-center" style="width: 10%">C Unit</th>
            <th class="text-center">Updated</th>
            <th class="text-center">Created</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $index => $record)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>
                <span>
                    {{ $record->name }}
                    @if ($record->alias) ({{ $record->alias }}) @endif
                </span>
                <div class="grey-text">{{ $record->remarks }}</div>
            </td>
            <td class="text-center">{{ $record->unit }}</td>
            <td class="text-center">{{ $record->compound_unit }} ({{ $record->packing / 100 }})</td>
            <td class="text-center">{{ date('d/m/Y', strtotime($record->updated_at)) }}</td>
            <td class="text-center">{{ date('d/m/Y', strtotime($record->created_at)) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
