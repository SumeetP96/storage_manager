@extends('layouts.export')

@section('content')
<header>Storage Manager - All Transfers</header>

<table>
    <thead>
        <tr>
            <th class="text-center" style="width: 1%">#</th>
            <th class="text-center" style="width: 12%">Date</th>
            <th class="text-center">Transfer</th>
            <th class="text-center">Reference no</th>
            <th class="text-left" style="width: 20%">From</th>
            <th class="text-left" style="width: 20%">To</th>
            <th class="text-center">Updated on</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $index => $record)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>

            <td class="text-center font-bold">{{ date('d-m-Y', strtotime($record->date)) }}</td>

            <td class="text-center">{{ $record->transferType }}</td>

            <td class="text-center font-bold">{{ $record->invoiceNo ? $record->invoiceNo : '-' }}</td>

            <td>{{ $record->fromName }}</td>

            <td>{{ $record->toName }}</td>

            <td class="text-center">{{ date('d-m-Y', strtotime($record->updated_at)) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
