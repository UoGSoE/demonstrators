@extends('layouts.app')

@section('content')
<div class="columns is-centered">
    <div class="column is-three-quarters">
        <h3 class="title is-3">Edit Degree Level: {{$degreeLevel->title}}
            <form class="is-inline" method="POST" action="{{route('admin.degreelevels.destroy', $degreeLevel->id)}}">
                {{ csrf_field() }}
                <button class="button is-gla-danger is-outlined is-pulled-right">
                    <span class="icon is-small"><i class="fa fa-trash" aria-hidden="true"></i></span>
                </button>
            </form>
        </h3>
        @if ($degreeLevel->requests()->count())
            <div class="notification is-danger">
                Please be aware that you are editing a degree level that is assigned to a demonstrator request or student.
            </div>
        @endif
        <form method="POST" action="{{route('admin.degreelevels.update', $degreeLevel->id)}}">
            {{ csrf_field() }}
            <div class="field">
                <label class="label">Title</label>
                <div class="control">
                    <input name="title" placeholder="Title" value="{{$degreeLevel->title}}" type="text" class="input" required>
                </div>
            </div>
            <button class="button is-gla-success">Update</button>
        </form>
    </div>
</div>
@stop