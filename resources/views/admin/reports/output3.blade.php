@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h3 class="title is-3">Working Students</h3>
  </div>
</div>
<div class="columns is-centered">
    <div class="column is-three-quarters">
        <table id="data-table" class="table is-narrow is-fullwidth">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Student Email</th>
                    <th>RTW Email Sent</th>
                    <th>Contract Email Sent</th>
                    <th>Total No. of Hours</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $student)
                    <tr>
                        <td>{{$student->fullName}}</td>
                        <td>{{$student->email}}</td>
                        <td>{{$student->getDateOf('StudentRTWReceived')}}</td>
                        <td>{{$student->getDateOf('StudentContractReady')}}</td>
                        <td>{{$student->getTotalConfirmedHours()}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>                
    </div>
</div>
@endsection