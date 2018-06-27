@extends('layouts.app')

@section('content')
<div class="columns is-centered">
    <div class="column is-three-quarters">
        <h3 class="title is-3">Add New Degree Level</h3>
        <form method="POST" action="{{route('admin.degreelevels.store')}}">
            {{ csrf_field() }}
            <div class="field">
                <label class="label">Degree Level</label>
                <div class="control">
                    <input name="title" placeholder="Degree Level" value="{{old('title')}}" type="text" class="input" required>
                </div>
            </div>
            <button class="button is-gla-success">Save</button>
        </form>
    </div>
</div>
@stop