@extends('layouts.export')

@section('content')
<header>Storage Manager - Inter Godown Transfers</header>

<table>
    <thead>
        <tr>
            <th class="text-center" style="width: 1%">#</th>
            <th class="text-left">Invoice</th>
            <th class="text-left" style="width: 25%">From godown</th>
            <th class="text-left" style="width: 25%">To godown</th>
            <th class="text-left">Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $index => $record)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="text-left">
                {{ date('d/m/Y', strtotime($record->date)) }}
                <div class="grey-text">{{ $record->invoiceNo }}</div>
            </td>
            <td class="text-left">{{ $record->fromName }}</td>
            <td class="text-left">{{ $record->toName }}</td>
            <td class="text-left">{{ $record->remarks ? $record->remarks : '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection