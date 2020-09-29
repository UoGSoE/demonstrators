@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h3 class="title is-3">Courses
        <admin-button href="{{route('admin.courses.create')}}" method="Add new" object="course"></admin-button>
    </h3>
  </div>
</div>
<div class="columns is-centered">
    <div class="column is-three-quarters">
        <table id="data-table" class="table is-narrow is-fullwidth">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Title</th>
                    <th># Staff</th>
                    <th># Requests</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($courses as $course)
                    <tr>
                        <td>{{$course->code}}</td>
                        <td>{{$course->title}}</td>
                        <td>{{$course->staff_count}}</td>
                        <td>{{$course->requests_count}}</td>
                        <td><a class="button is-small is-gla" href="{{route('admin.courses.edit', $course->id)}}">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
