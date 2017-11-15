@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h3 class="title is-3">Staff Members</h3>
  </div>
</div>
@foreach ($staff as $staffmember)
    <staff-member :allcourses="{{ $courses }}" :staffmember="{{ $staffmember->forVue() }}" :staff="{{ $staff }}"></staff-member>
@endforeach
@endsection