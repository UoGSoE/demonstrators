@extends('layouts.app')

@section('content')
<div class="columns is-centered">
  <div class="column is-three-quarters">
    <h3 class="title is-3">Requests With Unseen Applications (Older Than 3 Days)
      <a class="button is-gla is-outlined is-pulled-right" href="{{route('admin.reports.output6.download')}}">
        <span class="icon is-small">
          <i class="fa fa-download" aria-hidden="true"></i>
        </span>
      </a>
    </h3>
  </div>
</div>
<div class="columns is-centered">
    <div class="column is-three-quarters">
        @include('admin.reports.partials.output6_table')                                
    </div>
</div>
@endsection