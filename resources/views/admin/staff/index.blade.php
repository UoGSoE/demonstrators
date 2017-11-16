@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h3 class="title is-3">Staff Members</h3>
  </div>
</div>
@foreach ($staff as $staffmember)
    <staff-member :staffmember="{{ $staffmember->forVue() }}"></staff-member>
@endforeach
@endsection
@push('scripts')
<script>
  window.allcourses = @json($courses);
  window.staff = @json($staff);
</script>
@endpush