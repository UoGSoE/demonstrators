@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h3 class="title is-3">Output 5 (No Applications)</h3>
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
                </tr>
            </thead>
            <tbody>
                @foreach ($requests as $request)
                    <tr>
                        <td>{{$request->course->code}}</td>
                        <td>{{$request->course->title}}</td>
                        <td>{{$request->staff->fullName}}</td>
                        <td>{{$request->staff->email}}</td>
                        <td>{{$request->type}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>                
    </div>
</div>
@endsection