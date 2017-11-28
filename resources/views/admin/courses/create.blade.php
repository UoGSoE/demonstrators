@extends('layouts.app')

@section('content')
<div class="columns is-centered">
    <div class="column is-three-quarters">
        <h3 class="title is-3">Add New Course</h3>
        <form method="POST" action="{{route('admin.courses.store')}}">
            {{ csrf_field() }}
            <div class="field">
                <label class="label">Code</label>
                <div class="control">
                    <input name="code" placeholder="Code" value="{{old('code')}}" type="text" class="input" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Title</label>
                <div class="control">
                    <input name="title" placeholder="Title" value="{{old('title')}}" type="text" class="input" required>
                </div>
            </div>
            <button class="button is-gla-success">Save</button>
        </form>
    </div>
</div>
@stop