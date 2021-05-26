@extends('layouts.export')

@section('content')
    <div class="container">
        <header><h3>Storage Manager - Purchases</h3></header>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="text-center" style="width: 1%">#</th>
                    <th class="text-left">Invoice</th>
                    <th class="text-left" style="width: 25%">From account</th>
                    <th class="text-left" style="width: 25%">To godown</th>
                    <th class="text-left">Agent & remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $index => $record)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-left">
                        {{ date('d/m/Y', strtotime($record->date)) }}
                        <div class="text-secondary">{{ $record->invoiceNo }}</div>
                    </td>
                    <td class="text-left">{{ $record->fromName }}</td>
                    <td class="text-left">{{ $record->toName }}</td>
                    <td class="text-left">
                        @if ($record->agent || $record->remarks)
                        {{ $record->agent }}
                        <div class="text-secondary">{{ $record->remarks }}</div>
                        @else
                        -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
