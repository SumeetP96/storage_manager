@extends('layouts.export')

@section('content')
<header>Storage Manager - Inter Godown Transfers</header>

<table>
    <thead>
        <tr>
            <th class="text-center" style="width: 1%">#</th>
            <th class="text-left">Date</th>
            <th class="text-left">Transfer no</th>
            <th class="text-left" style="width: 25%">From godown</th>
            <th class="text-left" style="width: 25%">To godown</th>
            <th class="text-left">Remarks</th>
            <th class="text-right" style="width: 1%">Updated at</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $index => $record)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="font-bold">{{ date('d/m/Y', strtotime($record->date)) }}</td>
            <td>{{ $record->inter_godown_no }}</td>
            <td class="text-left">{{ $record->fromName }}</td>
            <td class="text-left">{{ $record->toName }}</td>
            <td class="text-left">{{ $record->remarks ? $record->remarks : '-' }}</td>
            <td class="text-right">{{ date('d/m/Y', strtotime($record->updated_at)) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
