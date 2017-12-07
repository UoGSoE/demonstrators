@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h3 class="title is-3">Unaccepted Applications
        <a class="button is-gla is-outlined is-pulled-right" href="{{route('admin.reports.output7.download')}}">
            <span class="icon is-small">
                <i class="fa fa-download" aria-hidden="true"></i>
            </span>
        </a>
    </h3>
  </div>
</div>
<div class="columns is-centered">
    <div class="column is-three-quarters">
        @include('admin.reports.partials.output7_table')          
    </div>
</div>
@endsection