@extends('layouts.export')

@section('content')
<header>Storage Manager - {{ $godownType }}</header>

<table>
    <thead>
        <tr>
            <th class="text-center" style="width: 1%">#</th>
            <th class="text-left" style="width: 30%">Name</th>
            <th class="text-left">Address</th>
            <th class="text-right">Contact</th>
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
            <td>
                {{ $record->address ? $record->address : '-' }}
                <div class="grey-text">{{ $record->email ? '('.$record->email . ')' : '' }}</div>
            </td>
            <td class="text-right">
                @if ($record->contact_1 || $record->contact_2)
                <div>{{ $record->contact_1 }}</div>
                <div>{{ $record->contact_2 }}</div>
                @else
                -
                @endif
            </td>
            <td class="text-center">{{ date('d/m/Y', strtotime($record->updated_at)) }}</td>
            <td class="text-center">{{ date('d/m/Y', strtotime($record->created_at)) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
