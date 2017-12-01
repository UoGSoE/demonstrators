@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h3 class="title is-3">Accepted Students</h3>
  </div>
</div>
<div class="columns is-centered">
    <div class="column is-three-quarters">
        @include('admin.reports.partials.output3_table')
    </div>
</div>
@endsection