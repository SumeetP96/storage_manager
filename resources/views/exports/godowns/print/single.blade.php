@extends('layouts.export')

@section('content')
    <div class="container">
        <header><h3>Storage Manager - {{ $godownType }} Details</h3></header>

        <table class="table table-bordered">
            <tr>
                <th class="text-left" style="width: 20%">Name</th>
                <td>{{ $record->name }}</td>
            </tr>

            <tr>
                <th class="text-left">Alias</th>
                <td>{{ $record->alias ? $record->alias : '-' }}</td>
            </tr>

            <tr>
                <th class="text-left">Address</th>
                <td>{{ $record->address ? $record->address : '-' }}</td>
            </tr>

            <tr>
                <th class="text-left">Contact 1</th>
                <td>{{ $record->contact_1 ? $record->contact_1 : '-' }}</td>
            </tr>

            <tr>
                <th class="text-left">Contact 2</th>
                <td>{{ $record->contact_2 ? $record->contact_2 : '-' }}</td>
            </tr>

            <tr>
                <th class="text-left">Email</th>
                <td>{{ $record->email ? $record->email : '-' }}</td>
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
    </div>
@endsection
