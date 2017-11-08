@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h3 class="title is-3">Unaccepted Applications</h3>
  </div>
</div>
<div class="columns is-centered">
    <div class="column is-three-quarters">
        <table id="data-table" class="table is-narrow">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Course Number</th>
                    <th>Course Title</th>
                    <th>Academic</th>
                    <th>Request Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($applications as $application)
                    <tr>
                        <td>{{$application->student->fullName}}</td>
                        <td>{{$application->request->course->code}}</td>
                        <td>{{$application->request->course->title}}</td>
                        <td>{{$application->request->staff->fullName}}</td>
                        <td>{{$application->request->type}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>                
    </div>
</div>
@endsection