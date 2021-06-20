@extends('layouts.export')

@section('content')
<header>Storage Manager - Purchases</header>

<table>
    <thead>
        <tr>
            <th class="text-center" style="width: 1%">#</th>
            <th class="text-left">Purc no</th>
            <th class="text-left">Invoice</th>
            <th class="text-left" style="min-width: 150px">From account</th>
            <th class="text-left" style="min-width: 150px">To godown</th>
            <th class="text-left" style="min-width: 125px">Agent & remarks</th>
            <th class="text-right">Updated at</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $index => $record)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="text-left font-bold">{{ $record->purchase_no }}</td>
            <td class="text-left">
                <div class="font-bold">{{ date('d/m/Y', strtotime($record->date)) }}</div>
                <div class="grey-text">{{ $record->invoiceNo }}</div>
            </td>
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
