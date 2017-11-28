@extends('layouts.app')

@section('content')
<div class="columns is-centered">
    <div class="column is-three-quarters">
        <h3 class="title is-3">Edit Course: {{$course->fullTitle}}
            <form class="is-inline" method="POST" action="{{route('admin.courses.destroy', $course->id)}}">
                {{ csrf_field() }}
                <button class="button is-gla-danger is-outlined is-pulled-right">
                    <span class="icon is-small"><i class="fa fa-trash" aria-hidden="true"></i></span>
                </button>
            </form>
        </h3>
        @if ($course->staff()->count() or $course->requests()->count())
            <div class="notification is-danger">
                Please be aware that you are editing a course that is assigned to a staff member or has active requests.
            </div>
        @endif
        <form method="POST" action="{{route('admin.courses.update', $course->id)}}">
            {{ csrf_field() }}
            <div class="field">
                <label class="label">Code</label>
                <div class="control">
                    <input name="code" placeholder="Code" value="{{$course->code}}" type="text" class="input" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Title</label>
                <div class="control">
                    <input name="title" placeholder="Title" value="{{$course->title}}" type="text" class="input" required>
                </div>
            </div>
            <button class="button is-gla-success">Update</button>
        </form>
    </div>
</div>
@stop