@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h3 class="title is-3">Unseen Applications Older Than 3 Days</h3>
  </div>
</div>
<div class="columns is-centered">
    <div class="column is-three-quarters">
        <table id="data-table" class="table is-narrow">
            <thead>
                <tr>
                    <th>Course Number</th>
                    <th>Course Title</th>
                    <th>Academic</th>
                    <th>Email</th>
                    <th>Request Type</th>
                    <th>Start Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($applications as $application)
                    <tr>
                        <td>{{$application->request->course->code}}</td>
                        <td>{{$application->request->course->title}}</td>
                        <td>{{$application->request->staff->fullName}}</td>
                        <td>{{$application->request->staff->email}}</td>
                        <td>{{$application->request->type}}</td>
                        <td>{{$application->request->getFormattedStartDate()}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>                
    </div>
</div>
@endsection