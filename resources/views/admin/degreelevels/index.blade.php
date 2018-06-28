@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h3 class="title is-3">Degree Levels
        <admin-button href="{{route('admin.degreelevels.create')}}" method="Add new" object="degree level"></admin-button>
    </h3>
  </div>
</div>
<div class="columns is-centered">
    <div class="column is-three-quarters">
        <table id="data-table" class="table is-narrow is-fullwidth">
            <thead>
                <tr>
                    <th>Degree Level</th>
                    <th>Number of Requests</th>
                    <th>Number of Students</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($degreeLevels as $degreeLevel)
                    <tr>
                        <td>{{$degreeLevel->title}}</td>
                        <td>{{$degreeLevel->requests->count()}}</td>
                        <td>{{$degreeLevel->students->count()}}</td>
                        <td><a class="button is-small is-gla" href="{{route('admin.degreelevels.edit', $degreeLevel->id)}}">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection