@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h3 class="title is-3">Staff Members
      <a class="button is-gla-success is-outlined is-pulled-right add-staff" href="{{route('admin.staff.create')}}">
        <span class="icon is-small">
          <i class="fa fa-plus-square" aria-hidden="true"></i>
        </span>
      </a>
    </h3>
  </div>
</div>
@foreach ($staff as $staffmember)
    <staff-member :allcourses="{{ $courses }}" :staffmember="{{ $staffmember->forVue() }}"></staff-member>
@endforeach
@endsection