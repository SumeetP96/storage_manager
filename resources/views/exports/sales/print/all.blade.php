@extends('layouts.export')

@section('content')
<header>Storage Manager - Sales</header>

<table>
    <thead>
        <tr>
            <th class="text-center" style="width: 1%">#</th>
            <th class="text-left">Invoice</th>
            <th class="text-left" style="width: 1%">Sale no</th>
            <th class="text-left" style="min-width: 200px">From account</th>
            <th class="text-left" style="min-width: 200px">To godown</th>
            <th class="text-left" style="min-width: 150px">Agent & remarks</th>
            <th class="text-right" style="width: 1%">Updated at</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $index => $record)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="text-left">
                <div class="font-bold">{{ date('d/m/Y', strtotime($record->date)) }}</div>
                <div class="grey-text">{{ $record->invoiceNo }}</div>
            </td>
            <td>{{ $record->sale_no }}</td>
            <td class="text-left">{{ $record->fromName }}</td>
            <td class="text-left">{{ $record->toName }}</td>
            <td class="text-left">
                @if ($record->agent || $record->remarks)
                {{ $record->agent }}
                <div class="grey-text">{{ $record->remarks }}</div>
                @else
                -
                @endif
            </td>
            <td class="text-right">{{ date('d/m/Y', strtotime($record->updated_at)) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
